    SparseCoefMixin,
)
from sklearn.linear_model._glm.glm import NewtonCholeskySolver
from sklearn.linear_model._linear_loss import LinearModelLoss
from sklearn.linear_model._sag import sag_solver
from sklearn.metrics import get_scorer, get_scorer_names
from sklearn.model_selection import check_cv
from sklearn.preprocessing import LabelEncoder
from sklearn.svm._base import _fit_liblinear
from sklearn.utils import (
    Bunch,
    check_array,
    check_consistent_length,
    check_random_state,
    compute_class_weight,
)
from sklearn.utils._param_validation import Hidden, Interval, StrOptions
from sklearn.utils.extmath import row_norms, softmax
from sklearn.utils.fixes import _get_additional_lbfgs_options_dict
from sklearn.utils.metadata_routing import (
    MetadataRouter,
    MethodMapping,
    _raise_for_params,
    _routing_enabled,
    process_routing,
)
from sklearn.utils.multiclass import check_classification_targets
from sklearn.utils.optimize import _check_optimize_result, _newton_cg
from sklearn.utils.parallel import Parallel, delayed
from sklearn.utils.validation import (
    _check_method_params,
    _check_sample_weight,
    check_is_fitted,
    validate_data,
)

_LOGISTIC_SOLVER_CONVERGENCE_MSG = (
    "Please also refer to the documentation for alternative solver options:\n"
    "    https://scikit-learn.org/stable/modules/linear_model.html"
    "#logistic-regression"
)


def _check_solver(solver, penalty, dual):
    if solver not in ["liblinear", "saga"] and penalty not in ("l2", None):
        raise ValueError(
            f"Solver {solver} supports only 'l2' or None penalties, got {penalty} "
            "penalty."
        )
    if solver != "liblinear" and dual:
        raise ValueError(f"Solver {solver} supports only dual=False, got dual={dual}")

    if penalty == "elasticnet" and solver != "saga":
        raise ValueError(
            f"Only 'saga' solver supports elasticnet penalty, got solver={solver}."
        )

    if solver == "liblinear" and penalty is None:
        # TODO(1.10): update message to remove "as well as penalty=None".
        raise ValueError(
            "C=np.inf as well as penalty=None is not supported for the liblinear solver"
        )

    return solver


def _logistic_regression_path(
    X,
    y,
    *,
    classes,
    Cs=10,
    fit_intercept=True,
    max_iter=100,
    tol=1e-4,
    verbose=0,
    solver="lbfgs",
    coef=None,
    class_weight=None,
    dual=False,
    penalty="l2",