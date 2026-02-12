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
    RunFinishedError,
    TrioInternalError,
)
from ._instrumentation import Instruments
from ._ki import KIManager, enable_ki_protection
from ._parking_lot import GLOBAL_PARKING_LOT_BREAKER
from ._run_context import GLOBAL_RUN_CONTEXT as GLOBAL_RUN_CONTEXT
from ._thread_cache import start_thread_soon
from ._traps import (
    Abort,
    CancelShieldedCheckpoint,
    PermanentlyDetachCoroutineObject,
    WaitTaskRescheduled,
    cancel_shielded_checkpoint,
    wait_task_rescheduled,
)

if sys.version_info < (3, 11):
    from exceptiongroup import BaseExceptionGroup


if TYPE_CHECKING:
    import contextvars
    import types
    from collections.abc import (
        Awaitable,
        Callable,
        Generator,
        Iterator,
        Sequence,
    )
    from types import TracebackType

    # for some strange reason Sphinx works with outcome.Outcome, but not Outcome, in
    # start_guest_run. Same with types.FrameType in iter_await_frames
    import outcome
    from typing_extensions import Self, TypeVar, TypeVarTuple, Unpack

    PosArgT = TypeVarTuple("PosArgT")
    StatusT = TypeVar("StatusT", default=None)
    StatusT_contra = TypeVar("StatusT_contra", contravariant=True, default=None)
    BaseExcT = TypeVar("BaseExcT", bound=BaseException)
else:
    from typing import TypeVar

    StatusT = TypeVar("StatusT")
    StatusT_contra = TypeVar("StatusT_contra", contravariant=True)

RetT = TypeVar("RetT")


DEADLINE_HEAP_MIN_PRUNE_THRESHOLD: Final = 1000

# Passed as a sentinel
_NO_SEND: Final[Outcome[object]] = cast("Outcome[object]", object())

# Used to track if an exceptiongroup can be collapsed
NONSTRICT_EXCEPTIONGROUP_NOTE = 'This is a "loose" ExceptionGroup, and may be collapsed by Trio if it only contains one exception - typically after `Cancelled` has been stripped from it. Note this has consequences for exception handling, and strict_exception_groups=True is recommended.'


@final
class _NoStatus(metaclass=NoPublicConstructor):
    """Sentinel for unset TaskStatus._value."""


# Decorator to mark methods public. This does nothing by itself, but
# trio/_tools/gen_exports.py looks for it.
def _public(fn: RetT) -> RetT:
    return fn