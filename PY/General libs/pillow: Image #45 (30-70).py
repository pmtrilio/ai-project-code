import atexit
import builtins
import io
import logging
import math
import os
import re
import struct
import sys
import tempfile
import warnings
from collections.abc import MutableMapping
from enum import IntEnum
from typing import IO, Protocol, cast

# VERSION was removed in Pillow 6.0.0.
# PILLOW_VERSION was removed in Pillow 9.0.0.
# Use __version__ instead.
from . import (
    ExifTags,
    ImageMode,
    TiffTags,
    UnidentifiedImageError,
    __version__,
    _plugins,
)
from ._binary import i32le, o32be, o32le
from ._deprecate import deprecate
from ._util import DeferredError, is_path

ElementTree: ModuleType | None
try:
    from defusedxml import ElementTree
except ImportError:
    ElementTree = None

TYPE_CHECKING = False
if TYPE_CHECKING:
    from collections.abc import Callable, Iterator, Sequence
    from types import ModuleType
    from typing import Any, Literal