from kombu.transport.native_delayed_delivery import calculate_routing_key
from kombu.utils.compat import register_after_fork
from kombu.utils.objects import cached_property
from kombu.utils.uuid import uuid
from vine import starpromise

from celery import platforms, signals
from celery._state import (_announce_app_finalized, _deregister_app, _register_app, _set_current_app, _task_stack,
                           connect_on_app_finalize, get_current_app, get_current_worker_task, set_default_app)
from celery.exceptions import AlwaysEagerIgnored, ImproperlyConfigured
from celery.loaders import get_loader_cls
from celery.local import PromiseProxy, maybe_evaluate
from celery.utils import abstract
from celery.utils.collections import AttributeDictMixin
from celery.utils.dispatch import Signal
from celery.utils.functional import first, head_from_fun, maybe_list
from celery.utils.imports import gen_task_name, instantiate, symbol_by_name
from celery.utils.log import get_logger
from celery.utils.objects import FallbackContext, mro_lookup
from celery.utils.time import maybe_make_aware, timezone, to_utc

from ..utils.annotations import annotation_is_class, annotation_issubclass, get_optional_arg
from ..utils.quorum_queues import detect_quorum_queues
# Load all builtin tasks
from . import backends, builtins  # noqa
from .annotations import prepare as prepare_annotations
from .autoretry import add_autoretry_behaviour
from .defaults import DEFAULT_SECURITY_DIGEST, find_deprecated_settings
from .registry import TaskRegistry
from .utils import (AppPickler, Settings, _new_key_to_old, _old_key_to_new, _unpickle_app, _unpickle_app_v2, appstr,
                    bugreport, detect_settings)

if typing.TYPE_CHECKING:  # pragma: no cover  # codecov does not capture this
    # flake8 marks the BaseModel import as unused, because the actual typehint is quoted.
    from pydantic import BaseModel  # noqa: F401

__all__ = ('Celery',)

logger = get_logger(__name__)

BUILTIN_FIXUPS = {