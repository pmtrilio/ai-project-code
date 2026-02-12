|          | <=> | Work Items | <=> |        | <=  | Result Q  | <= |         |
|          |     +------------+     |        |     +-----------+    |         |
|          |     | 6: call()  |     |        |     | ...       |    |         |
|          |     |    future  |     |        |     | 4, result |    |         |
|          |     | ...        |     |        |     | 3, except |    |         |
+----------+     +------------+     +--------+     +-----------+    +---------+

Executor.submit() called:
- creates a uniquely numbered _WorkItem and adds it to the "Work Items" dict
- adds the id of the _WorkItem to the "Work Ids" queue

Local worker thread:
- reads work ids from the "Work Ids" queue and looks up the corresponding
  WorkItem from the "Work Items" dict: if the work item has been cancelled then
  it is simply removed from the dict, otherwise it is repackaged as a
  _CallItem and put in the "Call Q". New _CallItems are put in the "Call Q"
  until "Call Q" is full. NOTE: the size of the "Call Q" is kept small because
  calls placed in the "Call Q" can no longer be cancelled with Future.cancel().
- reads _ResultItems from "Result Q", updates the future stored in the
  "Work Items" dict and deletes the dict entry

Process #1..n:
- reads _CallItems from "Call Q", executes the calls, and puts the resulting
  _ResultItems in "Result Q"
"""

__author__ = 'Brian Quinlan (brian@sweetapp.com)'

import os
from concurrent.futures import _base
import queue
import multiprocessing as mp
# This import is required to load the multiprocessing.connection submodule
# so that it can be accessed later as `mp.connection`
import multiprocessing.connection
from multiprocessing.queues import Queue
import threading
import weakref
from functools import partial
import itertools
import sys
from traceback import format_exception


_threads_wakeups = weakref.WeakKeyDictionary()
_global_shutdown = False


class _ThreadWakeup:
    def __init__(self):
        self._closed = False
        self._lock = threading.Lock()
        self._reader, self._writer = mp.Pipe(duplex=False)

    def close(self):
        # Please note that we do not take the self._lock when
        # calling clear() (to avoid deadlocking) so this method can
        # only be called safely from the same thread as all calls to
        # clear() even if you hold the lock. Otherwise we
        # might try to read from the closed pipe.
        with self._lock:
            if not self._closed:
                self._closed = True
                self._writer.close()
                self._reader.close()

    def wakeup(self):
        with self._lock:
            if not self._closed:
                self._writer.send_bytes(b"")

    def clear(self):
        if self._closed:
            raise RuntimeError('operation on closed _ThreadWakeup')
        while self._reader.poll():
            self._reader.recv_bytes()


def _python_exit():
    global _global_shutdown
    _global_shutdown = True
    items = list(_threads_wakeups.items())
    for _, thread_wakeup in items:
        # call not protected by ProcessPoolExecutor._shutdown_lock
        thread_wakeup.wakeup()
    for t, _ in items:
        t.join()

# Register for `_python_exit()` to be called just before joining all
# non-daemon threads. This is used instead of `atexit.register()` for
# compatibility with subinterpreters, which no longer support daemon threads.
# See bpo-39812 for context.
threading._register_atexit(_python_exit)

# Controls how many more calls than processes will be queued in the call queue.
# A smaller number will mean that processes spend more time idle waiting for
# work while a larger number will make Future.cancel() succeed less frequently
# (Futures in the call queue cannot be cancelled).
EXTRA_QUEUED_CALLS = 1

