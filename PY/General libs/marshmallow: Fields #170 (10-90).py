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