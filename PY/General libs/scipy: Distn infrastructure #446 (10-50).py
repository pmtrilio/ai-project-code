import types
import warnings
from itertools import zip_longest

from scipy._lib import doccer
from scipy._lib._docscrape import FunctionDoc
from ._distr_params import distcont, distdiscrete
from scipy._lib._util import check_random_state
import scipy._lib.array_api_extra as xpx

from scipy.special import comb, entr


# for root finding for continuous distribution ppf, and maximum likelihood
# estimation
from scipy import optimize

# for functions of continuous distributions (e.g. moments, entropy, cdf)
from scipy import integrate

# to approximate the pdf of a continuous distribution given its cdf
from scipy.stats._finite_differences import _derivative

# for scipy.stats.entropy. Attempts to import just that function or file
# have cause import problems
from scipy import stats

from numpy import (arange, putmask, ones, shape, ndarray, zeros, floor,
                   logical_and, log, sqrt, place, argmax, vectorize, asarray,
                   nan, inf, isinf, empty)

import numpy as np
from ._constants import _XMAX, _LOGXMAX
from ._censored_data import CensoredData
from scipy.stats._warnings_errors import FitError

# These are the docstring parts used for substitution in specific
# distribution docstrings

docheaders = {'methods': """\nMethods\n-------\n""",
              'notes': """\nNotes\n-----\n""",