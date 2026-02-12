
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