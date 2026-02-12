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