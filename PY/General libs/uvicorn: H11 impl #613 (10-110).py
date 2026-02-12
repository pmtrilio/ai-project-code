
import h11
from h11._connection import DEFAULT_MAX_INCOMPLETE_EVENT_SIZE

from uvicorn._types import (
    ASGI3Application,
    ASGIReceiveEvent,
    ASGISendEvent,
    HTTPRequestEvent,
    HTTPResponseBodyEvent,
    HTTPResponseStartEvent,
    HTTPScope,
)
from uvicorn.config import Config
from uvicorn.logging import TRACE_LOG_LEVEL
from uvicorn.protocols.http.flow_control import CLOSE_HEADER, HIGH_WATER_LIMIT, FlowControl, service_unavailable
from uvicorn.protocols.utils import get_client_addr, get_local_addr, get_path_with_query_string, get_remote_addr, is_ssl
from uvicorn.server import ServerState


def _get_status_phrase(status_code: int) -> bytes:
    try:
        return http.HTTPStatus(status_code).phrase.encode()
    except ValueError:
        return b""


STATUS_PHRASES = {status_code: _get_status_phrase(status_code) for status_code in range(100, 600)}


class H11Protocol(asyncio.Protocol):
    def __init__(
        self,
        config: Config,
        server_state: ServerState,
        app_state: dict[str, Any],
        _loop: asyncio.AbstractEventLoop | None = None,
    ) -> None:
        if not config.loaded:
            config.load()

        self.config = config
        self.app = config.loaded_app
        self.loop = _loop or asyncio.get_event_loop()
        self.logger = logging.getLogger("uvicorn.error")
        self.access_logger = logging.getLogger("uvicorn.access")
        self.access_log = self.access_logger.hasHandlers()
        self.conn = h11.Connection(
            h11.SERVER,
            config.h11_max_incomplete_event_size
            if config.h11_max_incomplete_event_size is not None
            else DEFAULT_MAX_INCOMPLETE_EVENT_SIZE,
        )
        self.ws_protocol_class = config.ws_protocol_class
        self.root_path = config.root_path
        self.limit_concurrency = config.limit_concurrency
        self.app_state = app_state

        # Timeouts
        self.timeout_keep_alive_task: asyncio.TimerHandle | None = None
        self.timeout_keep_alive = config.timeout_keep_alive

        # Shared server state
        self.server_state = server_state
        self.connections = server_state.connections
        self.tasks = server_state.tasks

        # Per-connection state
        self.transport: asyncio.Transport = None  # type: ignore[assignment]
        self.flow: FlowControl = None  # type: ignore[assignment]
        self.server: tuple[str, int] | None = None
        self.client: tuple[str, int] | None = None
        self.scheme: Literal["http", "https"] | None = None

        # Per-request state
        self.scope: HTTPScope = None  # type: ignore[assignment]
        self.headers: list[tuple[bytes, bytes]] = None  # type: ignore[assignment]
        self.cycle: RequestResponseCycle = None  # type: ignore[assignment]

    # Protocol interface
    def connection_made(  # type: ignore[override]
        self, transport: asyncio.Transport
    ) -> None:
        self.connections.add(self)

        self.transport = transport
        self.flow = FlowControl(transport)
        self.server = get_local_addr(transport)
        self.client = get_remote_addr(transport)
        self.scheme = "https" if is_ssl(transport) else "http"

        if self.logger.level <= TRACE_LOG_LEVEL:
            prefix = "%s:%d - " % self.client if self.client else ""
            self.logger.log(TRACE_LOG_LEVEL, "%sHTTP connection made", prefix)

    def connection_lost(self, exc: Exception | None) -> None:
        self.connections.discard(self)

        if self.logger.level <= TRACE_LOG_LEVEL:
            prefix = "%s:%d - " % self.client if self.client else ""
            self.logger.log(TRACE_LOG_LEVEL, "%sHTTP connection lost", prefix)