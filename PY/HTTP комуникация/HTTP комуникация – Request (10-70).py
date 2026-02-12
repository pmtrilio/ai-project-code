from django.core.exceptions import (
    BadRequest,
    DisallowedHost,
    ImproperlyConfigured,
    RequestDataTooBig,
    TooManyFieldsSent,
)
from django.core.files import uploadhandler
from django.http.multipartparser import (
    MultiPartParser,
    MultiPartParserError,
    TooManyFilesSent,
)
from django.utils.datastructures import (
    CaseInsensitiveMapping,
    ImmutableList,
    MultiValueDict,
)
from django.utils.encoding import escape_uri_path, iri_to_uri
from django.utils.functional import cached_property
from django.utils.http import is_same_domain, parse_header_parameters
from django.utils.regex_helper import _lazy_re_compile

RAISE_ERROR = object()
host_validation_re = _lazy_re_compile(
    r"^([a-z0-9.-]+|\[[a-f0-9]*:[a-f0-9.:]+\])(?::([0-9]+))?$"
)


class UnreadablePostError(OSError):
    pass


class RawPostDataException(Exception):
    """
    You cannot access raw_post_data from a request that has
    multipart/* POST data if it has been accessed via POST,
    FILES, etc..
    """

    pass


class HttpRequest:
    """A basic HTTP request."""

    # The encoding used in GET/POST dicts. None means use default setting.
    _encoding = None
    _upload_handlers = []

    def __init__(self):
        # WARNING: The `WSGIRequest` subclass doesn't call `super`.
        # Any variable assignment made here should also happen in
        # `WSGIRequest.__init__()`.

        self.GET = QueryDict(mutable=True)
        self.POST = QueryDict(mutable=True)
        self.COOKIES = {}
        self.META = {}
        self.FILES = MultiValueDict()
