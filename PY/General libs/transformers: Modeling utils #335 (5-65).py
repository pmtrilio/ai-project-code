# you may not use this file except in compliance with the License.
# You may obtain a copy of the License at
#
#     http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS,
# WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
# See the License for the specific language governing permissions and
# limitations under the License.
import collections
import copy
import functools
import importlib.metadata
import inspect
import json
import os
import re
import sys
import warnings
from abc import abstractmethod
from collections import defaultdict
from collections.abc import Callable, Iterator
from contextlib import contextmanager
from dataclasses import dataclass, field, replace
from enum import Enum
from functools import partial, wraps
from itertools import cycle
from threading import Thread
from typing import Optional, TypeVar, get_type_hints
from zipfile import is_zipfile

import torch
from huggingface_hub import create_repo, is_offline_mode, split_torch_state_dict_into_shards
from packaging import version
from safetensors import safe_open
from safetensors.torch import save_file as safe_save_file
from torch import Tensor, nn
from torch.distributions import constraints
from torch.utils.checkpoint import checkpoint

from . import initialization as init
from .configuration_utils import PreTrainedConfig
from .conversion_mapping import get_model_conversion_mapping
from .core_model_loading import (
    WeightConverter,
    WeightRenaming,
    convert_and_load_state_dict_in_model,
    revert_weight_conversion,
)
from .distributed import DistributedConfig
from .dynamic_module_utils import custom_object_save
from .generation import CompileConfig, GenerationConfig
from .integrations import PeftAdapterMixin, deepspeed_config, is_deepspeed_zero3_enabled, is_fsdp_enabled
from .integrations.accelerate import (
    _get_device_map,
    accelerate_disk_offload,
    accelerate_dispatch,
    check_and_set_device_map,
    expand_device_map,
    get_device,