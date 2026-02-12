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
