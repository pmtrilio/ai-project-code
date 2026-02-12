from typing import NoReturn
from typing import Optional
from typing import overload
from typing import Tuple
from typing import Type
from typing import TypeVar
from typing import Union

from .interfaces import BindTyping
from .interfaces import ConnectionEventsTarget
from .interfaces import DBAPICursor
from .interfaces import ExceptionContext
from .interfaces import ExecuteStyle
from .interfaces import ExecutionContext
from .interfaces import IsolationLevel
from .util import _distill_params_20
from .util import _distill_raw_params
from .util import TransactionalContext
from .. import exc
from .. import inspection
from .. import log
from .. import util
from ..sql import compiler
from ..sql import util as sql_util
from ..util.typing import TupleAny
from ..util.typing import TypeVarTuple
from ..util.typing import Unpack

if typing.TYPE_CHECKING:
    from . import CursorResult
    from . import ScalarResult
    from .interfaces import _AnyExecuteParams
    from .interfaces import _AnyMultiExecuteParams
    from .interfaces import _CoreAnyExecuteParams
    from .interfaces import _CoreMultiExecuteParams
    from .interfaces import _CoreSingleExecuteParams
    from .interfaces import _DBAPIAnyExecuteParams
    from .interfaces import _DBAPISingleExecuteParams
    from .interfaces import _ExecuteOptions
    from .interfaces import CompiledCacheType
    from .interfaces import CoreExecuteOptionsParameter
    from .interfaces import Dialect
    from .interfaces import SchemaTranslateMapType
    from .reflection import Inspector  # noqa
    from .url import URL
    from ..event import dispatcher
    from ..log import _EchoFlagType
    from ..pool import _ConnectionFairy
    from ..pool import Pool
    from ..pool import PoolProxiedConnection
    from ..sql import Executable
    from ..sql._typing import _InfoType
    from ..sql.compiler import Compiled
    from ..sql.ddl import ExecutableDDLElement
    from ..sql.ddl import InvokeDDLBase
    from ..sql.functions import FunctionElement
    from ..sql.schema import DefaultGenerator
    from ..sql.schema import HasSchemaAttr
    from ..sql.schema import SchemaVisitable
    from ..sql.selectable import TypedReturnsRows


_T = TypeVar("_T", bound=Any)
_Ts = TypeVarTuple("_Ts")
_EMPTY_EXECUTION_OPTS: _ExecuteOptions = util.EMPTY_DICT
NO_OPTIONS: Mapping[str, Any] = util.EMPTY_DICT


class Connection(ConnectionEventsTarget, inspection.Inspectable["Inspector"]):
    """Provides high-level functionality for a wrapped DB-API connection.

    The :class:`_engine.Connection` object is procured by calling the
    :meth:`_engine.Engine.connect` method of the :class:`_engine.Engine`
    object, and provides services for execution of SQL statements as well
    as transaction control.

    The Connection object is **not** thread-safe. While a Connection can be
    shared among threads using properly synchronized access, it is still
    possible that the underlying DBAPI connection may not support shared
    access between threads. Check the DBAPI documentation for details.

    The Connection object represents a single DBAPI connection checked out
    from the connection pool. In this state, the connection pool has no
    affect upon the connection, including its expiration or timeout state.
    For the connection pool to properly manage connections, connections
    should be returned to the connection pool (i.e. ``connection.close()``)
    whenever the connection is not in use.

    .. index::
      single: thread safety; Connection

    """

    dialect: Dialect
    dispatch: dispatcher[ConnectionEventsTarget]

    _sqla_logger_namespace = "sqlalchemy.engine.Connection"

    # used by sqlalchemy.engine.util.TransactionalContext
    _trans_context_manager: Optional[TransactionalContext] = None
