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