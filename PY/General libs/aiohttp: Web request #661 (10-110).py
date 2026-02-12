from collections.abc import Iterator, Mapping, MutableMapping
from re import Pattern
from types import MappingProxyType
from typing import TYPE_CHECKING, Any, Final, Optional, TypeVar, cast, overload
from urllib.parse import parse_qsl

from multidict import CIMultiDict, CIMultiDictProxy, MultiDict, MultiDictProxy
from yarl import URL

from . import hdrs
from ._cookie_helpers import parse_cookie_header
from .abc import AbstractStreamWriter
from .helpers import (
    _SENTINEL,
    ETAG_ANY,
    LIST_QUOTED_ETAG_RE,
    ChainMapProxy,
    ETag,
    HeadersMixin,
    RequestKey,
    frozen_dataclass_decorator,
    is_expected_content_type,
    parse_http_date,
    reify,
    sentinel,
    set_exception,
)
from .http_parser import RawRequestMessage
from .http_writer import HttpVersion
from .multipart import BodyPartReader, MultipartReader
from .streams import EmptyStreamReader, StreamReader
from .typedefs import (
    DEFAULT_JSON_DECODER,
    JSONDecoder,
    LooseHeaders,
    RawHeaders,
    StrOrURL,
)
from .web_exceptions import (
    HTTPBadRequest,
    HTTPRequestEntityTooLarge,
    HTTPUnsupportedMediaType,
)
from .web_response import StreamResponse

if sys.version_info >= (3, 11):
    from typing import Self
else:
    Self = Any

__all__ = ("BaseRequest", "FileField", "Request")


if TYPE_CHECKING:
    from .web_app import Application
    from .web_protocol import RequestHandler
    from .web_urldispatcher import UrlMappingMatchInfo


_T = TypeVar("_T")


@frozen_dataclass_decorator
class FileField:
    name: str
    filename: str
    file: io.BufferedReader
    content_type: str
    headers: CIMultiDictProxy[str]


_TCHAR: Final[str] = string.digits + string.ascii_letters + r"!#$%&'*+.^_`|~-"
# '-' at the end to prevent interpretation as range in a char class

_TOKEN: Final[str] = rf"[{_TCHAR}]+"

_QDTEXT: Final[str] = r"[{}]".format(
    r"".join(chr(c) for c in (0x09, 0x20, 0x21) + tuple(range(0x23, 0x7F)))
)
# qdtext includes 0x5C to escape 0x5D ('\]')
# qdtext excludes obs-text (because obsoleted, and encoding not specified)

_QUOTED_PAIR: Final[str] = r"\\[\t !-~]"

_QUOTED_STRING: Final[str] = rf'"(?:{_QUOTED_PAIR}|{_QDTEXT})*"'

# This does not have a ReDOS/performance concern as long as it used with re.match().
_FORWARDED_PAIR: Final[str] = rf"({_TOKEN})=({_TOKEN}|{_QUOTED_STRING})(:\d{{1,4}})?"

_QUOTED_PAIR_REPLACE_RE: Final[Pattern[str]] = re.compile(r"\\([\t !-~])")
# same pattern as _QUOTED_PAIR but contains a capture group

_FORWARDED_PAIR_RE: Final[Pattern[str]] = re.compile(_FORWARDED_PAIR)

############################################################
# HTTP Request
############################################################


class BaseRequest(MutableMapping[str | RequestKey[Any], Any], HeadersMixin):
    POST_METHODS = {