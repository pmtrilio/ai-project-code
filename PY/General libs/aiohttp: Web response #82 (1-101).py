import asyncio
import datetime
import enum
import json
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


_T = TypeVar("_T")


# TODO(py311): Convert to StrEnum for wider use
class ContentCoding(enum.Enum):
    # The content codings that we have support for.
    #
    # Additional registered codings are listed at:
    # https://www.iana.org/assignments/http-parameters/http-parameters.xhtml#content-coding
    deflate = "deflate"
    gzip = "gzip"
    identity = "identity"


CONTENT_CODINGS = {coding.value: coding for coding in ContentCoding}

############################################################
# HTTP Response classes
############################################################


class StreamResponse(
    MutableMapping[str | ResponseKey[Any], Any], HeadersMixin, CookieMixin
):

    _body: None | bytes | bytearray | Payload
    _length_check = True
    _body = None
    _keep_alive: bool | None = None
    _chunked: bool = False
    _compression: bool = False
    _compression_strategy: int | None = None
    _compression_force: ContentCoding | None = None
    _req: Optional["BaseRequest"] = None
    _payload_writer: AbstractStreamWriter | None = None
    _eof_sent: bool = False
    _must_be_empty_body: bool | None = None
    _body_length = 0
    _send_headers_immediately = True

    def __init__(
        self,
        *,
        status: int = 200,
        reason: str | None = None,
        headers: LooseHeaders | None = None,
        _real_headers: CIMultiDict[str] | None = None,
    ) -> None:
        """Initialize a new stream response object.

        _real_headers is an internal parameter used to pass a pre-populated
        headers object. It is used by the `Response` class to avoid copying
        the headers when creating a new response object. It is not intended
        to be used by external code.
        """