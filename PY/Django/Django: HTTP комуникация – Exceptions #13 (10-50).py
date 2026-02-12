class FieldDoesNotExist(Exception):
    """The requested model field does not exist"""

    pass


class AppRegistryNotReady(Exception):
    """The django.apps registry is not populated yet"""

    pass


class ObjectDoesNotExist(Exception):
    """The requested object does not exist"""

    silent_variable_failure = True


class ObjectNotUpdated(Exception):
    """The updated object no longer exists."""


class MultipleObjectsReturned(Exception):
    """The query returned multiple objects when only one was expected."""

    pass


class SuspiciousOperation(Exception):
    """The user did something suspicious"""


class SuspiciousMultipartForm(SuspiciousOperation):
    """Suspect MIME request in multipart form data"""

    pass


class SuspiciousFileOperation(SuspiciousOperation):
    """A Suspicious filesystem operation was attempted"""
