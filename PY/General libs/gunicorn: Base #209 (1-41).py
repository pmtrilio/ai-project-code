#
# This file is part of gunicorn released under the MIT license.
# See the NOTICE for more information.

import io
import os
import signal
import sys
import time
import traceback
from datetime import datetime
from random import randint
from ssl import SSLError

from gunicorn import util
from gunicorn.http.errors import (
    ForbiddenProxyRequest, InvalidHeader,
    InvalidHeaderName, InvalidHTTPVersion,
    InvalidProxyLine, InvalidRequestLine,
    InvalidRequestMethod, InvalidSchemeHeaders,
    LimitRequestHeaders, LimitRequestLine,
    UnsupportedTransferCoding, ExpectationFailed,
    ConfigurationProblem, ObsoleteFolding,
)
from gunicorn.http.wsgi import Response, default_environ
from gunicorn.reloader import reloader_engines
from gunicorn.workers.workertmp import WorkerTmp


class Worker:

    SIGNALS = [getattr(signal, "SIG%s" % x) for x in (
        "ABRT HUP QUIT INT TERM USR1 USR2 WINCH CHLD".split()
    )]

    PIPE = []

    def __init__(self, age, ppid, sockets, app, timeout, cfg, log):
        """\
        This is called pre-fork so it shouldn't do anything to the
        current process. If there's a need to make process wide