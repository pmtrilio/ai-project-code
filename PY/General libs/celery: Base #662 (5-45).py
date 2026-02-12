import os
import sys
import threading
import typing
import warnings
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