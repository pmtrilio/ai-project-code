  sub-estimator implementations.

Single and multi-output problems are both handled.
"""

# Authors: The scikit-learn developers
# SPDX-License-Identifier: BSD-3-Clause

import threading
from abc import ABCMeta, abstractmethod
from numbers import Integral
from warnings import warn

import numpy as np
from scipy.sparse import hstack as sparse_hstack
from scipy.sparse import issparse

from sklearn.base import (
    ClassifierMixin,
    MultiOutputMixin,
    RegressorMixin,
    TransformerMixin,
    _fit_context,
    is_classifier,
)
from sklearn.ensemble._base import BaseEnsemble, _partition_estimators
from sklearn.ensemble._bootstrap import _get_n_samples_bootstrap
from sklearn.exceptions import DataConversionWarning
from sklearn.metrics import accuracy_score, r2_score
from sklearn.preprocessing import OneHotEncoder
from sklearn.tree import (
    BaseDecisionTree,
    DecisionTreeClassifier,
    DecisionTreeRegressor,
    ExtraTreeClassifier,
    ExtraTreeRegressor,
)
from sklearn.tree._tree import DOUBLE, DTYPE
from sklearn.utils import (
    check_random_state,
    compute_class_weight,
    compute_sample_weight,
)
from sklearn.utils._param_validation import Interval, RealNotInt, StrOptions
from sklearn.utils._tags import get_tags
from sklearn.utils.multiclass import check_classification_targets, type_of_target
from sklearn.utils.parallel import Parallel, delayed
from sklearn.utils.validation import (
    _check_feature_names_in,
    _check_sample_weight,
    _num_samples,
    check_is_fitted,
    validate_data,
)

__all__ = [
    "ExtraTreesClassifier",
    "ExtraTreesRegressor",
    "RandomForestClassifier",
    "RandomForestRegressor",
    "RandomTreesEmbedding",