import threading
import time
import traceback
import types
import warnings

# If threading is available then ThreadPool should be provided.  Therefore
# we avoid top-level imports which are liable to fail on some systems.
from . import util
from . import get_context, TimeoutError
from .connection import wait

#
# Constants representing the state of a pool
#

INIT = "INIT"
RUN = "RUN"
CLOSE = "CLOSE"
TERMINATE = "TERMINATE"

#
# Miscellaneous
#

job_counter = itertools.count()

def mapstar(args):
    return list(map(*args))

def starmapstar(args):
    return list(itertools.starmap(args[0], args[1]))

#
# Hack to embed stringification of remote traceback in local traceback
#

class RemoteTraceback(Exception):
    def __init__(self, tb):
        self.tb = tb
    def __str__(self):
        return self.tb

class ExceptionWithTraceback:
    def __init__(self, exc, tb):
        tb = traceback.format_exception(type(exc), exc, tb)
        tb = ''.join(tb)
        self.exc = exc
        self.tb = '\n"""\n%s"""' % tb
    def __reduce__(self):
        return rebuild_exc, (self.exc, self.tb)

def rebuild_exc(exc, tb):
    exc.__cause__ = RemoteTraceback(tb)
    return exc

#
# Code run by worker processes
#

class MaybeEncodingError(Exception):