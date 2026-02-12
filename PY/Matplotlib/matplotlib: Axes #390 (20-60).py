import matplotlib.inset as minset
import matplotlib.legend as mlegend
import matplotlib.lines as mlines
import matplotlib.markers as mmarkers
import matplotlib.mlab as mlab
import matplotlib.patches as mpatches
import matplotlib.path as mpath
import matplotlib.quiver as mquiver
import matplotlib.stackplot as mstack
import matplotlib.streamplot as mstream
import matplotlib.table as mtable
import matplotlib.text as mtext
import matplotlib.ticker as mticker
import matplotlib.transforms as mtransforms
import matplotlib.tri as mtri
import matplotlib.units as munits
from matplotlib import _api, _docstring, _preprocess_data, _style_helpers
from matplotlib.axes._base import (
    _AxesBase, _TransformedBoundsLocator, _process_plot_format)
from matplotlib.axes._secondary_axes import SecondaryAxis
from matplotlib.container import (
    BarContainer, ErrorbarContainer, PieContainer, StemContainer)
from matplotlib.text import Text
from matplotlib.transforms import _ScaledRotation

_log = logging.getLogger(__name__)


# The axes module contains all the wrappers to plotting functions.
# All the other methods should go in the _AxesBase class.


def _make_axes_method(func):
    """
    Patch the qualname for functions that are directly added to Axes.

    Some Axes functionality is defined in functions in other submodules.
    These are simply added as attributes to Axes. As a result, their
    ``__qualname__`` is e.g. only "table" and not "Axes.table". This
    function fixes that.
