labeled data series.

Similar to its R counterpart, data.frame, except providing automatic data
alignment and a host of useful data manipulation methods having to do with the
labeling information
"""

from __future__ import annotations

import collections
from collections import abc
from collections.abc import (
    Callable,
    Hashable,
    Iterable,
    Iterator,
    Mapping,
    Sequence,
)
import functools
from io import StringIO
import itertools
import operator
import sys
from textwrap import dedent
from typing import (
    TYPE_CHECKING,
    Any,
    Literal,
    Self,
    cast,
    overload,
)
import warnings

import numpy as np
from numpy import ma

from pandas._config import get_option

from pandas._libs import (
    algos as libalgos,
    lib,
    properties,
)
from pandas._libs.hashtable import duplicated
from pandas._libs.lib import is_range_indexer
from pandas.compat import CHAINED_WARNING_DISABLED
from pandas.compat._constants import (
    REF_COUNT,
    REF_COUNT_METHOD,
)
from pandas.compat._optional import import_optional_dependency
from pandas.compat.numpy import function as nv
from pandas.errors import (
    ChainedAssignmentError,
    InvalidIndexError,
    Pandas4Warning,
)
from pandas.errors.cow import (
    _chained_assignment_method_update_msg,
    _chained_assignment_msg,
)
from pandas.util._decorators import (
    deprecate_nonkeyword_arguments,
    set_module,
)
from pandas.util._exceptions import (
    find_stack_level,
)
from pandas.util._validators import (
    validate_ascending,
    validate_bool_kwarg,
    validate_percentile,
)

from pandas.core.dtypes.cast import (
    LossySetitemError,
    can_hold_element,
    construct_1d_arraylike_from_scalar,
    construct_2d_arraylike_from_scalar,
    find_common_type,
    infer_dtype_from_scalar,
    invalidate_string_dtypes,
    maybe_downcast_to_dtype,
    maybe_unbox_numpy_scalar,
)
from pandas.core.dtypes.common import (
    infer_dtype_from_object,
    is_1d_only_ea_dtype,
    is_array_like,
    is_bool_dtype,
    is_dataclass,
    is_dict_like,
    is_float,
    is_float_dtype,
    is_hashable,
    is_integer,
    is_integer_dtype,
    is_iterator,
    is_list_like,