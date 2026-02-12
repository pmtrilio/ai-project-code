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
        ip_str: The IP address string to check
        allow_list: The original allow list (strings, may contain "*")
        networks: Pre-computed ipaddress.ip_network objects from config
    """
    if '*' in allow_list:
        return True
    try:
        ip = ipaddress.ip_address(ip_str)
    except ValueError:
        return False
    for network in networks:
        if ip in network:
            return True
    return False


class Message:
    def __init__(self, cfg, unreader, peer_addr):
        self.cfg = cfg
        self.unreader = unreader
        self.peer_addr = peer_addr
        self.remote_addr = peer_addr
        self.version = None
        self.headers = []
        self.trailers = []
        self.body = None
        self.scheme = "https" if cfg.is_ssl else "http"
        self.must_close = False
        self._expected_100_continue = False

        # set headers limits
        self.limit_request_fields = cfg.limit_request_fields
        if (self.limit_request_fields <= 0
                or self.limit_request_fields > MAX_HEADERS):
            self.limit_request_fields = MAX_HEADERS
        self.limit_request_field_size = cfg.limit_request_field_size
        if self.limit_request_field_size < 0:
            self.limit_request_field_size = DEFAULT_MAX_HEADERFIELD_SIZE

        # set max header buffer size
        max_header_field_size = self.limit_request_field_size or DEFAULT_MAX_HEADERFIELD_SIZE
        self.max_buffer_headers = self.limit_request_fields * \
            (max_header_field_size + 2) + 4

        unused = self.parse(self.unreader)
        self.unreader.unread(unused)
        self.set_body_reader()

    def force_close(self):
        self.must_close = True

    def parse(self, unreader):
        raise NotImplementedError()

    def parse_headers(self, data, from_trailer=False):
        cfg = self.cfg
        headers = []

        # Split lines on \r\n
        lines = [bytes_to_str(line) for line in data.split(b"\r\n")]

        # handle scheme headers
        scheme_header = False
        secure_scheme_headers = {}
        forwarder_headers = []