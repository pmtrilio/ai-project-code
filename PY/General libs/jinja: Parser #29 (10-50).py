from .lexer import describe_token_expr

if t.TYPE_CHECKING:
    import typing_extensions as te

    from .environment import Environment

_ImportInclude = t.TypeVar("_ImportInclude", nodes.Import, nodes.Include)
_MacroCall = t.TypeVar("_MacroCall", nodes.Macro, nodes.CallBlock)

_statement_keywords = frozenset(
    [
        "for",
        "if",
        "block",
        "extends",
        "print",
        "macro",
        "include",
        "from",
        "import",
        "set",
        "with",
        "autoescape",
    ]
)
_compare_operators = frozenset(["eq", "ne", "lt", "lteq", "gt", "gteq"])

_math_nodes: dict[str, type[nodes.Expr]] = {
    "add": nodes.Add,
    "sub": nodes.Sub,
    "mul": nodes.Mul,
    "div": nodes.Div,
    "floordiv": nodes.FloorDiv,
    "mod": nodes.Mod,
}


class Parser:
    """This is the central parsing class Jinja uses.  It's passed to
    extensions and can be used to parse expressions or statements.