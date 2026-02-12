from itertools import chain, islice
from weakref import ref as weak_ref

from asgiref.sync import sync_to_async

import django
from django.conf import settings
from django.core import exceptions
from django.db import (
    DJANGO_VERSION_PICKLE_KEY,
    IntegrityError,
    NotSupportedError,
    connections,
    router,
    transaction,
)
from django.db.models import AutoField, DateField, DateTimeField, Field, Max, sql
from django.db.models.constants import LOOKUP_SEP, OnConflict
from django.db.models.deletion import Collector
from django.db.models.expressions import Case, DatabaseDefault, F, Value, When
from django.db.models.fetch_modes import FETCH_ONE
from django.db.models.functions import Cast, Trunc
from django.db.models.query_utils import FilteredRelation, Q
from django.db.models.sql.constants import GET_ITERATOR_CHUNK_SIZE, ROW_COUNT
from django.db.models.utils import (
    AltersData,
    create_namedtuple_class,
    resolve_callables,
)
from django.utils import timezone
from django.utils.deprecation import RemovedInDjango70Warning
from django.utils.functional import cached_property

# The maximum number of results to fetch in a get() query.
MAX_GET_RESULTS = 21

# The maximum number of items to display in a QuerySet.__repr__
REPR_OUTPUT_SIZE = 20

PROHIBITED_FILTER_KWARGS = frozenset(["_connector", "_negated"])
