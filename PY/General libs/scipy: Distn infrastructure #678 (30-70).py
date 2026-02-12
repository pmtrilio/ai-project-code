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
              'examples': """\nExamples\n--------\n"""}

_doc_rvs = """\
rvs(%(shapes)s, loc=0, scale=1, size=1, random_state=None)
    Random variates.
"""
_doc_pdf = """\
pdf(x, %(shapes)s, loc=0, scale=1)
    Probability density function.
"""
_doc_logpdf = """\
logpdf(x, %(shapes)s, loc=0, scale=1)
    Log of the probability density function.
"""
_doc_pmf = """\
pmf(k, %(shapes)s, loc=0, scale=1)
    Probability mass function.
"""
_doc_logpmf = """\
logpmf(k, %(shapes)s, loc=0, scale=1)