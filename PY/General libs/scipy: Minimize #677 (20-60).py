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