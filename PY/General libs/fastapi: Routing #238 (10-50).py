    Mapping,
    Sequence,
)
from contextlib import AsyncExitStack, asynccontextmanager
from enum import Enum, IntEnum
from typing import (
    Annotated,
    Any,
    Callable,
    Optional,
    Union,
)

from annotated_doc import Doc
from fastapi import params
from fastapi._compat import (
    ModelField,
    Undefined,
    annotation_is_pydantic_v1,
    lenient_issubclass,
)
from fastapi.datastructures import Default, DefaultPlaceholder
from fastapi.dependencies.models import Dependant
from fastapi.dependencies.utils import (
    _should_embed_body_fields,
    get_body_field,
    get_dependant,
    get_flat_dependant,
    get_parameterless_sub_dependant,
    get_typed_return_annotation,
    solve_dependencies,
)
from fastapi.encoders import jsonable_encoder
from fastapi.exceptions import (
    EndpointContext,
    FastAPIError,
    PydanticV1NotSupportedError,
    RequestValidationError,
    ResponseValidationError,
    WebSocketRequestValidationError,
)