    Iterable,
    Mapping,
    Sequence,
)
import functools
import operator
import sys
from textwrap import dedent
from typing import (
    IO,
    TYPE_CHECKING,
    Any,
    Literal,
    Self,
    cast,
    overload,
)
import warnings

import numpy as np

from pandas._libs import (
    lib,
    properties,
    reshape,
)
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

from pandas.core.dtypes.astype import astype_is_view
from pandas.core.dtypes.cast import (
    LossySetitemError,
    construct_1d_arraylike_from_scalar,
    find_common_type,