"""
Forest of trees-based ensemble methods.

Those methods include random forests and extremely randomized trees.

The module structure is the following:

- The ``BaseForest`` base class implements a common ``fit`` method for all
  the estimators in the module. The ``fit`` method of the base ``Forest``
  class calls the ``fit`` method of each sub-estimator on random samples
  (with replacement, a.k.a. bootstrap) of the training set.

  The init of the sub-estimator is further delegated to the
  ``BaseEnsemble`` constructor.

- The ``ForestClassifier`` and ``ForestRegressor`` base classes further
  implement the prediction logic by computing an average of the predicted
  outcomes of the sub-estimators.

- The ``RandomForestClassifier`` and ``RandomForestRegressor`` derived
  classes provide the user with concrete implementations of
  the forest ensemble method using classical, deterministic
  ``DecisionTreeClassifier`` and ``DecisionTreeRegressor`` as
  sub-estimator implementations.

- The ``ExtraTreesClassifier`` and ``ExtraTreesRegressor`` derived
  classes provide the user with concrete implementations of the
  forest ensemble method using the extremely randomized trees
  ``ExtraTreeClassifier`` and ``ExtraTreeRegressor`` as
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
]

MAX_INT = np.iinfo(np.int32).max


def _generate_sample_indices(
    random_state, n_samples, n_samples_bootstrap, sample_weight
):
    """
    Private function used to _parallel_build_trees function."""
