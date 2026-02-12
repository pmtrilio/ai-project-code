
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

        Parameters
        ----------
        X : array-like of shape (n_samples, n_features)
            Training data, where `n_samples` is the number of samples
            and `n_features` is the number of features.

        y : array-like of shape (n_samples,), default=None
            The target variable for supervised learning problems.

        groups : array-like of shape (n_samples,), default=None
            Always ignored, exists for API compatibility.

        Yields
        ------
        train : ndarray
            The training set indices for that split.

        test : ndarray
            The testing set indices for that split.
        """
        if groups is not None:
            warnings.warn(
                f"The groups parameter is ignored by {self.__class__.__name__}",
                UserWarning,
            )
        return super().split(X, y, groups=groups)


class GroupsConsumerMixin(_MetadataRequester):
    """A Mixin to ``groups`` by default.

    This Mixin makes the object to request ``groups`` by default as ``True``.

    .. versionadded:: 1.3
    """