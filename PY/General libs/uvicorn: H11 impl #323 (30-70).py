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