    Mapping,
    MutableMapping,
    Sequence,
)
from contextlib import AbstractAsyncContextManager, asynccontextmanager
from functools import lru_cache, partial, update_wrapper
from typing import Any, TypeVar, cast, final, overload

from aiosignal import Signal
from frozenlist import FrozenList

from . import hdrs
from .helpers import AppKey
from .log import web_logger
from .typedefs import Handler, Middleware
from .web_exceptions import NotAppKeyWarning
from .web_middlewares import _fix_request_current_app
from .web_request import Request
from .web_response import StreamResponse
from .web_routedef import AbstractRouteDef
from .web_urldispatcher import (
    AbstractResource,
    AbstractRoute,
    Domain,
    MaskDomain,
    MatchedSubAppResource,
    PrefixedSubAppResource,
    SystemRoute,
    UrlDispatcher,
)

__all__ = ("Application", "CleanupError")

_AppSignal = Signal["Application"]
_RespPrepareSignal = Signal[Request, StreamResponse]
_Middlewares = FrozenList[Middleware]
_MiddlewaresHandlers = Sequence[Middleware]
_Subapps = list["Application"]

_T = TypeVar("_T")
_U = TypeVar("_U")
_Resource = TypeVar("_Resource", bound=AbstractResource)


def _build_middlewares(
    handler: Handler, apps: tuple["Application", ...]
) -> Callable[[Request], Awaitable[StreamResponse]]:
    """Apply middlewares to handler."""
    # The slice is to reverse the order of the apps
    # so they are applied in the order they were added
    for app in apps[::-1]:
        assert app.pre_frozen, "middleware handlers are not ready"
        for m in app._middlewares_handlers:
            handler = update_wrapper(partial(m, handler=handler), handler)
    return handler


_cached_build_middleware = lru_cache(maxsize=1024)(_build_middlewares)


@final