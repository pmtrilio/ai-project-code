gethostbyaddr() -- map an IP number or hostname to DNS info
getservbyname() -- map a service name and a protocol name to a port number
getprotobyname() -- map a protocol name (e.g. 'tcp') to a number
ntohs(), ntohl() -- convert 16, 32 bit int from network to host byte order
htons(), htonl() -- convert 16, 32 bit int from host to network byte order
inet_aton() -- convert IP addr string (123.45.67.89) to 32-bit packed format
inet_ntoa() -- convert 32-bit packed format IP to string (123.45.67.89)
socket.getdefaulttimeout() -- get the default timeout value
socket.setdefaulttimeout() -- set the default timeout value
create_connection() -- connects to an address, with an optional timeout and
                       optional source address.
create_server() -- create a TCP socket and bind it to a specified address.

 [*] not available on all platforms!

Special objects:

SocketType -- type object for socket objects
error -- exception raised for I/O errors
has_ipv6 -- boolean value indicating if IPv6 is supported

IntEnum constants:

AF_INET, AF_UNIX -- socket domains (first argument to socket() call)
SOCK_STREAM, SOCK_DGRAM, SOCK_RAW -- socket types (second argument)

Integer constants:

Many other constants may be defined; these may be used in calls to
the setsockopt() and getsockopt() methods.
"""

import _socket
from _socket import *

import io
import os
import sys
from enum import IntEnum, IntFlag
from functools import partial

try:
    import errno
except ImportError:
    errno = None
EBADF = getattr(errno, 'EBADF', 9)
EAGAIN = getattr(errno, 'EAGAIN', 11)
EWOULDBLOCK = getattr(errno, 'EWOULDBLOCK', 11)

__all__ = ["fromfd", "getfqdn", "create_connection", "create_server",
           "has_dualstack_ipv6", "AddressFamily", "SocketKind"]
__all__.extend(os._get_exports_list(_socket))

# Set up the socket.AF_* socket.SOCK_* constants as members of IntEnums for
# nicer string representations.
# Note that _socket only knows about the integer values. The public interface
# in this module understands the enums and translates them back from integers
# where needed (e.g. .family property of a socket object).

IntEnum._convert_(
        'AddressFamily',
        __name__,
        lambda C: C.isupper() and C.startswith('AF_'))

IntEnum._convert_(
        'SocketKind',
        __name__,
        lambda C: C.isupper() and C.startswith('SOCK_'))

IntFlag._convert_(
        'MsgFlag',
        __name__,
        lambda C: C.isupper() and C.startswith('MSG_'))

IntFlag._convert_(
        'AddressInfo',
        __name__,
        lambda C: C.isupper() and C.startswith('AI_'))

_LOCALHOST    = '127.0.0.1'
_LOCALHOST_V6 = '::1'