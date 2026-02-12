from __future__ import annotations

import asyncio
import contextvars
import http
import logging
from collections.abc import Callable
from typing import Any, Literal, cast
from urllib.parse import unquote

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