import queue
import sys
import typing
import warnings
import weakref
from socket import timeout as SocketTimeout
from types import TracebackType

from ._base_connection import _TYPE_BODY
from ._collections import HTTPHeaderDict
from ._request_methods import RequestMethods
from .connection import (
    BaseSSLError,
    BrokenPipeError,
    DummyConnection,
    HTTPConnection,
    HTTPException,
    HTTPSConnection,
    ProxyConfig,
    _wrap_proxy_error,
)
from .connection import port_by_scheme as port_by_scheme
from .exceptions import (
    ClosedPoolError,
    EmptyPoolError,
    FullPoolError,
    HostChangedError,
    InsecureRequestWarning,
    LocationValueError,
    MaxRetryError,
    NewConnectionError,
    ProtocolError,
    ProxyError,
    ReadTimeoutError,
    SSLError,
    TimeoutError,
)
from .response import BaseHTTPResponse
from .util.connection import is_connection_dropped
from .util.proxy import connection_requires_http_tunnel
from .util.request import _TYPE_BODY_POSITION, set_file_position