import typing
import warnings
from types import TracebackType
from urllib.parse import urljoin

from ._collections import HTTPHeaderDict, RecentlyUsedContainer
from ._request_methods import RequestMethods
from .connection import ProxyConfig
from .connectionpool import HTTPConnectionPool, HTTPSConnectionPool, port_by_scheme
from .exceptions import (
    LocationValueError,
    MaxRetryError,
    ProxySchemeUnknown,
    URLSchemeUnknown,
)
from .response import BaseHTTPResponse
from .util.connection import _TYPE_SOCKET_OPTIONS
from .util.proxy import connection_requires_http_tunnel
from .util.retry import Retry
from .util.timeout import Timeout
from .util.url import Url, parse_url

if typing.TYPE_CHECKING:
    import ssl

    from typing_extensions import Self

__all__ = ["PoolManager", "ProxyManager", "proxy_from_url"]


log = logging.getLogger(__name__)

SSL_KEYWORDS = (
    "key_file",
    "cert_file",
    "cert_reqs",
    "ca_certs",
    "ca_cert_data",
    "ssl_version",
    "ssl_minimum_version",
    "ssl_maximum_version",
    "ca_cert_dir",
    "ssl_context",
    "key_password",
    "server_hostname",
)
# Default value for `blocksize` - a new parameter introduced to
# http.client.HTTPConnection & http.client.HTTPSConnection in Python 3.7
_DEFAULT_BLOCKSIZE = 16384


class PoolKey(typing.NamedTuple):
    """
    All known keyword arguments that could be provided to the pool manager, its
    pools, or the underlying connections.

    All custom key schemes should include the fields in this key at a minimum.
    """

    key_scheme: str
    key_host: str