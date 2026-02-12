import dataclasses
import hashlib
import json
import os
import sys
import traceback
import warnings
from collections.abc import (
    Awaitable,
    Callable,
    Collection,
    Coroutine,
    Generator,
    Iterable,
    Mapping,
    Sequence,
)
from contextlib import suppress
from types import TracebackType
from typing import (
    TYPE_CHECKING,
    Any,
    Final,
    Generic,
    Literal,
    TypedDict,
    TypeVar,
    final,
    overload,
)

from multidict import CIMultiDict, MultiDict, MultiDictProxy, istr
from yarl import URL, Query

from . import hdrs, http, payload
from ._websocket.reader import WebSocketDataQueue
from .abc import AbstractCookieJar
from .client_exceptions import (
    ClientConnectionError,
    ClientConnectionResetError,
    ClientConnectorCertificateError,