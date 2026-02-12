from starlette.requests import Request
from starlette.responses import Response
from starlette.routing import BaseRoute, Router
from starlette.types import ASGIApp, ExceptionHandler, Lifespan, Receive, Scope, Send

AppType = TypeVar("AppType", bound="Starlette")
P = ParamSpec("P")


class Starlette:
    """Creates an Starlette application."""

    def __init__(
        self: AppType,
        debug: bool = False,
        routes: Sequence[BaseRoute] | None = None,
        middleware: Sequence[Middleware] | None = None,
        exception_handlers: Mapping[Any, ExceptionHandler] | None = None,
        lifespan: Lifespan[AppType] | None = None,
    ) -> None:
        """Initializes the application.

        Parameters:
            debug: Boolean indicating if debug tracebacks should be returned on errors.
            routes: A list of routes to serve incoming HTTP and WebSocket requests.
            middleware: A list of middleware to run for every request. A starlette
                application will always automatically include two middleware classes.
                `ServerErrorMiddleware` is added as the very outermost middleware, to handle
                any uncaught errors occurring anywhere in the entire stack.
                `ExceptionMiddleware` is added as the very innermost middleware, to deal
                with handled exception cases occurring in the routing or endpoints.
            exception_handlers: A mapping of either integer status codes,
                or exception class types onto callables which handle the exceptions.
                Exception handler callables should be of the form
                `handler(request, exc) -> response` and may be either standard functions, or
                async functions.
            lifespan: A lifespan context function, which can be used to perform
                startup and shutdown tasks. This is a newer style that replaces the
                `on_startup` and `on_shutdown` handlers. Use one or the other, not both.
        """
        self.debug = debug