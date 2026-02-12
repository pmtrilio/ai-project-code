#
# Copyright (c) 1997-2009 by Secret Labs AB.  All rights reserved.
# Copyright (c) 1995-2009 by Fredrik Lundh.
#
# See the README file for information on usage and redistribution.
#

from __future__ import annotations

import abc
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

logger = logging.getLogger(__name__)


class DecompressionBombWarning(RuntimeWarning):
    pass


class DecompressionBombError(Exception):
    pass


WARN_POSSIBLE_FORMATS: bool = False

# Limit to around a quarter gigabyte for a 24-bit (3 bpp) image
MAX_IMAGE_PIXELS: int | None = int(1024 * 1024 * 1024 // 4 // 3)


try:
    # If the _imaging C module is not present, Pillow will not load.
    # Note that other modules should not refer to _imaging directly;
    # import Image and use the Image.core variable instead.
    # Also note that Image.core is not a publicly documented interface,
    # and should be considered private and subject to change.
    from . import _imaging as core

    if __version__ != getattr(core, "PILLOW_VERSION", None):
        msg = (
            "The _imaging extension was built for another version of Pillow or PIL:\n"
            f"Core version: {getattr(core, 'PILLOW_VERSION', None)}\n"