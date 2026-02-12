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
    "asyncio": "uvicorn.loops.asyncio:asyncio_loop_factory",
    "uvloop": "uvicorn.loops.uvloop:uvloop_loop_factory",
}
INTERFACES: list[InterfaceType] = ["auto", "asgi3", "asgi2", "wsgi"]

SSL_PROTOCOL_VERSION: int = ssl.PROTOCOL_TLS_SERVER

LOGGING_CONFIG: dict[str, Any] = {
    "version": 1,