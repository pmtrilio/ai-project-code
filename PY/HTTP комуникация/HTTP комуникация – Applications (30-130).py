from fastapi.types import DecoratedCallable, IncEx
from fastapi.utils import generate_unique_id
from starlette.applications import Starlette
from starlette.datastructures import State
from starlette.exceptions import HTTPException
from starlette.middleware import Middleware
from starlette.middleware.base import BaseHTTPMiddleware
from starlette.middleware.errors import ServerErrorMiddleware
from starlette.middleware.exceptions import ExceptionMiddleware
from starlette.requests import Request
from starlette.responses import HTMLResponse, JSONResponse, Response
from starlette.routing import BaseRoute
from starlette.types import ASGIApp, ExceptionHandler, Lifespan, Receive, Scope, Send
from typing_extensions import deprecated

AppType = TypeVar("AppType", bound="FastAPI")


class FastAPI(Starlette):
    """
    `FastAPI` app class, the main entrypoint to use FastAPI.

    Read more in the
    [FastAPI docs for First Steps](https://fastapi.tiangolo.com/tutorial/first-steps/).

    ## Example

    ```python
    from fastapi import FastAPI

    app = FastAPI()
    ```
    """

    def __init__(
        self: AppType,
        *,
        debug: Annotated[
            bool,
            Doc(
                """
                Boolean indicating if debug tracebacks should be returned on server
                errors.

                Read more in the
                [Starlette docs for Applications](https://www.starlette.dev/applications/#instantiating-the-application).
                """
            ),
        ] = False,
        routes: Annotated[
            Optional[list[BaseRoute]],
            Doc(
                """
                **Note**: you probably shouldn't use this parameter, it is inherited
                from Starlette and supported for compatibility.

                ---

                A list of routes to serve incoming HTTP and WebSocket requests.
                """
            ),
            deprecated(
                """
                You normally wouldn't use this parameter with FastAPI, it is inherited
                from Starlette and supported for compatibility.

                In FastAPI, you normally would use the *path operation methods*,
                like `app.get()`, `app.post()`, etc.
                """
            ),
        ] = None,
        title: Annotated[
            str,
            Doc(
                """
                The title of the API.

                It will be added to the generated OpenAPI (e.g. visible at `/docs`).

                Read more in the
                [FastAPI docs for Metadata and Docs URLs](https://fastapi.tiangolo.com/tutorial/metadata/#metadata-for-api).

                **Example**

                ```python
                from fastapi import FastAPI

                app = FastAPI(title="ChimichangApp")
                ```
                """
            ),
        ] = "FastAPI",
        summary: Annotated[
            Optional[str],
            Doc(
                """
                A short summary of the API.

                It will be added to the generated OpenAPI (e.g. visible at `/docs`).

                Read more in the