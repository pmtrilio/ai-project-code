by Apache's log4j system.

Copyright (C) 2001-2022 Vinay Sajip. All Rights Reserved.

To use, simply 'import logging' and log away!
"""

import errno
import functools
import io
import logging
import logging.handlers
import os
import queue
import re
import struct
import threading
import traceback

from socketserver import ThreadingTCPServer, StreamRequestHandler


DEFAULT_LOGGING_CONFIG_PORT = 9030

RESET_ERROR = errno.ECONNRESET

#
#   The following code implements a socket listener for on-the-fly
#   reconfiguration of logging.
#
#   _listener holds the server object doing the listening
_listener = None

def fileConfig(fname, defaults=None, disable_existing_loggers=True, encoding=None):
    """
    Read the logging configuration from a ConfigParser-format file.

    This can be called several times from an application, allowing an end user
    the ability to select from various pre-canned configurations (if the
    developer provides a mechanism to present the choices and load the chosen
    configuration).