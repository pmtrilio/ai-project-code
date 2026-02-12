from typing import overload
from typing import TYPE_CHECKING
from typing import TypeVar
import warnings

import _pytest
from _pytest import nodes
from _pytest._code import getfslineno
from _pytest._code import Source
from _pytest._code.code import FormattedExcinfo
from _pytest._code.code import TerminalRepr
from _pytest._io import TerminalWriter
from _pytest.compat import assert_never
from _pytest.compat import get_real_func
from _pytest.compat import getfuncargnames
from _pytest.compat import getimfunc
from _pytest.compat import getlocation
from _pytest.compat import NOTSET
from _pytest.compat import NotSetType
from _pytest.compat import safe_getattr
from _pytest.compat import safe_isclass
from _pytest.compat import signature
from _pytest.config import _PluggyPlugin
from _pytest.config import Config
from _pytest.config import ExitCode
from _pytest.config.argparsing import Parser
from _pytest.deprecated import check_ispytest
from _pytest.deprecated import YIELD_FIXTURE
from _pytest.main import Session
from _pytest.mark import ParameterSet
from _pytest.mark.structures import MarkDecorator
from _pytest.outcomes import fail
from _pytest.outcomes import skip
from _pytest.outcomes import TEST_OUTCOME
from _pytest.pathlib import absolutepath
from _pytest.pathlib import bestrelpath
from _pytest.scope import _ScopeName
from _pytest.scope import HIGH_SCOPES
from _pytest.scope import Scope
from _pytest.warning_types import PytestWarning


if sys.version_info < (3, 11):
    from exceptiongroup import BaseExceptionGroup


if TYPE_CHECKING:
    from _pytest.python import CallSpec2
    from _pytest.python import Function
    from _pytest.python import Metafunc


# The value of the fixture -- return/yield of the fixture function (type variable).
FixtureValue = TypeVar("FixtureValue", covariant=True)
# The type of the fixture function (type variable).
FixtureFunction = TypeVar("FixtureFunction", bound=Callable[..., object])
# The type of a fixture function (type alias generic in fixture value).
_FixtureFunc = Callable[..., FixtureValue] | Callable[..., Generator[FixtureValue]]
# The type of FixtureDef.cached_result (type alias generic in fixture value).
_FixtureCachedResult = (
    tuple[
        # The result.
        FixtureValue,
        # Cache key.
        object,
        None,
    ]
    | tuple[
        None,
        # Cache key.
        object,
        # The exception and the original traceback.
        tuple[BaseException, types.TracebackType | None],
    ]
)


def pytest_sessionstart(session: Session) -> None:
    session._fixturemanager = FixtureManager(session)


def get_scope_package(
    node: nodes.Item,
    fixturedef: FixtureDef[object],
) -> nodes.Node | None:
    from _pytest.python import Package

    for parent in node.iter_parents():
        if isinstance(parent, Package) and parent.nodeid == fixturedef.baseid:
            return parent
    return node.session


def get_scope_node(node: nodes.Node, scope: Scope) -> nodes.Node | None:
    """Get the closest parent node (including self) which matches the given
    scope.

    If there is no parent node for the scope (e.g. asking for class scope on a
    Module, or on a Function when not defined in a class), returns None.
    """
    import _pytest.python