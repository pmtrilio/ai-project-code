"""
Module contains tools for processing files into DataFrames or other objects

GH#48849 provides a convenient way of deprecating keyword arguments
"""

from __future__ import annotations

from collections import (
    abc,
    defaultdict,
)
import csv
import sys
from typing import (
    IO,
    TYPE_CHECKING,
    Any,
    Generic,
    Literal,
    Self,
    TypedDict,
    Unpack,
    cast,
    overload,
)
import warnings

import numpy as np

from pandas._libs import lib
from pandas._libs.parsers import STR_NA_VALUES
from pandas.errors import (
    AbstractMethodError,
    ParserWarning,
)
from pandas.util._decorators import (
    set_module,
)
from pandas.util._exceptions import find_stack_level
from pandas.util._validators import check_dtype_backend