# standard filters
#
# History:
# 1995-11-27 fl   Created
# 2002-06-08 fl   Added rank and mode filters
# 2003-09-15 fl   Fixed rank calculation in rank filter; added expand call
#
# Copyright (c) 1997-2003 by Secret Labs AB.
# Copyright (c) 1995-2002 by Fredrik Lundh.
#
# See the README file for information on usage and redistribution.
#
from __future__ import annotations

import abc
import functools
from collections.abc import Sequence
from typing import cast

TYPE_CHECKING = False
if TYPE_CHECKING:
    from collections.abc import Callable
    from types import ModuleType
    from typing import Any

    from . import _imaging
    from ._typing import NumpyArray


class Filter(abc.ABC):
    @abc.abstractmethod
    def filter(self, image: _imaging.ImagingCore) -> _imaging.ImagingCore:
        pass


class MultibandFilter(Filter):
    pass


class BuiltinFilter(MultibandFilter):
    filterargs: tuple[Any, ...]

    def filter(self, image: _imaging.ImagingCore) -> _imaging.ImagingCore:
        if image.mode == "P":
            msg = "cannot filter palette images"
            raise ValueError(msg)
        return image.filter(*self.filterargs)


class Kernel(BuiltinFilter):
    """
    Create a convolution kernel. This only supports 3x3 and 5x5 integer and floating
    point kernels.

    Kernels can only be applied to "L" and "RGB" images.

    :param size: Kernel size, given as (width, height). This must be (3,3) or (5,5).
    :param kernel: A sequence containing kernel weights. The kernel will be flipped
                   vertically before being applied to the image.
    :param scale: Scale factor. If given, the result for each pixel is divided by this
                  value. The default is the sum of the kernel weights.