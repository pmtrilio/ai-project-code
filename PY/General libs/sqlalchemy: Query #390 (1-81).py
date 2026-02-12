# orm/query.py
# Copyright (C) 2005-2026 the SQLAlchemy authors and contributors
# <see AUTHORS file>
#
# This module is part of SQLAlchemy and is released under
# the MIT License: https://www.opensource.org/licenses/mit-license.php

"""The Query class and support.

Defines the :class:`_query.Query` class, the central
construct used by the ORM to construct database queries.

The :class:`_query.Query` class should not be confused with the
:class:`_expression.Select` class, which defines database
SELECT operations at the SQL (non-ORM) level.  ``Query`` differs from
``Select`` in that it returns ORM-mapped objects and interacts with an
ORM session, whereas the ``Select`` construct interacts directly with the
database to return iterable result sets.

"""
from __future__ import annotations

import collections.abc as collections_abc
import operator
from typing import Any
from typing import Callable
from typing import cast
from typing import Dict
from typing import Generic
from typing import Iterable
from typing import Iterator
from typing import List
from typing import Literal
from typing import Mapping
from typing import Optional
from typing import overload
from typing import Sequence
from typing import Tuple
from typing import Type
from typing import TYPE_CHECKING
from typing import TypeVar
from typing import Union

from . import attributes
from . import interfaces
from . import loading
from . import util as orm_util
from ._typing import _O
from .base import _assertions
from .context import _column_descriptions
from .context import _determine_last_joined_entity
from .context import _legacy_filter_by_entity_zero
from .context import _ORMCompileState
from .context import FromStatement
from .context import QueryContext
from .interfaces import ORMColumnDescription
from .interfaces import ORMColumnsClauseRole
from .util import AliasedClass
from .util import object_mapper
from .util import with_parent
from .. import exc as sa_exc
from .. import inspect
from .. import inspection
from .. import log
from .. import sql
from .. import util
from ..engine import Result
from ..engine import Row
from ..event import dispatcher
from ..event import EventTarget
from ..sql import coercions
from ..sql import expression
from ..sql import roles
from ..sql import Select
from ..sql import util as sql_util
from ..sql import visitors
from ..sql._typing import _FromClauseArgument
from ..sql.annotation import SupportsCloneAnnotations
from ..sql.base import _entity_namespace_key
from ..sql.base import _generative
from ..sql.base import _NoArg