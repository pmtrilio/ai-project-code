
from pandas._libs import lib
from pandas._libs.parsers import STR_NA_VALUES
from pandas.errors import (
    AbstractMethodError,
    ParserWarning,
)
from pandas.util._decorators import (
    set_module,
)
from pandas.util._exceptions import find_stack_level
from pandas.util._validators import check_dtype_backend

from pandas.core.dtypes.common import (
    is_file_like,
    is_float,
    is_integer,
    is_list_like,
    pandas_dtype,
)

from pandas import Series
from pandas.core.frame import DataFrame
from pandas.core.indexes.api import RangeIndex

from pandas.io.common import (
    IOHandles,
    get_handle,
    stringify_path,
    validate_header_arg,
)
from pandas.io.parsers.arrow_parser_wrapper import ArrowParserWrapper
from pandas.io.parsers.base_parser import (
    ParserBase,
    is_index_col,
    parser_defaults,
)
from pandas.io.parsers.c_parser_wrapper import CParserWrapper
from pandas.io.parsers.python_parser import (
    FixedWidthFieldParser,
    PythonParser,
)

if TYPE_CHECKING:
    from collections.abc import (
        Callable,
        Hashable,
        Iterable,
        Mapping,
        Sequence,
    )
    from types import TracebackType

    from pandas._typing import (
        CompressionOptions,
        CSVEngine,
        DtypeArg,
        DtypeBackend,
        FilePath,
        HashableT,
        IndexLabel,
        ReadCsvBuffer,
        StorageOptions,
        UsecolsArgType,
    )

    class _read_shared(TypedDict, Generic[HashableT], total=False):
        # annotations shared between read_csv/fwf/table's overloads
        # NOTE: Keep in sync with the annotations of the implementation
        sep: str | None | lib.NoDefault
        delimiter: str | None | lib.NoDefault
        header: int | Sequence[int] | None | Literal["infer"]
        names: Sequence[Hashable] | None | lib.NoDefault
        index_col: IndexLabel | Literal[False] | None
        usecols: UsecolsArgType
        dtype: DtypeArg | None
        engine: CSVEngine | None
        converters: Mapping[HashableT, Callable] | None
        true_values: list | None
        false_values: list | None
        skipinitialspace: bool