# Authors: The scikit-learn developers
# SPDX-License-Identifier: BSD-3-Clause


import warnings
from numbers import Integral, Real

import numpy as np
from scipy import sparse, stats
from scipy.special import boxcox, inv_boxcox

from sklearn.base import (
    BaseEstimator,
    ClassNamePrefixFeaturesOutMixin,
    OneToOneFeatureMixin,
    TransformerMixin,
    _fit_context,
)
from sklearn.preprocessing._encoders import OneHotEncoder
from sklearn.utils import _array_api, check_array, metadata_routing, resample
from sklearn.utils._array_api import (
    _find_matching_floating_dtype,
    _max_precision_float_dtype,
    _modify_in_place_if_numpy,
    device,
    get_namespace,
    get_namespace_and_device,
    size,
    supported_float_dtypes,
)
from sklearn.utils._param_validation import (
    Interval,
    Options,
    StrOptions,
    validate_params,
)
from sklearn.utils.extmath import _incremental_mean_and_var, row_norms
from sklearn.utils.sparsefuncs import (
    incr_mean_variance_axis,
    inplace_column_scale,
    mean_variance_axis,
    min_max_axis,
)
from sklearn.utils.sparsefuncs_fast import (
    inplace_csr_row_normalize_l1,
    inplace_csr_row_normalize_l2,
)
from sklearn.utils.validation import (
    FLOAT_DTYPES,
    _check_sample_weight,
    check_is_fitted,
    check_random_state,
    validate_data,
)

BOUNDS_THRESHOLD = 1e-7

__all__ = [
    "Binarizer",
    "KernelCenterer",
    "MaxAbsScaler",
    "MinMaxScaler",
    "Normalizer",
    "OneHotEncoder",
    "PowerTransformer",
    "QuantileTransformer",
    "RobustScaler",
    "StandardScaler",
    "add_dummy_feature",
    "binarize",
    "maxabs_scale",
    "minmax_scale",
    "normalize",
    "power_transform",
    "quantile_transform",
    "robust_scale",
    "scale",
]


def _is_constant_feature(var, mean, n_samples):
    """Detect if a feature is indistinguishable from a constant feature.

    The detection is based on its computed variance and on the theoretical
    error bounds of the '2 pass algorithm' for variance computation.

    See "Algorithms for computing the sample variance: analysis and
    recommendations", by Chan, Golub, and LeVeque.
    """
    # In scikit-learn, variance is always computed using float64 accumulators.
    xp, _, device_ = get_namespace_and_device(var, mean)
    max_float_dtype = _max_precision_float_dtype(xp=xp, device=device_)
    eps = xp.finfo(max_float_dtype).eps

    upper_bound = n_samples * eps * var + (n_samples * mean * eps) ** 2
    return var <= upper_bound


def _handle_zeros_in_scale(scale, copy=True, constant_mask=None):
    """Set scales of near constant features to 1.
