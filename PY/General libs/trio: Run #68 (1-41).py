from __future__ import annotations

import enum
import functools
import gc
import itertools
import random
import select
import sys
import warnings
from collections import deque
from contextlib import AbstractAsyncContextManager, contextmanager, suppress
from contextvars import copy_context
from heapq import heapify, heappop, heappush
from math import inf, isnan
from time import perf_counter
from typing import (
    TYPE_CHECKING,
    Any,
    Final,
    NoReturn,
    Protocol,
    cast,
    overload,
)

import attrs
from outcome import Error, Outcome, Value, capture
from sniffio import thread_local as sniffio_library
from sortedcontainers import SortedDict

from .. import _core
from .._abc import Clock, Instrument
from .._deprecate import warn_deprecated
from .._util import NoPublicConstructor, coroutine_or_error, final
from ._asyncgens import AsyncGenerators
from ._concat_tb import concat_tb
from ._entry_queue import EntryQueue, TrioToken
from ._exceptions import (
    Cancelled,
    CancelReasonLiteral,