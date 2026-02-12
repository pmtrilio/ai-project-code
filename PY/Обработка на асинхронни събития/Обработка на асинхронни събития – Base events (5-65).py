which wraps a multiplexer with functionality for scheduling callbacks,
immediately or at a given time in the future.

Whenever a public API takes a callback, subsequent positional
arguments will be passed to the callback if/when it is called.  This
avoids the proliferation of trivial lambdas implementing closures.
Keyword arguments for the callback are not supported; this is a
conscious design decision, leaving the door open for keyword arguments
to modify the meaning of the API call itself.
"""

import collections
import collections.abc
import concurrent.futures
import errno
import heapq
import itertools
import os
import socket
import stat
import subprocess
import threading
import time
import traceback
import sys
import warnings
import weakref

try:
    import ssl
except ImportError:  # pragma: no cover
    ssl = None

from . import constants
from . import coroutines
from . import events
from . import exceptions
from . import futures
from . import protocols
from . import sslproto
from . import staggered
from . import tasks
from . import timeouts
from . import transports
from . import trsock
from .log import logger


__all__ = 'BaseEventLoop','Server',


# Minimum number of _scheduled timer handles before cleanup of
# cancelled handles is performed.
_MIN_SCHEDULED_TIMER_HANDLES = 100

# Minimum fraction of _scheduled timer handles that are cancelled
# before cleanup of cancelled handles is performed.
_MIN_CANCELLED_TIMER_HANDLES_FRACTION = 0.5


_HAS_IPv6 = hasattr(socket, 'AF_INET6')