__all__ = ['BaseProcess', 'current_process', 'active_children',
           'parent_process']

#
# Imports
#

import os
import sys
import signal
import itertools
import threading
from _weakrefset import WeakSet

#
#
#

try:
    ORIGINAL_DIR = os.path.abspath(os.getcwd())
except OSError:
    ORIGINAL_DIR = None

#
# Public functions
#

def current_process():
    '''
    Return process object representing the current process
    '''
    return _current_process

def active_children():
    '''
    Return list of process objects corresponding to live child processes
    '''
    _cleanup()
    return list(_children)


def parent_process():
    '''
    Return process object representing the parent process
    '''
    return _parent_process

#
#
#

def _cleanup():
    # check for processes which have finished
    for p in list(_children):
        if (child_popen := p._popen) and child_popen.poll() is not None:
            _children.discard(p)

#
# The `Process` class
#
