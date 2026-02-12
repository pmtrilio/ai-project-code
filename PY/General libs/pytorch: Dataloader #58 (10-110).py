
import contextlib
import functools
import itertools
import logging
import multiprocessing as python_multiprocessing
import os
import queue
import threading
import warnings
from collections.abc import Callable
from typing import Any, Generic, NoReturn, TYPE_CHECKING, TypeVar
from typing_extensions import Self

import torch
import torch.distributed as dist
import torch.utils.data.graph_settings
from torch._utils import ExceptionWrapper
from torch.utils.data import _utils
from torch.utils.data.datapipes.datapipe import (
    _IterDataPipeSerializationWrapper,
    _MapDataPipeSerializationWrapper,
    IterDataPipe,
    MapDataPipe,
)
from torch.utils.data.dataset import Dataset, IterableDataset
from torch.utils.data.sampler import (
    BatchSampler,
    RandomSampler,
    Sampler,
    SequentialSampler,
)


if TYPE_CHECKING:
    from collections.abc import Iterable

__all__ = [
    "DataLoader",
    "get_worker_info",
    "default_collate",
    "default_convert",
]


_T = TypeVar("_T")
_T_co = TypeVar("_T_co", covariant=True)
_worker_init_fn_t = Callable[[int], None]

# Ideally we would parameterize `DataLoader` by the return type of `collate_fn`, but there is currently no way to have that
# type parameter set to a default value if the user doesn't pass in a custom 'collate_fn'.
# See https://github.com/python/mypy/issues/3737.
_collate_fn_t = Callable[[list[_T]], Any]


# These functions used to be defined in this file. However, it was moved to
# _utils/collate.py. Although it is rather hard to access this from user land
# (one has to explicitly directly `import torch.utils.data.dataloader`), there
# probably is user code out there using it. This aliasing maintains BC in this
# aspect.
default_collate: _collate_fn_t = _utils.collate.default_collate
default_convert = _utils.collate.default_convert

get_worker_info = _utils.worker.get_worker_info

logger = logging.getLogger(__name__)


class _DatasetKind:
    Map = 0
    Iterable = 1

    @staticmethod
    def create_fetcher(kind, dataset, auto_collation, collate_fn, drop_last):
        if kind == _DatasetKind.Map:
            return _utils.fetch._MapDatasetFetcher(
                dataset, auto_collation, collate_fn, drop_last
            )
        else:
            return _utils.fetch._IterableDatasetFetcher(
                dataset, auto_collation, collate_fn, drop_last
            )


class _InfiniteConstantSampler(Sampler):
    r"""Analogous to ``itertools.repeat(None, None)``.

    Used as sampler for :class:`~torch.utils.data.IterableDataset`.
    """

    def __iter__(self):
        while True:
            yield None


def _get_distributed_settings():
    if dist.is_available() and dist.is_initialized():
        return dist.get_world_size(), dist.get_rank()
    else:
        return 1, 0
