# mypy: allow-untyped-defs

import functools
import inspect
import itertools
import warnings
import weakref
from collections import namedtuple, OrderedDict
from collections.abc import Callable, Iterator, Mapping
from typing import Any, Optional, overload, TypeVar, Union
from typing_extensions import Self

import torch
from torch import device, dtype, Tensor
from torch._prims_common import DeviceLikeType
from torch.nn.parameter import Buffer, Parameter
from torch.utils._python_dispatch import is_traceable_wrapper_subclass
from torch.utils.hooks import BackwardHook, RemovableHandle


__all__ = [
    "register_module_forward_pre_hook",
    "register_module_forward_hook",
    "register_module_full_backward_pre_hook",
    "register_module_backward_hook",
    "register_module_full_backward_hook",
    "register_module_buffer_registration_hook",
    "register_module_module_registration_hook",
    "register_module_parameter_registration_hook",
    "Module",
]

_grad_t = Union[tuple[Tensor, ...], Tensor]
# See https://mypy.readthedocs.io/en/latest/generics.html#generic-methods-and-generic-self for the use
# of `T` to annotate `self`. Many methods of `Module` return `self` and we want those return values to be
# the type of the subclass, not the looser type of `Module`.
T = TypeVar("T", bound="Module")


class _IncompatibleKeys(
    # pyrefly: ignore [invalid-inheritance]
    namedtuple("IncompatibleKeys", ["missing_keys", "unexpected_keys"]),
):
    __slots__ = ()

    def __repr__(self) -> str:
        # pyrefly: ignore [missing-attribute]
        if not self.missing_keys and not self.unexpected_keys:
            return "<All keys matched successfully>"
        return super().__repr__()

    __str__ = __repr__


def _addindent(s_, numSpaces):
    s = s_.split("\n")
    # don't do anything for single-line stuff
    if len(s) == 1:
        return s_
    first = s.pop(0)
    # Only add indentation to non-blank lines; blank lines stay empty