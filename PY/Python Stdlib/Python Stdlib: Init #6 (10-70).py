# VINAY SAJIP DISCLAIMS ALL WARRANTIES WITH REGARD TO THIS SOFTWARE, INCLUDING
# ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS. IN NO EVENT SHALL
# VINAY SAJIP BE LIABLE FOR ANY SPECIAL, INDIRECT OR CONSEQUENTIAL DAMAGES OR
# ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF USE, DATA OR PROFITS, WHETHER
# IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER TORTIOUS ACTION, ARISING OUT
# OF OR IN CONNECTION WITH THE USE OR PERFORMANCE OF THIS SOFTWARE.

"""
Logging package for Python. Based on PEP 282 and comments thereto in
comp.lang.python.

Copyright (C) 2001-2022 Vinay Sajip. All Rights Reserved.

To use, simply 'import logging' and log away!
"""

import sys, os, time, io, re, traceback, warnings, weakref, collections.abc

from types import GenericAlias
from string import Template
from string import Formatter as StrFormatter


__all__ = ['BASIC_FORMAT', 'BufferingFormatter', 'CRITICAL', 'DEBUG', 'ERROR',
           'FATAL', 'FileHandler', 'Filter', 'Formatter', 'Handler', 'INFO',
           'LogRecord', 'Logger', 'LoggerAdapter', 'NOTSET', 'NullHandler',
           'StreamHandler', 'WARN', 'WARNING', 'addLevelName', 'basicConfig',
           'captureWarnings', 'critical', 'debug', 'disable', 'error',
           'exception', 'fatal', 'getLevelName', 'getLogger', 'getLoggerClass',
           'info', 'log', 'makeLogRecord', 'setLoggerClass', 'shutdown',
           'warn', 'warning', 'getLogRecordFactory', 'setLogRecordFactory',
           'lastResort', 'raiseExceptions', 'getLevelNamesMapping',
           'getHandlerByName', 'getHandlerNames']

import threading

__author__  = "Vinay Sajip <vinay_sajip@red-dove.com>"
__status__  = "production"

#---------------------------------------------------------------------------
#   Miscellaneous module data
#---------------------------------------------------------------------------

#
#_startTime is used as the base when calculating the relative time of events
#
_startTime = time.time_ns()

#
#raiseExceptions is used to see if exceptions during handling should be
#propagated
#
raiseExceptions = True

#
# If you don't want threading information in the log, set this to False
#
logThreads = True

#
# If you don't want multiprocessing information in the log, set this to False