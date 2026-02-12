#
# Author:  Travis Oliphant  2002-2011 with contributions from
#          SciPy Developers 2004-2011
#
from scipy._lib._util import getfullargspec_no_self as _getfullargspec

import sys
import keyword
import re
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