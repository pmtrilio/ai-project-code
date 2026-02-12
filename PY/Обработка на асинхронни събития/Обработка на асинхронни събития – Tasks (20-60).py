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
    if loop is None:
        loop = events.get_running_loop()
    # capturing the set of eager tasks first, so if an eager task "graduates"
    # to a regular task in another thread, we don't risk missing it.
    eager_tasks = list(_eager_tasks)

    return {t for t in itertools.chain(_scheduled_tasks, eager_tasks)
            if futures._get_loop(t) is loop and not t.done()}


class Task(futures._PyFuture):  # Inherit Python Task implementation
                                # from a Python Future implementation.

    """A coroutine wrapped in a Future."""
