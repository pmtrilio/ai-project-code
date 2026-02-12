    Protocols: TypeAlias = H11Protocol | HttpToolsProtocol | WSProtocol | WebSocketProtocol | WebSocketsSansIOProtocol

HANDLED_SIGNALS = (
    signal.SIGINT,  # Unix signal 2. Sent by Ctrl+C.
    signal.SIGTERM,  # Unix signal 15. Sent by `kill <pid>`.
)
if sys.platform == "win32":  # pragma: py-not-win32
    HANDLED_SIGNALS += (signal.SIGBREAK,)  # Windows signal 21. Sent by Ctrl+Break.

logger = logging.getLogger("uvicorn.error")


class ServerState:
    """
    Shared servers state that is available between all protocol instances.
    """

    def __init__(self) -> None:
        self.total_requests = 0
        self.connections: set[Protocols] = set()
        self.tasks: set[asyncio.Task[None]] = set()
        self.default_headers: list[tuple[bytes, bytes]] = []


class Server:
    def __init__(self, config: Config) -> None:
        self.config = config
        self.server_state = ServerState()

        self.started = False
        self.should_exit = False
        self.force_exit = False
        self.last_notified = 0.0

        self._captured_signals: list[int] = []

    def run(self, sockets: list[socket.socket] | None = None) -> None:
        return asyncio_run(self.serve(sockets=sockets), loop_factory=self.config.get_loop_factory())

    async def serve(self, sockets: list[socket.socket] | None = None) -> None:
        with self.capture_signals():