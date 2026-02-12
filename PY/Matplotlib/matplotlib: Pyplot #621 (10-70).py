an implicit,  MATLAB-like, way of plotting.  It also opens figures on your
screen, and acts as the figure GUI manager.

pyplot is mainly intended for interactive plots and simple cases of
programmatic plot generation::

    import numpy as np
    import matplotlib.pyplot as plt

    x = np.arange(0, 5, 0.1)
    y = np.sin(x)
    plt.plot(x, y)
    plt.show()

The explicit object-oriented API is recommended for complex plots, though
pyplot is still usually used to create the figure and often the Axes in the
figure. See `.pyplot.figure`, `.pyplot.subplots`, and
`.pyplot.subplot_mosaic` to create figures, and
:doc:`Axes API </api/axes_api>` for the plotting methods on an Axes::

    import numpy as np
    import matplotlib.pyplot as plt

    x = np.arange(0, 5, 0.1)
    y = np.sin(x)
    fig, ax = plt.subplots()
    ax.plot(x, y)
    plt.show()


See :ref:`api_interfaces` for an explanation of the tradeoffs between the
implicit and explicit interfaces.
"""

# fmt: off

from __future__ import annotations

from contextlib import AbstractContextManager, ExitStack
from enum import Enum
import functools
import importlib
import inspect
import logging
import sys
import threading
import time
from typing import IO, TYPE_CHECKING, cast, overload

from cycler import cycler  # noqa: F401
import matplotlib
import matplotlib.image
from matplotlib import _api
# Re-exported (import x as x) for typing.
from matplotlib import get_backend as get_backend, rcParams as rcParams
from matplotlib import cm as cm  # noqa: F401
from matplotlib import style as style  # noqa: F401
from matplotlib import _pylab_helpers
from matplotlib import interactive  # noqa: F401
from matplotlib import cbook
from matplotlib import _docstring