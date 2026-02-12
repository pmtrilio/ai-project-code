# engine/base.py
# Copyright (C) 2005-2026 the SQLAlchemy authors and contributors
# <see AUTHORS file>
#
# This module is part of SQLAlchemy and is released under
# the MIT License: https://www.opensource.org/licenses/mit-license.php
"""Defines :class:`_engine.Connection` and :class:`_engine.Engine`."""
from __future__ import annotations

import contextlib
import sys
import typing
from typing import Any
from typing import Callable
from typing import cast
from typing import Iterable
from typing import Iterator
from typing import List
from typing import Mapping
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