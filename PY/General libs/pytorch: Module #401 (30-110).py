    "Module",
]

_grad_t = Union[tuple[Tensor, ...], Tensor]
# See https://mypy.readthedocs.io/en/latest/generics.html#generic-methods-and-generic-self for the use
# of `T` to annotate `self`. Many methods of `Module` return `self` and we want those return values to be
# the type of the subclass, not the looser type of `Module`.
T = TypeVar("T", bound="Module")


class _IncompatibleKeys(
    # pyrefly: ignore [invalid-inheritance]
    namedtuple("IncompatibleKeys", ["missing_keys", "unexpected_keys"]),
):
    __slots__ = ()

    def __repr__(self) -> str:
        # pyrefly: ignore [missing-attribute]
        if not self.missing_keys and not self.unexpected_keys:
            return "<All keys matched successfully>"
        return super().__repr__()

    __str__ = __repr__


def _addindent(s_, numSpaces):
    s = s_.split("\n")
    # don't do anything for single-line stuff
    if len(s) == 1:
        return s_
    first = s.pop(0)
    # Only add indentation to non-blank lines; blank lines stay empty
    s = [(numSpaces * " ") + line if line.strip() else "" for line in s]
    s = "\n".join(s)
    s = first + "\n" + s
    return s


r"""This tracks hooks common to all modules that are executed immediately before
.registering the buffer/module/parameter"""
_global_buffer_registration_hooks: dict[int, Callable] = OrderedDict()
_global_module_registration_hooks: dict[int, Callable] = OrderedDict()
_global_parameter_registration_hooks: dict[int, Callable] = OrderedDict()


class _WrappedHook:
    def __init__(self, hook: Callable, module: Optional["Module"] = None) -> None:
        self.hook: Callable = hook
        functools.update_wrapper(self, hook)

        self.with_module: bool = False

        if module is not None:
            self.module: weakref.ReferenceType[Module] = weakref.ref(module)
            self.with_module = True

    def __call__(self, *args: Any, **kwargs: Any) -> Any:
        if self.with_module:
            module = self.module()
            if module is None:
                raise RuntimeError("You are trying to call the hook of a dead Module!")
            return self.hook(module, *args, **kwargs)
        return self.hook(*args, **kwargs)

    def __getstate__(self) -> dict:
        result = {"hook": self.hook, "with_module": self.with_module}
        if self.with_module:
            # pyrefly: ignore [unsupported-operation]
            result["module"] = self.module()

        return result

    def __setstate__(self, state: dict):
        self.hook = state["hook"]
        self.with_module = state["with_module"]

        if self.with_module:
            if state["module"] is None:
                raise RuntimeError(
                    "You are trying to revive the hook of a dead Module!"
                )