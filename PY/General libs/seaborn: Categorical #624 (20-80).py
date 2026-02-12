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
        orient=None,
        require_numeric=False,
        color=None,
        legend="auto",
    ):

        super().__init__(data=data, variables=variables)

        # This method takes care of some bookkeeping that is necessary because the
        # original categorical plots (prior to the 2021 refactor) had some rules that
        # don't fit exactly into VectorPlotter logic. It may be wise to have a second
        # round of refactoring that moves the logic deeper, but this will keep things
        # relatively sensible for now.

        # For wide data, orient determines assignment to x/y differently from the
        # default VectorPlotter rules. If we do decide to make orient part of the
        # _base variable assignment, we'll want to figure out how to express that.
        if self.input_format == "wide" and orient in ["h", "y"]:
            self.plot_data = self.plot_data.rename(columns={"x": "y", "y": "x"})
            orig_variables = set(self.variables)