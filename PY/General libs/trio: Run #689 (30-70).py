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