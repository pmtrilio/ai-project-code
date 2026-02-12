    for t, q in items:
        t.join()

# Register for `_python_exit()` to be called just before joining all
# non-daemon threads. This is used instead of `atexit.register()` for
# compatibility with subinterpreters, which no longer support daemon threads.
# See bpo-39812 for context.
threading._register_atexit(_python_exit)

# At fork, reinitialize the `_global_shutdown_lock` lock in the child process
if hasattr(os, 'register_at_fork'):
    os.register_at_fork(before=_global_shutdown_lock.acquire,
                        after_in_child=_global_shutdown_lock._at_fork_reinit,
                        after_in_parent=_global_shutdown_lock.release)
    os.register_at_fork(after_in_child=_threads_queues.clear)


class WorkerContext:

    @classmethod
    def prepare(cls, initializer, initargs):
        if initializer is not None:
            if not callable(initializer):
                raise TypeError("initializer must be a callable")
        def create_context():
            return cls(initializer, initargs)
        def resolve_task(fn, args, kwargs):
            return (fn, args, kwargs)
        return create_context, resolve_task

    def __init__(self, initializer, initargs):
        self.initializer = initializer
        self.initargs = initargs

    def initialize(self):
        if self.initializer is not None:
            self.initializer(*self.initargs)

    def finalize(self):
        pass
