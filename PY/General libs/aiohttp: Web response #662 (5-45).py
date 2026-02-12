import math
import time
import warnings
from collections.abc import Iterator, MutableMapping
from concurrent.futures import Executor
from http import HTTPStatus
from typing import TYPE_CHECKING, Any, Optional, TypeVar, Union, cast, overload

from multidict import CIMultiDict, istr

from . import hdrs, payload
from .abc import AbstractStreamWriter
from .compression_utils import ZLibCompressor
from .helpers import (
    ETAG_ANY,
    QUOTED_ETAG_RE,
    CookieMixin,
    ETag,
    HeadersMixin,
    ResponseKey,
    must_be_empty_body,
    parse_http_date,
    populate_with_cookies,
    rfc822_formatted_time,
    sentinel,
    should_remove_content_length,
    validate_etag_value,
)
from .http import SERVER_SOFTWARE, HttpVersion10, HttpVersion11
from .payload import Payload
from .typedefs import JSONEncoder, LooseHeaders

REASON_PHRASES = {http_status.value: http_status.phrase for http_status in HTTPStatus}
LARGE_BODY_SIZE = 1024**2

__all__ = ("ContentCoding", "StreamResponse", "Response", "json_response")


if TYPE_CHECKING:
    from .web_request import BaseRequest
