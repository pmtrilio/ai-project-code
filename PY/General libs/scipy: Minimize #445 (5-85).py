---------
- minimize : minimization of a function of several variables.
- minimize_scalar : minimization of a function of one variable.
"""

__all__ = ['minimize', 'minimize_scalar']


from warnings import warn

import numpy as np
from scipy._lib._util import wrapped_inspect_signature

# unconstrained minimization
from ._optimize import (_minimize_neldermead, _minimize_powell, _minimize_cg,
                        _minimize_bfgs, _minimize_newtoncg,
                        _minimize_scalar_brent, _minimize_scalar_bounded,
                        _minimize_scalar_golden, MemoizeJac, OptimizeResult,
                        _wrap_callback, _recover_from_bracket_error)
from ._trustregion_dogleg import _minimize_dogleg
from ._trustregion_ncg import _minimize_trust_ncg
from ._trustregion_krylov import _minimize_trust_krylov
from ._trustregion_exact import _minimize_trustregion_exact
from ._trustregion_constr import _minimize_trustregion_constr

# constrained minimization
from ._lbfgsb_py import _minimize_lbfgsb
from ._tnc import _minimize_tnc
from ._cobyla_py import _minimize_cobyla
from ._cobyqa_py import _minimize_cobyqa
from ._slsqp_py import _minimize_slsqp
from ._constraints import (old_bound_to_new, new_bounds_to_old,
                           old_constraint_to_new, new_constraint_to_old,
                           NonlinearConstraint, LinearConstraint, Bounds,
                           PreparedConstraint)
from ._differentiable_functions import FD_METHODS

MINIMIZE_METHODS = ['nelder-mead', 'powell', 'cg', 'bfgs', 'newton-cg',
                    'l-bfgs-b', 'tnc', 'cobyla', 'cobyqa', 'slsqp',
                    'trust-constr', 'dogleg', 'trust-ncg', 'trust-exact',
                    'trust-krylov']

# These methods support the new callback interface (passed an OptimizeResult)
MINIMIZE_METHODS_NEW_CB = ['nelder-mead', 'powell', 'cg', 'bfgs', 'newton-cg',
                           'l-bfgs-b', 'trust-constr', 'dogleg', 'trust-ncg',
                           'trust-exact', 'trust-krylov', 'cobyqa', 'cobyla', 'slsqp']

MINIMIZE_SCALAR_METHODS = ['brent', 'bounded', 'golden']

def minimize(fun, x0, args=(), method=None, jac=None, hess=None,
             hessp=None, bounds=None, constraints=(), tol=None,
             callback=None, options=None):
    """Minimization of scalar function of one or more variables.

    Parameters
    ----------
    fun : callable
        The objective function to be minimized::

            fun(x, *args) -> float

        where ``x`` is a 1-D array with shape (n,) and ``args``
        is a tuple of the fixed parameters needed to completely
        specify the function.

        Suppose the callable has signature ``f0(x, *my_args, **my_kwargs)``, where
        ``my_args`` and ``my_kwargs`` are required positional and keyword arguments.
        Rather than passing ``f0`` as the callable, wrap it to accept
        only ``x``; e.g., pass ``fun=lambda x: f0(x, *my_args, **my_kwargs)`` as the
        callable, where ``my_args`` (tuple) and ``my_kwargs`` (dict) have been
        gathered before invoking this function.
    x0 : ndarray, shape (n,)
        Initial guess. Array of real elements of size (n,),
        where ``n`` is the number of independent variables.
    args : tuple, optional
        Extra arguments passed to the objective function and its
        derivatives (`fun`, `jac` and `hess` functions).
    method : str or callable, optional
        Type of solver.  Should be one of

        - 'Nelder-Mead' :ref:`(see here) <optimize.minimize-neldermead>`