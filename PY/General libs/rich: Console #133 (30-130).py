    TextIO,
    Tuple,
    Type,
    Union,
    cast,
    runtime_checkable,
)

from rich._null_file import NULL_FILE

from . import errors, themes
from ._emoji_replace import _emoji_replace
from ._export_format import CONSOLE_HTML_FORMAT, CONSOLE_SVG_FORMAT
from ._fileno import get_fileno
from ._log_render import FormatTimeCallable, LogRender
from .align import Align, AlignMethod
from .color import ColorSystem, blend_rgb
from .control import Control
from .emoji import EmojiVariant
from .highlighter import NullHighlighter, ReprHighlighter
from .markup import render as render_markup
from .measure import Measurement, measure_renderables
from .pager import Pager, SystemPager
from .pretty import Pretty, is_expandable
from .protocol import rich_cast
from .region import Region
from .scope import render_scope
from .screen import Screen
from .segment import Segment
from .style import Style, StyleType
from .styled import Styled
from .terminal_theme import DEFAULT_TERMINAL_THEME, SVG_EXPORT_THEME, TerminalTheme
from .text import Text, TextType
from .theme import Theme, ThemeStack

if TYPE_CHECKING:
    from ._windows import WindowsConsoleFeatures
    from .live import Live
    from .status import Status

JUPYTER_DEFAULT_COLUMNS = 115
JUPYTER_DEFAULT_LINES = 100
WINDOWS = sys.platform == "win32"

HighlighterType = Callable[[Union[str, "Text"]], "Text"]
JustifyMethod = Literal["default", "left", "center", "right", "full"]
OverflowMethod = Literal["fold", "crop", "ellipsis", "ignore"]


class NoChange:
    pass


NO_CHANGE = NoChange()

try:
    _STDIN_FILENO = sys.__stdin__.fileno()  # type: ignore[union-attr]
except Exception:
    _STDIN_FILENO = 0
try:
    _STDOUT_FILENO = sys.__stdout__.fileno()  # type: ignore[union-attr]
except Exception:
    _STDOUT_FILENO = 1
try:
    _STDERR_FILENO = sys.__stderr__.fileno()  # type: ignore[union-attr]
except Exception:
    _STDERR_FILENO = 2

_STD_STREAMS = (_STDIN_FILENO, _STDOUT_FILENO, _STDERR_FILENO)
_STD_STREAMS_OUTPUT = (_STDOUT_FILENO, _STDERR_FILENO)


_TERM_COLORS = {
    "kitty": ColorSystem.EIGHT_BIT,
    "256color": ColorSystem.EIGHT_BIT,
    "16color": ColorSystem.STANDARD,
}


class ConsoleDimensions(NamedTuple):
    """Size of the terminal."""

    width: int
    """The width of the console in 'cells'."""
    height: int
    """The height of the console in lines."""


@dataclass
class ConsoleOptions:
    """Options for __rich_console__ method."""

    size: ConsoleDimensions
    """Size of console."""
    legacy_windows: bool
    """legacy_windows: flag for legacy windows."""
    min_width: int
    """Minimum width of renderable."""
    max_width: int
    """Maximum width of renderable."""
    is_terminal: bool