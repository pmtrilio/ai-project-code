    'SimpleQueue',
]


try:
    from _queue import Empty
except ImportError:
    class Empty(Exception):
        'Exception raised by Queue.get(block=0)/get_nowait().'
        pass

class Full(Exception):
    'Exception raised by Queue.put(block=0)/put_nowait().'
    pass


class ShutDown(Exception):
    '''Raised when put/get with shut-down queue.'''


class Queue:
    '''Create a queue object with a given maximum size.

    If maxsize is <= 0, the queue size is infinite.
    '''

    def __init__(self, maxsize=0):
        self.maxsize = maxsize
        self._init(maxsize)

        # mutex must be held whenever the queue is mutating.  All methods
        # that acquire mutex must release it before returning.  mutex
        # is shared between the three conditions, so acquiring and
        # releasing the conditions also acquires and releases mutex.
        self.mutex = threading.Lock()

        # Notify not_empty whenever an item is added to the queue; a
        # thread waiting to get is notified then.
        self.not_empty = threading.Condition(self.mutex)

        # Notify not_full whenever an item is removed from the queue;