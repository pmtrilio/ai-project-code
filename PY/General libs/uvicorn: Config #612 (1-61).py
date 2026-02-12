from __future__ import annotations

import asyncio
import inspect
import json
import logging
import logging.config
import os
import socket
import ssl
import sys
from collections.abc import Awaitable, Callable
from configparser import RawConfigParser
from pathlib import Path
from typing import IO, Any, Literal

import click

from uvicorn._compat import iscoroutinefunction
from uvicorn._types import ASGIApplication
from uvicorn.importer import ImportFromStringError, import_from_string
from uvicorn.logging import TRACE_LOG_LEVEL
from uvicorn.middleware.asgi2 import ASGI2Middleware
from uvicorn.middleware.message_logger import MessageLoggerMiddleware
from uvicorn.middleware.proxy_headers import ProxyHeadersMiddleware
from uvicorn.middleware.wsgi import WSGIMiddleware

HTTPProtocolType = Literal["auto", "h11", "httptools"]
WSProtocolType = Literal["auto", "none", "websockets", "websockets-sansio", "wsproto"]
LifespanType = Literal["auto", "on", "off"]
LoopFactoryType = Literal["none", "auto", "asyncio", "uvloop"]
InterfaceType = Literal["auto", "asgi3", "asgi2", "wsgi"]

LOG_LEVELS: dict[str, int] = {
    "critical": logging.CRITICAL,
    "error": logging.ERROR,
    "warning": logging.WARNING,
    "info": logging.INFO,
    "debug": logging.DEBUG,
    "trace": TRACE_LOG_LEVEL,
}
HTTP_PROTOCOLS: dict[str, str] = {
    "auto": "uvicorn.protocols.http.auto:AutoHTTPProtocol",
    "h11": "uvicorn.protocols.http.h11_impl:H11Protocol",
    "httptools": "uvicorn.protocols.http.httptools_impl:HttpToolsProtocol",
}
WS_PROTOCOLS: dict[str, str | None] = {
    "auto": "uvicorn.protocols.websockets.auto:AutoWebSocketsProtocol",
    "none": None,
    "websockets": "uvicorn.protocols.websockets.websockets_impl:WebSocketProtocol",
    "websockets-sansio": "uvicorn.protocols.websockets.websockets_sansio_impl:WebSocketsSansIOProtocol",
    "wsproto": "uvicorn.protocols.websockets.wsproto_impl:WSProtocol",
}
LIFESPAN: dict[str, str] = {
    "auto": "uvicorn.lifespan.on:LifespanOn",
    "on": "uvicorn.lifespan.on:LifespanOn",
    "off": "uvicorn.lifespan.off:LifespanOff",
}
LOOP_FACTORIES: dict[str, str | None] = {
    "none": None,
    "auto": "uvicorn.loops.auto:auto_loop_factory",