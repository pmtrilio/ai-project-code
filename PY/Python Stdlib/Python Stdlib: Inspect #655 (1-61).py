"""Get useful information from live Python objects.

This module encapsulates the interface provided by the internal special
attributes (co_*, im_*, tb_*, etc.) in a friendlier fashion.
It also provides some help for examining source code and class layout.

Here are some of the useful functions provided by this module:

    ismodule(), isclass(), ismethod(), ispackage(), isfunction(),
        isgeneratorfunction(), isgenerator(), istraceback(), isframe(),
        iscode(), isbuiltin(), isroutine() - check object types
    getmembers() - get members of an object that satisfy a given condition

    getfile(), getsourcefile(), getsource() - find an object's source code
    getdoc(), getcomments() - get documentation on an object
    getmodule() - determine the module that an object came from
    getclasstree() - arrange classes so as to represent their hierarchy

    getargvalues(), getcallargs() - get info about function arguments
    getfullargspec() - same, with support for Python 3 features
    formatargvalues() - format an argument spec
    getouterframes(), getinnerframes() - get info about frames
    currentframe() - get the current stack frame
    stack(), trace() - get info about frames on the stack or in a traceback

    signature() - get a Signature object for the callable
"""

# This module is in the public domain.  No warranties.

__author__ = ('Ka-Ping Yee <ping@lfw.org>',
              'Yury Selivanov <yselivanov@sprymix.com>')

__all__ = [
    "AGEN_CLOSED",
    "AGEN_CREATED",
    "AGEN_RUNNING",
    "AGEN_SUSPENDED",
    "ArgInfo",
    "Arguments",
    "Attribute",
    "BlockFinder",
    "BoundArguments",
    "BufferFlags",
    "CORO_CLOSED",
    "CORO_CREATED",
    "CORO_RUNNING",
    "CORO_SUSPENDED",
    "CO_ASYNC_GENERATOR",
    "CO_COROUTINE",
    "CO_GENERATOR",
    "CO_ITERABLE_COROUTINE",
    "CO_NESTED",
    "CO_NEWLOCALS",
    "CO_NOFREE",
    "CO_OPTIMIZED",
    "CO_VARARGS",
    "CO_VARKEYWORDS",
    "CO_HAS_DOCSTRING",
    "CO_METHOD",
    "ClassFoundException",