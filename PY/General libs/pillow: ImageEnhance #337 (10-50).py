#
# History:
# 1996-03-23 fl  Created
# 2009-06-16 fl  Fixed mean calculation
#
# Copyright (c) Secret Labs AB 1997.
# Copyright (c) Fredrik Lundh 1996.
#
# See the README file for information on usage and redistribution.
#
from __future__ import annotations

from . import Image, ImageFilter, ImageStat


class _Enhance:
    image: Image.Image
    degenerate: Image.Image

    def enhance(self, factor: float) -> Image.Image:
        """
        Returns an enhanced image.

        :param factor: A floating point value controlling the enhancement.
                       Factor 1.0 always returns a copy of the original image,
                       lower factors mean less color (brightness, contrast,
                       etc), and higher values more. There are no restrictions
                       on this value.
        :rtype: :py:class:`~PIL.Image.Image`
        """
        return Image.blend(self.degenerate, self.image, factor)


class Color(_Enhance):
    """Adjust image color balance.

    This class can be used to adjust the colour balance of an image, in
    a manner similar to the controls on a colour TV set. An enhancement
    factor of 0.0 gives a black and white image. A factor of 1.0 gives
    the original image.
    """