from .exceptions import NoReverseMatch, Resolver404
from .utils import get_callable


class ResolverMatch:
    def __init__(
        self,
        func,
        args,
        kwargs,
        url_name=None,
        app_names=None,
        namespaces=None,
        route=None,
        tried=None,
        captured_kwargs=None,
        extra_kwargs=None,
    ):
        self.func = func
        self.args = args
        self.kwargs = kwargs
        self.url_name = url_name
        self.route = route
        self.tried = tried
        self.captured_kwargs = captured_kwargs
        self.extra_kwargs = extra_kwargs

        # If a URLRegexResolver doesn't have a namespace or app_name, it passes
        # in an empty value.
        self.app_names = [x for x in app_names if x] if app_names else []
        self.app_name = ":".join(self.app_names)
        self.namespaces = [x for x in namespaces if x] if namespaces else []
        self.namespace = ":".join(self.namespaces)

        if hasattr(func, "view_class"):
            func = func.view_class
        if not hasattr(func, "__name__"):
            # A class-based view
            self._func_path = func.__class__.__module__ + "." + func.__class__.__name__
        else:
            # A function-based view
            self._func_path = func.__module__ + "." + func.__name__

        view_path = url_name or self._func_path
        self.view_name = ":".join([*self.namespaces, view_path])

    def __getitem__(self, index):
        return (self.func, self.args, self.kwargs)[index]

    def __repr__(self):
        if isinstance(self.func, functools.partial):
            func = repr(self.func)
        else:
            func = self._func_path
        return (
            "ResolverMatch(func=%s, args=%r, kwargs=%r, url_name=%r, "
            "app_names=%r, namespaces=%r, route=%r%s%s)"
            % (
                func,
                self.args,
                self.kwargs,
                self.url_name,
                self.app_names,
                self.namespaces,
                self.route,
                (
                    f", captured_kwargs={self.captured_kwargs!r}"
                    if self.captured_kwargs
                    else ""
                ),
                f", extra_kwargs={self.extra_kwargs!r}" if self.extra_kwargs else "",
            )
        )

    def __reduce_ex__(self, protocol):
        raise PicklingError(f"Cannot pickle {self.__class__.__qualname__}.")


def get_resolver(urlconf=None):
    if urlconf is None:
        urlconf = settings.ROOT_URLCONF
    return _get_cached_resolver(urlconf)


@functools.cache
def _get_cached_resolver(urlconf=None):
    return URLResolver(RegexPattern(r"^/"), urlconf)


@functools.cache
def get_ns_resolver(ns_pattern, resolver, converters):
    # Build a namespaced resolver for the given parent URLconf pattern.
    # This makes it possible to have captured parameters in the parent
    # URLconf pattern.
    pattern = RegexPattern(ns_pattern)
    pattern.converters = dict(converters)
    ns_resolver = URLResolver(pattern, resolver.url_patterns)
    return URLResolver(RegexPattern(r"^/"), [ns_resolver])


class LocaleRegexDescriptor: