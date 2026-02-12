    'FIRST_COMPLETED', 'FIRST_EXCEPTION', 'ALL_COMPLETED',
    'wait', 'wait_for', 'as_completed', 'sleep',
    'gather', 'shield', 'ensure_future', 'run_coroutine_threadsafe',
    'current_task', 'all_tasks',
    'create_eager_task_factory', 'eager_task_factory',
    '_register_task', '_unregister_task', '_enter_task', '_leave_task',
)

import concurrent.futures
import contextvars
import functools
import inspect
import itertools
import math
import types
import weakref
from types import GenericAlias

from . import base_tasks
from . import coroutines
from . import events
from . import exceptions
from . import futures
from . import queues
from . import timeouts

# Helper to generate new task names
# This uses itertools.count() instead of a "+= 1" operation because the latter
# is not thread safe. See bpo-11866 for a longer explanation.
_task_name_counter = itertools.count(1).__next__


def current_task(loop=None):
    """Return a currently executed task."""
    if loop is None:
        loop = events.get_running_loop()
    return _current_tasks.get(loop)


def all_tasks(loop=None):
    """Return a set of all tasks for the loop."""