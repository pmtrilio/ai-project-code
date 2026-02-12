
from __future__ import annotations

import copy
import json
import os
import re
import warnings
from collections import OrderedDict, UserDict
from collections.abc import Callable, Collection, Mapping, Sequence, Sized
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
