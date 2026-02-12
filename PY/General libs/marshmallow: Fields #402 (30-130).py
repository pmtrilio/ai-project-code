
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
        raise _FieldInstanceResolutionError
    return cls_or_instance


class Field(typing.Generic[_InternalT]):
    """Base field from which all other fields inherit.
    This class should not be used directly within Schemas.

    :param dump_default: If set, this value will be used during serialization if the
        input value is missing. If not set, the field will be excluded from the
        serialized output if the input value is missing. May be a value or a callable.
    :param load_default: Default deserialization value for the field if the field is not
        found in the input data. May be a value or a callable.
    :param data_key: The name of the dict key in the external representation, i.e.
        the input of `load` and the output of `dump`.
        If `None`, the key will match the name of the field.
    :param attribute: The name of the key/attribute in the internal representation, i.e.
        the output of `load` and the input of `dump`.
        If `None`, the key/attribute will match the name of the field.
        Note: This should only be used for very specific use cases such as