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