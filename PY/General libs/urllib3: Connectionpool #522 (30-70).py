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
from .util.retry import Retry
from .util.ssl_match_hostname import CertificateError
from .util.timeout import _DEFAULT_TIMEOUT, _TYPE_DEFAULT, Timeout
from .util.url import Url, _encode_target
from .util.url import _normalize_host as normalize_host
from .util.url import parse_url
from .util.util import to_str

if typing.TYPE_CHECKING:
    import ssl

    from typing_extensions import Self

    from ._base_connection import BaseHTTPConnection, BaseHTTPSConnection

log = logging.getLogger(__name__)

_TYPE_TIMEOUT = typing.Union[Timeout, float, _TYPE_DEFAULT, None]


# Pool objects
class ConnectionPool:
    """
    Base class for all connection pools, such as
    :class:`.HTTPConnectionPool` and :class:`.HTTPSConnectionPool`.