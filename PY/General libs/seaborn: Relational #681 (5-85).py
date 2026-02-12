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
The default treatment of the `hue` (and to a lesser extent, `size`)
semantic, if present, depends on whether the variable is inferred to
represent "numeric" or "categorical" data. In particular, numeric variables
are represented with a sequential colormap by default, and the legend
entries show regular "ticks" with values that may or may not exist in the
data. This behavior can be controlled through various parameters, as
described and illustrated below.
    """,
))

_relational_docs = dict(

    # --- Shared function parameters
    data_vars="""
x, y : names of variables in `data` or vector data
    Input data variables; must be numeric. Can pass data directly or
    reference columns in `data`.
    """,
    data="""
data : DataFrame, array, or list of arrays
    Input data structure. If `x` and `y` are specified as names, this
    should be a "long-form" DataFrame containing those columns. Otherwise
    it is treated as "wide-form" data and grouping variables are ignored.
    See the examples for the various ways this parameter can be specified
    and the different effects of each.
    """,
    palette="""
palette : string, list, dict, or matplotlib colormap
    An object that determines how colors are chosen when `hue` is used.
    It can be the name of a seaborn palette or matplotlib colormap, a list
    of colors (anything matplotlib understands), a dict mapping levels
    of the `hue` variable to colors, or a matplotlib colormap object.
    """,
    hue_order="""
hue_order : list
    Specified order for the appearance of the `hue` variable levels,
    otherwise they are determined from the data. Not relevant when the
    `hue` variable is numeric.
    """,
    hue_norm="""