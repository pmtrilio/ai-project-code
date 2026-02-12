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
