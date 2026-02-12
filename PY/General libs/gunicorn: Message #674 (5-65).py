from enum import IntEnum
import ipaddress
import re
import socket
import struct

from gunicorn.http.body import ChunkedReader, LengthReader, EOFReader, Body
from gunicorn.http.errors import (
    InvalidHeader, InvalidHeaderName, NoMoreData,
    InvalidRequestLine, InvalidRequestMethod, InvalidHTTPVersion,
    LimitRequestLine, LimitRequestHeaders,
    UnsupportedTransferCoding, ObsoleteFolding,
    ExpectationFailed,
)
from gunicorn.http.errors import InvalidProxyLine, InvalidProxyHeader, ForbiddenProxyRequest
from gunicorn.http.errors import InvalidSchemeHeaders
from gunicorn.util import bytes_to_str, split_request_uri


# PROXY protocol v2 constants
PP_V2_SIGNATURE = b"\x0D\x0A\x0D\x0A\x00\x0D\x0A\x51\x55\x49\x54\x0A"


class PPCommand(IntEnum):
    """PROXY protocol v2 commands."""
    LOCAL = 0x0
    PROXY = 0x1


class PPFamily(IntEnum):
    """PROXY protocol v2 address families."""
    UNSPEC = 0x0
    INET = 0x1   # IPv4
    INET6 = 0x2  # IPv6
    UNIX = 0x3


class PPProtocol(IntEnum):
    """PROXY protocol v2 transport protocols."""
    UNSPEC = 0x0
    STREAM = 0x1  # TCP
    DGRAM = 0x2   # UDP


MAX_REQUEST_LINE = 8190
MAX_HEADERS = 32768
DEFAULT_MAX_HEADERFIELD_SIZE = 8190

# verbosely on purpose, avoid backslash ambiguity
RFC9110_5_6_2_TOKEN_SPECIALS = r"!#$%&'*+-.^_`|~"
TOKEN_RE = re.compile(r"[%s0-9a-zA-Z]+" % (re.escape(RFC9110_5_6_2_TOKEN_SPECIALS)))
METHOD_BADCHAR_RE = re.compile("[a-z#]")
# usually 1.0 or 1.1 - RFC9112 permits restricting to single-digit versions
VERSION_RE = re.compile(r"HTTP/(\d)\.(\d)")
RFC9110_5_5_INVALID_AND_DANGEROUS = re.compile(r"[\0\r\n]")


def _ip_in_allow_list(ip_str, allow_list, networks):
    """Check if IP address is in the allow list.

    Args: