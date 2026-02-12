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