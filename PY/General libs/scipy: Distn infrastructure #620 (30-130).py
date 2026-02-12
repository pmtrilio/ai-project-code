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
    Log of the probability mass function.
"""
_doc_cdf = """\
cdf(x, %(shapes)s, loc=0, scale=1)
    Cumulative distribution function.
"""
_doc_logcdf = """\
logcdf(x, %(shapes)s, loc=0, scale=1)
    Log of the cumulative distribution function.
"""
_doc_sf = """\
sf(x, %(shapes)s, loc=0, scale=1)
    Survival function  (also defined as ``1 - cdf``, but `sf` is sometimes more accurate).
"""  # noqa: E501
_doc_logsf = """\
logsf(x, %(shapes)s, loc=0, scale=1)
    Log of the survival function.
"""
_doc_ppf = """\
ppf(q, %(shapes)s, loc=0, scale=1)
    Percent point function (inverse of ``cdf`` --- percentiles).
"""
_doc_isf = """\
isf(q, %(shapes)s, loc=0, scale=1)
    Inverse survival function (inverse of ``sf``).
"""
_doc_moment = """\
moment(order, %(shapes)s, loc=0, scale=1)
    Non-central moment of the specified order.
"""
_doc_stats = """\
stats(%(shapes)s, loc=0, scale=1, moments='mv')
    Mean('m'), variance('v'), skew('s'), and/or kurtosis('k').
"""
_doc_entropy = """\
entropy(%(shapes)s, loc=0, scale=1)
    (Differential) entropy of the RV.
"""
_doc_fit = """\
fit(data)
    Parameter estimates for generic data.
    See `scipy.stats.rv_continuous.fit <https://docs.scipy.org/doc/scipy/reference/generated/scipy.stats.rv_continuous.fit.html#scipy.stats.rv_continuous.fit>`__ for detailed documentation of the
    keyword arguments.
"""  # noqa: E501
_doc_expect = """\
expect(func, args=(%(shapes_)s), loc=0, scale=1, lb=None, ub=None, conditional=False, **kwds)
    Expected value of a function (of one argument) with respect to the distribution.
"""  # noqa: E501
_doc_expect_discrete = """\
expect(func, args=(%(shapes_)s), loc=0, lb=None, ub=None, conditional=False)
    Expected value of a function (of one argument) with respect to the distribution.
"""
_doc_median = """\
median(%(shapes)s, loc=0, scale=1)
    Median of the distribution.
"""
_doc_mean = """\
mean(%(shapes)s, loc=0, scale=1)
    Mean of the distribution.
"""