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