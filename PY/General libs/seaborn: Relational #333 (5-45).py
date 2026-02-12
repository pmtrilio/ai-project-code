import pandas as pd
import matplotlib as mpl
import matplotlib.pyplot as plt
from matplotlib.cbook import normalize_kwargs

from ._base import (
    VectorPlotter,
)
from .utils import (
    adjust_legend_subtitles,
    _default_color,
    _deprecate_ci,
    _get_transform_functions,
    _scatter_legend_artist,
)
from ._compat import groupby_apply_include_groups
from ._statistics import EstimateAggregator, WeightedAggregator
from .axisgrid import FacetGrid, _facet_docs
from ._docstrings import DocstringComponents, _core_docs


__all__ = ["relplot", "scatterplot", "lineplot"]


_relational_narrative = DocstringComponents(dict(

    # ---  Introductory prose
    main_api="""
The relationship between `x` and `y` can be shown for different subsets
of the data using the `hue`, `size`, and `style` parameters. These
parameters control what visual semantics are used to identify the different
subsets. It is possible to show up to three dimensions independently by
using all three semantic types, but this style of plot can be hard to
interpret and is often ineffective. Using redundant semantics (i.e. both
`hue` and `style` for the same variable) can be helpful for making
graphics more accessible.

See the :ref:`tutorial <relational_tutorial>` for more information.
    """,

    relational_semantic="""