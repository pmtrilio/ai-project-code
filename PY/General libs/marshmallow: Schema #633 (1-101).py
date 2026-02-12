"""The `Schema <marshmallow.Schema>` class, including its metaclass and options (`class Meta <marshmallow.Schema.Meta>`)."""

# ruff: noqa: SLF001
from __future__ import annotations

import copy
import datetime as dt
import decimal
import functools
import inspect
import json
import operator
import typing
import uuid
from abc import ABCMeta
from collections import defaultdict
from collections.abc import Mapping, Sequence
from itertools import zip_longest

from marshmallow import class_registry, types
from marshmallow import fields as ma_fields
from marshmallow.constants import EXCLUDE, INCLUDE, RAISE, missing
from marshmallow.decorators import (
    POST_DUMP,
    POST_LOAD,
    PRE_DUMP,
    PRE_LOAD,
    VALIDATES,
    VALIDATES_SCHEMA,
)
from marshmallow.error_store import ErrorStore
from marshmallow.exceptions import SCHEMA, StringNotCollectionError, ValidationError
from marshmallow.orderedset import OrderedSet
from marshmallow.utils import (
    get_value,
    is_collection,
    is_sequence_but_not_string,
    set_value,
)

if typing.TYPE_CHECKING:
    from marshmallow.fields import Field


def _get_fields(attrs) -> list[tuple[str, Field]]:
    """Get fields from a class

    :param attrs: Mapping of class attributes
    """
    ret = []
    for field_name, field_value in attrs.items():
        if isinstance(field_value, type) and issubclass(field_value, ma_fields.Field):
            raise TypeError(
                f'Field for "{field_name}" must be declared as a '
                "Field instance, not a class. "
                f'Did you mean "fields.{field_value.__name__}()"?'
            )
        if isinstance(field_value, ma_fields.Field):
            ret.append((field_name, field_value))
    return ret


# This function allows Schemas to inherit from non-Schema classes and ensures
#   inheritance according to the MRO
def _get_fields_by_mro(klass: SchemaMeta):
    """Collect fields from a class, following its method resolution order. The
    class itself is excluded from the search; only its parents are checked. Get
    fields from ``_declared_fields`` if available, else use ``__dict__``.

    :param klass: Class whose fields to retrieve
    """
    mro = inspect.getmro(klass)
    # Combine fields from all parents
    # functools.reduce(operator.iadd, list_of_lists) is faster than sum(list_of_lists, [])
    # Loop over mro in reverse to maintain correct order of fields
    return functools.reduce(
        operator.iadd,
        (
            _get_fields(
                getattr(base, "_declared_fields", base.__dict__),
            )
            for base in mro[:0:-1]
        ),
        [],
    )


class SchemaMeta(ABCMeta):
    """Metaclass for the Schema class. Binds the declared fields to
    a ``_declared_fields`` attribute, which is a dictionary mapping attribute
    names to field objects. Also sets the ``opts`` class attribute, which is
    the Schema class's `class Meta <marshmallow.Schema.Meta>` options.
    """

    Meta: type
    opts: typing.Any
    OPTIONS_CLASS: type
    _declared_fields: dict[str, Field]

    def __new__(
        mcs,