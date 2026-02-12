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
            orig_x = self.variables.pop("x", None)
            orig_y = self.variables.pop("y", None)
            orig_x_type = self.var_types.pop("x", None)
            orig_y_type = self.var_types.pop("y", None)
            if "x" in orig_variables:
                self.variables["y"] = orig_x
                self.var_types["y"] = orig_x_type
            if "y" in orig_variables:
                self.variables["x"] = orig_y
                self.var_types["x"] = orig_y_type

        # Initially there was more special code for wide-form data where plots were
        # multi-colored by default and then either palette or color could be used.
        # We want to provide backwards compatibility for this behavior in a relatively
        # simply way, so we delete the hue information when color is specified.
        if (
            self.input_format == "wide"
            and "hue" in self.variables
            and color is not None
        ):
            self.plot_data.drop("hue", axis=1)
            self.variables.pop("hue")

        # The concept of an "orientation" is important to the original categorical
        # plots, but there's no provision for it in VectorPlotter, so we need it here.
        # Note that it could be useful for the other functions in at least two ways
        # (orienting a univariate distribution plot from long-form data and selecting
        # the aggregation axis in lineplot), so we may want to eventually refactor it.
        self.orient = infer_orient(
            x=self.plot_data.get("x", None),
            y=self.plot_data.get("y", None),
            orient=orient,
            require_numeric=False,
        )

        self.legend = legend

        # Short-circuit in the case of an empty plot
        if not self.has_xy_data:
            return

        # Categorical plots can be "univariate" in which case they get an anonymous
        # category label on the opposite axis. Note: this duplicates code in the core
        # scale_categorical function. We need to do it here because of the next line.
        if self.orient not in self.variables:
            self.variables[self.orient] = None
            self.var_types[self.orient] = "categorical"
            self.plot_data[self.orient] = ""

        # Categorical variables have discrete levels that we need to track