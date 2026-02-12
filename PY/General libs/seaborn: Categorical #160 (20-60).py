from seaborn import utils
from seaborn.utils import (
    desaturate,
    _check_argument,
    _draw_figure,
    _default_color,
    _get_patch_legend_artist,
    _get_transform_functions,
    _scatter_legend_artist,
    _version_predates,
)
from seaborn._compat import groupby_apply_include_groups
from seaborn._statistics import (
    EstimateAggregator,
    LetterValues,
    WeightedAggregator,
)
from seaborn.palettes import light_palette
from seaborn.axisgrid import FacetGrid, _facet_docs


__all__ = [
    "catplot",
    "stripplot", "swarmplot",
    "boxplot", "violinplot", "boxenplot",
    "pointplot", "barplot", "countplot",
]


class _CategoricalPlotter(VectorPlotter):

    wide_structure = {"x": "@columns", "y": "@values", "hue": "@columns"}
    flat_structure = {"y": "@values"}

    _legend_attributes = ["color"]

    def __init__(
        self,
        data=None,
        variables={},
        order=None,