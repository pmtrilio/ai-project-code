functions to be run in multiprocessing. E.g., the data loading worker loop is
in `./_utils/worker.py`.
"""

from __future__ import annotations

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