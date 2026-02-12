    _stack_if_compiling,
    _to_scalar,
    _use_grad_for_differentiable,
    _view_as_real,
    DeviceDict,
    DeviceDtypeDict,
    Optimizer,
    ParamsT,
)


__all__ = ["Adam", "adam"]


class Adam(Optimizer):
    def __init__(
        self,
        params: ParamsT,
        lr: float | Tensor = 1e-3,
        betas: tuple[float | Tensor, float | Tensor] = (0.9, 0.999),
        eps: float = 1e-8,
        weight_decay: float = 0,
        amsgrad: bool = False,
        *,
        foreach: bool | None = None,
        maximize: bool = False,
        capturable: bool = False,
        differentiable: bool = False,
        fused: bool | None = None,
        decoupled_weight_decay: bool = False,
    ) -> None:
        if isinstance(lr, Tensor):
            if foreach and not capturable:
                raise ValueError(
                    "lr as a Tensor is not supported for capturable=False and foreach=True"
                )
            if lr.numel() != 1:
                raise ValueError("Tensor lr must be 1-element")
        if not 0.0 <= lr:
            raise ValueError(f"Invalid learning rate: {lr}")
        if not 0.0 <= eps:
            raise ValueError(f"Invalid epsilon value: {eps}")
        if not 0.0 <= betas[0] < 1.0:
            raise ValueError(f"Invalid beta parameter at index 0: {betas[0]}")
        if not 0.0 <= betas[1] < 1.0:
            raise ValueError(f"Invalid beta parameter at index 1: {betas[1]}")
        if not 0.0 <= weight_decay:
            raise ValueError(f"Invalid weight_decay value: {weight_decay}")
        if not (
            (isinstance(betas[0], float) and isinstance(betas[1], float))
            or (isinstance(betas[0], Tensor) and isinstance(betas[1], Tensor))
        ):
            raise ValueError("betas must be either both floats or both Tensors")
        if isinstance(betas[0], Tensor):
            if not capturable and foreach:
                raise ValueError(
                    "betas[0] as a Tensor is not supported for capturable=False and foreach=True"
                )
            if betas[0].numel() != 1:
                raise ValueError("Tensor betas[0] must be 1-element")
        if isinstance(betas[1], Tensor):