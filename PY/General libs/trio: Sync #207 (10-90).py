from . import _core
from ._core import (
    Abort,
    ParkingLot,
    RaiseCancelT,
    add_parking_lot_breaker,
    enable_ki_protection,
    remove_parking_lot_breaker,
)
from ._deprecate import warn_deprecated
from ._util import final

if TYPE_CHECKING:
    from collections.abc import Callable
    from types import TracebackType

    from typing_extensions import deprecated

    from ._core import Task
    from ._core._parking_lot import ParkingLotStatistics
else:
    T = TypeVar("T")

    def deprecated(
        message: str,
        /,
        *,
        category: type[Warning] | None = DeprecationWarning,
        stacklevel: int = 1,
    ) -> Callable[[T], T]:
        def wrapper(f: T) -> T:
            return f

        return wrapper


@attrs.frozen
class EventStatistics:
    """An object containing debugging information.

    Currently the following fields are defined:

    * ``tasks_waiting``: The number of tasks blocked on this event's
      :meth:`trio.Event.wait` method.

    """

    tasks_waiting: int


@final
@attrs.define(repr=False, eq=False)
class Event:
    """A waitable boolean value useful for inter-task synchronization,
    inspired by :class:`threading.Event`.

    An event object has an internal boolean flag, representing whether
    the event has happened yet. The flag is initially False, and the
    :meth:`wait` method waits until the flag is True. If the flag is
    already True, then :meth:`wait` returns immediately. (If the event has
    already happened, there's nothing to wait for.) The :meth:`set` method
    sets the flag to True, and wakes up any waiters.

    This behavior is useful because it helps avoid race conditions and
    lost wakeups: it doesn't matter whether :meth:`set` gets called just
    before or after :meth:`wait`. If you want a lower-level wakeup
    primitive that doesn't have this protection, consider :class:`Condition`
    or :class:`trio.lowlevel.ParkingLot`.

    .. note:: Unlike `threading.Event`, `trio.Event` has no
       `~threading.Event.clear` method. In Trio, once an `Event` has happened,
       it cannot un-happen. If you need to represent a series of events,
       consider creating a new `Event` object for each one (they're cheap!),
       or other synchronization methods like :ref:`channels <channels>` or
       `trio.lowlevel.ParkingLot`.

    """

    _tasks: set[Task] = attrs.field(factory=set, init=False)
    _flag: bool = attrs.field(default=False, init=False)
