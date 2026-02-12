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
    """

    def __init__(
        self,
        environment: "Environment",
        source: str,
        name: str | None = None,
        filename: str | None = None,
        state: str | None = None,
    ) -> None:
        self.environment = environment
        self.stream = environment._tokenize(source, name, filename, state)
        self.name = name
        self.filename = filename
        self.closed = False
        self.extensions: dict[
            str, t.Callable[[Parser], nodes.Node | list[nodes.Node]]
        ] = {}
        for extension in environment.iter_extensions():
            for tag in extension.tags:
                self.extensions[tag] = extension.parse
        self._last_identifier = 0
        self._tag_stack: list[str] = []
        self._end_token_stack: list[tuple[str, ...]] = []

    def fail(
        self,
        msg: str,
        lineno: int | None = None,
        exc: type[TemplateSyntaxError] = TemplateSyntaxError,
    ) -> "te.NoReturn":
        """Convenience method that raises `exc` with the message, passed
        line number or last line number as well as the current name and
        filename.
        """
        if lineno is None:
            lineno = self.stream.current.lineno
        raise exc(msg, lineno, self.name, self.filename)

    def _fail_ut_eof(
        self,
        name: str | None,
        end_token_stack: list[tuple[str, ...]],
        lineno: int | None,
    ) -> "te.NoReturn":
        expected: set[str] = set()
        for exprs in end_token_stack:
            expected.update(map(describe_token_expr, exprs))
        if end_token_stack:
            currently_looking: str | None = " or ".join(