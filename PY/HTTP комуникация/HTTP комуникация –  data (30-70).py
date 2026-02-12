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