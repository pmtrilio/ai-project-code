
# Authors: The scikit-learn developers
# SPDX-License-Identifier: BSD-3-Clause

import numbers
import warnings
from abc import ABCMeta, abstractmethod
from collections import defaultdict
from collections.abc import Iterable
from inspect import signature
from itertools import chain, combinations
from math import ceil, floor

import numpy as np
from scipy.special import comb

from sklearn.utils import (
    _safe_indexing,
    check_random_state,
    indexable,
    metadata_routing,
)
from sklearn.utils._array_api import (
    _convert_to_numpy,
    get_namespace,
    get_namespace_and_device,
    move_to,
)
from sklearn.utils._param_validation import Interval, RealNotInt, validate_params
from sklearn.utils.extmath import _approximate_mode
from sklearn.utils.metadata_routing import _MetadataRequester
from sklearn.utils.multiclass import type_of_target
from sklearn.utils.validation import _num_samples, check_array, column_or_1d

__all__ = [
    "BaseCrossValidator",
    "GroupKFold",
    "GroupShuffleSplit",
    "KFold",
    "LeaveOneGroupOut",
    "LeaveOneOut",
    "LeavePGroupsOut",
    "LeavePOut",
    "PredefinedSplit",
    "RepeatedKFold",
    "RepeatedStratifiedKFold",
    "ShuffleSplit",
    "StratifiedGroupKFold",
    "StratifiedKFold",
    "StratifiedShuffleSplit",
    "check_cv",
    "train_test_split",
]


class _UnsupportedGroupCVMixin:
    """Mixin for splitters that do not support Groups."""

    def split(self, X, y=None, groups=None):
        """Generate indices to split data into training and test set.
