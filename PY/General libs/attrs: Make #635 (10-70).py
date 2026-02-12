import itertools
import linecache
import sys
import types
import unicodedata
import weakref

from collections.abc import Callable, Mapping
from functools import cached_property
from typing import Any, NamedTuple, TypeVar

# We need to import _compat itself in addition to the _compat members to avoid
# having the thread-local in the globals here.
from . import _compat, _config, setters
from ._compat import (
    PY_3_10_PLUS,
    PY_3_11_PLUS,
    PY_3_13_PLUS,
    _AnnotationExtractor,
    _get_annotations,
    get_generic_base,
)
from .exceptions import (
    DefaultAlreadySetError,
    FrozenInstanceError,
    NotAnAttrsClassError,
    UnannotatedAttributeError,
)


# This is used at least twice, so cache it here.
_OBJ_SETATTR = object.__setattr__
_INIT_FACTORY_PAT = "__attr_factory_%s"
_CLASSVAR_PREFIXES = (
    "typing.ClassVar",
    "t.ClassVar",
    "ClassVar",
    "typing_extensions.ClassVar",
)
# we don't use a double-underscore prefix because that triggers
# name mangling when trying to create a slot for the field
# (when slots=True)
_HASH_CACHE_FIELD = "_attrs_cached_hash"

_EMPTY_METADATA_SINGLETON = types.MappingProxyType({})

# Unique object for unequivocal getattr() defaults.
_SENTINEL = object()

_DEFAULT_ON_SETATTR = setters.pipe(setters.convert, setters.validate)


class _Nothing(enum.Enum):
    """
    Sentinel to indicate the lack of a value when `None` is ambiguous.

    If extending attrs, you can use ``typing.Literal[NOTHING]`` to show
    that a value may be ``NOTHING``.

    .. versionchanged:: 21.1.0 ``bool(NOTHING)`` is now False.
    .. versionchanged:: 22.2.0 ``NOTHING`` is now an ``enum.Enum`` variant.