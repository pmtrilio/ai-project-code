import ipaddress
import math
import numbers
import typing
import uuid
from collections.abc import Mapping as _Mapping
from enum import Enum as EnumType

try:
    from typing import Unpack
except ImportError:  # Remove when dropping Python 3.10
    from typing_extensions import Unpack

# Remove when dropping Python 3.10
try:
    from backports.datetime_fromisoformat import MonkeyPatch
except ImportError:
    pass
else:
    MonkeyPatch.patch_fromisoformat()

from marshmallow import class_registry, types, utils, validate
from marshmallow.constants import missing as missing_
from marshmallow.exceptions import (
    StringNotCollectionError,
    ValidationError,
    _FieldInstanceResolutionError,
)
from marshmallow.validate import And, Length

if typing.TYPE_CHECKING:
    from marshmallow.schema import Schema, SchemaMeta


__all__ = [
    "IP",
    "URL",
    "UUID",
    "AwareDateTime",
    "Bool",
    "Boolean",
    "Constant",
    "Date",
    "DateTime",
    "Decimal",
    "Dict",
    "Email",
    "Enum",
    "Field",
    "Float",
    "Function",
    "IPInterface",
    "IPv4",
    "IPv4Interface",
    "IPv6",
    "IPv6Interface",
    "Int",
    "Integer",
    "List",
    "Mapping",
    "Method",
    "NaiveDateTime",
    "Nested",
    "Number",
    "Pluck",
    "Raw",
    "Str",
    "String",
    "Time",
    "TimeDelta",
    "Tuple",
    "Url",
]

_InternalT = typing.TypeVar("_InternalT")


class _BaseFieldKwargs(typing.TypedDict, total=False):
    load_default: typing.Any
    dump_default: typing.Any
    data_key: str | None
    attribute: str | None
    validate: types.Validator | typing.Iterable[types.Validator] | None
    required: bool
    allow_none: bool | None
    load_only: bool
    dump_only: bool
    error_messages: dict[str, str] | None
    metadata: typing.Mapping[str, typing.Any] | None


def _resolve_field_instance(cls_or_instance: Field | type[Field]) -> Field:
    """Return a Field instance from a Field class or instance.

    :param cls_or_instance: Field class or instance.
    """
    if isinstance(cls_or_instance, type):
        if not issubclass(cls_or_instance, Field):
            raise _FieldInstanceResolutionError
        return cls_or_instance()
    if not isinstance(cls_or_instance, Field):