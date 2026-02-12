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
    :param offset: Offset. If given, this value is added to the result, after it has
                   been divided by the scale factor.
    """

    name = "Kernel"

    def __init__(
        self,
        size: tuple[int, int],
        kernel: Sequence[float],
        scale: float | None = None,
        offset: float = 0,
    ) -> None:
        if scale is None:
            # default scale is sum of kernel
            scale = functools.reduce(lambda a, b: a + b, kernel)
        if size[0] * size[1] != len(kernel):
            msg = "not enough coefficients in kernel"
            raise ValueError(msg)
        self.filterargs = size, scale, offset, kernel


class RankFilter(Filter):
    """
    Create a rank filter.  The rank filter sorts all pixels in
    a window of the given size, and returns the ``rank``'th value.

    :param size: The kernel size, in pixels.
    :param rank: What pixel value to pick.  Use 0 for a min filter,
                 ``size * size / 2`` for a median filter, ``size * size - 1``
                 for a max filter, etc.
    """

    name = "Rank"

    def __init__(self, size: int, rank: int) -> None:
        self.size = size
        self.rank = rank

    def filter(self, image: _imaging.ImagingCore) -> _imaging.ImagingCore:
        if image.mode == "P":
            msg = "cannot filter palette images"
            raise ValueError(msg)
        image = image.expand(self.size // 2, self.size // 2)
        return image.rankfilter(self.size, self.rank)


class MedianFilter(RankFilter):
    """
    Create a median filter. Picks the median pixel value in a window with the
    given size.

    :param size: The kernel size, in pixels.
    """
