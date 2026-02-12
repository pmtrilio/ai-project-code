"""Defining fields on models."""

from __future__ import annotations as _annotations

import dataclasses
import inspect
import re
import sys
from collections.abc import Callable, Mapping
from copy import copy
from dataclasses import Field as DataclassField
from functools import cached_property
from typing import TYPE_CHECKING, Annotated, Any, ClassVar, Literal, TypeVar, cast, final, overload
from warnings import warn

import annotated_types
import typing_extensions
from pydantic_core import MISSING, PydanticUndefined
from typing_extensions import Self, TypeAlias, TypedDict, Unpack, deprecated
from typing_inspection import typing_objects
from typing_inspection.introspection import UNKNOWN, AnnotationSource, ForbiddenQualifier, Qualifier, inspect_annotation

from . import types
from ._internal import _decorators, _fields, _generics, _internal_dataclass, _repr, _typing_extra, _utils
from ._internal._namespace_utils import GlobalsNamespace, MappingNamespace
from .aliases import AliasChoices, AliasGenerator, AliasPath
from .config import JsonDict
from .errors import PydanticForbiddenQualifier, PydanticUserError
from .json_schema import PydanticJsonSchemaWarning
from .warnings import PydanticDeprecatedSince20

if TYPE_CHECKING:
    from ._internal._config import ConfigWrapper
    from ._internal._repr import ReprArgs


__all__ = 'Field', 'FieldInfo', 'PrivateAttr', 'computed_field'


_Unset: Any = PydanticUndefined
