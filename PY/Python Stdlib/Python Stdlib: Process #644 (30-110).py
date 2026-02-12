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

class BaseProcess(object):
    '''
    Process objects represent activity that is run in a separate process

    The class is analogous to `threading.Thread`
    '''
    def _Popen(self):
        raise NotImplementedError

    def __init__(self, group=None, target=None, name=None, args=(), kwargs=None,
                 *, daemon=None):
        assert group is None, 'group argument must be None for now'
        count = next(_process_counter)
        self._identity = _current_process._identity + (count,)
        self._config = _current_process._config.copy()
        self._parent_pid = os.getpid()
        self._parent_name = _current_process.name
        self._popen = None
        self._closed = False
        self._target = target
        self._args = tuple(args)
        self._kwargs = dict(kwargs) if kwargs else {}
        self._name = name or type(self).__name__ + '-' + \
                     ':'.join(str(i) for i in self._identity)
        if daemon is not None:
            self.daemon = daemon
        _dangling.add(self)

    def _check_closed(self):
        if self._closed:
            raise ValueError("process object is closed")

    def run(self):
        '''
        Method to be run in sub-process; can be overridden in sub-class
        '''
        if self._target:
            self._target(*self._args, **self._kwargs)

    def start(self):