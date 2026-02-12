# 1996-04-30 fl   PIL release 0.1b1
# 1999-07-28 fl   PIL release 1.0 final
# 2000-06-07 fl   PIL release 1.1
# 2000-10-20 fl   PIL release 1.1.1
# 2001-05-07 fl   PIL release 1.1.2
# 2002-03-15 fl   PIL release 1.1.3
# 2003-05-10 fl   PIL release 1.1.4
# 2005-03-28 fl   PIL release 1.1.5
# 2006-12-02 fl   PIL release 1.1.6
# 2009-11-15 fl   PIL release 1.1.7
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