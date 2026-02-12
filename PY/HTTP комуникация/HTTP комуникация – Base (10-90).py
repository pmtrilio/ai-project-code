import django
from django.apps import apps
from django.conf import settings
from django.core import checks
from django.core.exceptions import (
    NON_FIELD_ERRORS,
    FieldDoesNotExist,
    FieldError,
    MultipleObjectsReturned,
    ObjectDoesNotExist,
    ObjectNotUpdated,
    ValidationError,
)
from django.db import (
    DJANGO_VERSION_PICKLE_KEY,
    DatabaseError,
    connection,
    connections,
    router,
    transaction,
)
from django.db.models import NOT_PROVIDED, ExpressionWrapper, IntegerField, Max, Value
from django.db.models.constants import LOOKUP_SEP
from django.db.models.deletion import CASCADE, DO_NOTHING, Collector, DatabaseOnDelete
from django.db.models.expressions import DatabaseDefault
from django.db.models.fetch_modes import FETCH_ONE
from django.db.models.fields.composite import CompositePrimaryKey
from django.db.models.fields.related import (
    ForeignObjectRel,
    OneToOneField,
    lazy_related_operation,
    resolve_relation,
)
from django.db.models.functions import Coalesce
from django.db.models.manager import Manager
from django.db.models.options import Options
from django.db.models.query import F, Q
from django.db.models.signals import (
    class_prepared,
    post_init,
    post_save,
    pre_init,
    pre_save,
)
from django.db.models.utils import AltersData, make_model_tuple
from django.utils.encoding import force_str
from django.utils.hashable import make_hashable
from django.utils.text import capfirst, get_text_list
from django.utils.translation import gettext_lazy as _


class Deferred:
    def __repr__(self):
        return "<Deferred field>"

    def __str__(self):
        return "<Deferred field>"


DEFERRED = Deferred()


def subclass_exception(name, bases, module, attached_to):
    """
    Create exception subclass. Used by ModelBase below.

    The exception is created in a way that allows it to be pickled, assuming
    that the returned exception class will be added as an attribute to the
    'attached_to' class.
    """
    return type(
        name,
        bases,
        {
            "__module__": module,
            "__qualname__": "%s.%s" % (attached_to.__qualname__, name),
        },
    )


def _has_contribute_to_class(value):