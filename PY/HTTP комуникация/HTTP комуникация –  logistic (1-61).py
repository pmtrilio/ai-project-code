"""
Logistic Regression
"""

# Authors: The scikit-learn developers
# SPDX-License-Identifier: BSD-3-Clause

import numbers
import warnings
from numbers import Integral, Real

import numpy as np
from scipy import optimize

from sklearn._loss.loss import HalfBinomialLoss, HalfMultinomialLoss
from sklearn.base import _fit_context
from sklearn.linear_model._base import (
    BaseEstimator,
    LinearClassifierMixin,
    SparseCoefMixin,
)
from sklearn.linear_model._glm.glm import NewtonCholeskySolver
from sklearn.linear_model._linear_loss import LinearModelLoss
from sklearn.linear_model._sag import sag_solver
from sklearn.metrics import get_scorer, get_scorer_names
from sklearn.model_selection import check_cv
from sklearn.preprocessing import LabelEncoder
from sklearn.svm._base import _fit_liblinear
from sklearn.utils import (
    Bunch,
    check_array,
    check_consistent_length,
    check_random_state,
    compute_class_weight,
)
from sklearn.utils._param_validation import Hidden, Interval, StrOptions
from sklearn.utils.extmath import row_norms, softmax
from sklearn.utils.fixes import _get_additional_lbfgs_options_dict
from sklearn.utils.metadata_routing import (
    MetadataRouter,
    MethodMapping,
    _raise_for_params,
    _routing_enabled,
    process_routing,
)
from sklearn.utils.multiclass import check_classification_targets
from sklearn.utils.optimize import _check_optimize_result, _newton_cg
from sklearn.utils.parallel import Parallel, delayed
from sklearn.utils.validation import (
    _check_method_params,
    _check_sample_weight,
    check_is_fitted,
    validate_data,
)

_LOGISTIC_SOLVER_CONVERGENCE_MSG = (
    "Please also refer to the documentation for alternative solver options:\n"
    "    https://scikit-learn.org/stable/modules/linear_model.html"
    "#logistic-regression"
)
