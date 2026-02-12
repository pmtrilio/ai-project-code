from dataclasses import dataclass
from pathlib import Path
from typing import TYPE_CHECKING, Any, NamedTuple, Union

import numpy as np
from huggingface_hub import create_repo, is_offline_mode, list_repo_files
from packaging import version

from . import __version__
from .dynamic_module_utils import custom_object_save
from .utils import (
    CHAT_TEMPLATE_DIR,
    CHAT_TEMPLATE_FILE,
    ExplicitEnum,
    PaddingStrategy,
    PushToHubMixin,
    TensorType,
    add_end_docstrings,
    cached_file,
    copy_func,
    extract_commit_hash,
    is_mlx_available,
    is_numpy_array,
    is_protobuf_available,
    is_tokenizers_available,
    is_torch_available,
    is_torch_device,
    is_torch_tensor,
    list_repo_templates,
    logging,
    requires_backends,
    to_py_obj,
)
from .utils.chat_parsing_utils import recursive_parse
from .utils.chat_template_utils import render_jinja_template
from .utils.import_utils import PROTOBUF_IMPORT_ERROR


if TYPE_CHECKING:
    if is_torch_available():
        import torch


def import_protobuf_decode_error(error_message=""):
    if is_protobuf_available():
        from google.protobuf.message import DecodeError

        return DecodeError
    else:
        raise ImportError(PROTOBUF_IMPORT_ERROR.format(error_message))


def flatten(arr: list):
    res = []
    if len(arr) > 0:
        for sub_arr in arr:
            if isinstance(arr[0], (list, tuple)):
                res.extend(flatten(sub_arr))
            else:
                res.append(sub_arr)
    return res


if is_tokenizers_available() or TYPE_CHECKING:
    from tokenizers import Encoding as EncodingFast

if is_tokenizers_available():
    from tokenizers import AddedToken
else:

    @dataclass(frozen=False, eq=True)
    class AddedToken:
        """
        AddedToken represents a token to be added to a Tokenizer An AddedToken can have special options defining the
        way it should behave.

        The `normalized` will default to `not special` if it is not specified, similarly to the definition in
        `tokenizers`.
        """

        def __init__(
            self, content: str, single_word=False, lstrip=False, rstrip=False, special=False, normalized=None
        ):
            self.content = content
            self.single_word = single_word
            self.lstrip = lstrip
            self.rstrip = rstrip
            self.special = special
            self.normalized = normalized if normalized is not None else not special

        def __getstate__(self):
            return self.__dict__

        def __str__(self):
            return self.content


logger = logging.get_logger(__name__)

VERY_LARGE_INTEGER = int(1e30)  # This is used to set the max input length for a model with infinite size input
LARGE_INTEGER = int(1e20)  # This is used when we need something big but slightly smaller than VERY_LARGE_INTEGER