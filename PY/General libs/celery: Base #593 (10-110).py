from collections import UserDict, defaultdict, deque
from datetime import datetime
from datetime import timezone as datetime_timezone
from operator import attrgetter

from click.exceptions import Exit
from dateutil.parser import isoparse
from kombu import Exchange, pools
from kombu.clocks import LamportClock
from kombu.common import oid_from
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
    'celery.fixups.django:fixup',
}
USING_EXECV = os.environ.get('FORKED_BY_MULTIPROCESSING')

ERR_ENVVAR_NOT_SET = """
The environment variable {0!r} is not set,
and as such the configuration could not be loaded.

Please set this variable and make sure it points to
a valid configuration module.

Example:
    {0}="proj.celeryconfig"
"""


def app_has_custom(app, attr):
    """Return true if app has customized method `attr`.

    Note:
        This is used for optimizations in cases where we know
        how the default behavior works, but need to account
        for someone using inheritance to override a method/property.
    """
    return mro_lookup(app.__class__, attr, stop={Celery, object},
                      monkey_patched=[__name__])


def _unpickle_appattr(reverse_name, args):
    """Unpickle app."""
    # Given an attribute name and a list of args, gets
    # the attribute from the current app and calls it.
    return get_current_app()._rgetattr(reverse_name)(*args)


def _after_fork_cleanup_app(app):
    # This is used with multiprocessing.register_after_fork,
    # so need to be at module level.
    try:
        app._after_fork()
    except Exception as exc:  # pylint: disable=broad-except
        logger.info('after forker raised exception: %r', exc, exc_info=1)


def pydantic_wrapper(
    app: "Celery",
    task_fun: typing.Callable[..., typing.Any],
    task_name: str,
    strict: bool = True,
    context: typing.Optional[typing.Dict[str, typing.Any]] = None,