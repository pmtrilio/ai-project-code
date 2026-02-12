# to the functools module.
# Written by Nick Coghlan <ncoghlan at gmail.com>,
# Raymond Hettinger <python at rcn.com>,
# and Łukasz Langa <lukasz at langa.pl>.
#   Copyright (C) 2006 Python Software Foundation.
# See C source code for _functools credits/copyright

__all__ = ['update_wrapper', 'wraps', 'WRAPPER_ASSIGNMENTS', 'WRAPPER_UPDATES',
           'total_ordering', 'cache', 'cmp_to_key', 'lru_cache', 'reduce',
           'partial', 'partialmethod', 'singledispatch', 'singledispatchmethod',
           'cached_property', 'Placeholder']

from abc import get_cache_token
from collections import namedtuple
# import weakref  # Deferred to single_dispatch()
from operator import itemgetter
from reprlib import recursive_repr
from types import GenericAlias, MethodType, MappingProxyType, UnionType
from _thread import RLock

################################################################################
### update_wrapper() and wraps() decorator
################################################################################

# update_wrapper() and wraps() are tools to help write
# wrapper functions that can handle naive introspection

WRAPPER_ASSIGNMENTS = ('__module__', '__name__', '__qualname__', '__doc__',
                       '__annotate__', '__type_params__')
WRAPPER_UPDATES = ('__dict__',)
def update_wrapper(wrapper,
                   wrapped,
                   assigned = WRAPPER_ASSIGNMENTS,
                   updated = WRAPPER_UPDATES):
    """Update a wrapper function to look like the wrapped function

       wrapper is the function to be updated
       wrapped is the original function
       assigned is a tuple naming the attributes assigned directly
       from the wrapped function to the wrapper function (defaults to
       functools.WRAPPER_ASSIGNMENTS)