from __future__ import annotations

import json
import typing as t
from http import HTTPStatus
from urllib.parse import urljoin

from .._internal import _get_environ
from ..datastructures import Headers
from ..http import generate_etag
from ..http import http_date
from ..http import is_resource_modified
from ..http import parse_etags
from ..http import parse_range_header
from ..http import remove_entity_headers
from ..sansio.response import Response as _SansIOResponse
from ..urls import iri_to_uri
from ..utils import cached_property
from ..wsgi import _RangeWrapper
from ..wsgi import ClosingIterator
from ..wsgi import get_current_url

if t.TYPE_CHECKING:
    from _typeshed.wsgi import StartResponse
    from _typeshed.wsgi import WSGIApplication
    from _typeshed.wsgi import WSGIEnvironment

    from .request import Request


def _iter_encoded(iterable: t.Iterable[str | bytes]) -> t.Iterator[bytes]:
    for item in iterable:
        if isinstance(item, str):
            yield item.encode()
        else:
            yield item


class Response(_SansIOResponse):
    """Represents an outgoing WSGI HTTP response with body, status, and
    headers. Has properties and methods for using the functionality
    defined by various HTTP specs.

    The response body is flexible to support different use cases. The
    simple form is passing bytes, or a string which will be encoded as
    UTF-8. Passing an iterable of bytes or strings makes this a
    streaming response. A generator is particularly useful for building
    a CSV file in memory or using SSE (Server Sent Events). A file-like
    object is also iterable, although the
    :func:`~werkzeug.utils.send_file` helper should be used in that
    case.

    The response object is itself a WSGI application callable. When
    called (:meth:`__call__`) with ``environ`` and ``start_response``,
    it will pass its status and headers to ``start_response`` then
    return its body as an iterable.

    .. code-block:: python

        from werkzeug.wrappers.response import Response
