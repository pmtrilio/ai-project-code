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

    def __init__(self, image: Image.Image) -> None:
        self.image = image
        self.intermediate_mode = "L"
        if "A" in image.getbands():
            self.intermediate_mode = "LA"

        if self.intermediate_mode != image.mode:
            image = image.convert(self.intermediate_mode).convert(image.mode)
        self.degenerate = image


class Contrast(_Enhance):
    """Adjust image contrast.

    This class can be used to control the contrast of an image, similar
    to the contrast control on a TV set. An enhancement factor of 0.0
    gives a solid gray image. A factor of 1.0 gives the original image.
    """

    def __init__(self, image: Image.Image) -> None:
        self.image = image
        if image.mode != "L":
            image = image.convert("L")
        mean = int(ImageStat.Stat(image).mean[0] + 0.5)
        self.degenerate = Image.new("L", image.size, mean)
        if self.degenerate.mode != self.image.mode:
            self.degenerate = self.degenerate.convert(self.image.mode)

        if "A" in self.image.getbands():