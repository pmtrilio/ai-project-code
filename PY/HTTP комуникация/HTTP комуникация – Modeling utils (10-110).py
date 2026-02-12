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
    load_offloaded_parameter,
)
from .integrations.deepspeed import _load_state_dict_into_zero3_model
from .integrations.eager_paged import eager_paged_attention_forward
from .integrations.flash_attention import flash_attention_forward
from .integrations.flash_paged import paged_attention_forward
from .integrations.flex_attention import flex_attention_forward
from .integrations.hub_kernels import is_kernel
from .integrations.peft import maybe_load_adapters
from .integrations.sdpa_attention import sdpa_attention_forward
from .integrations.sdpa_paged import sdpa_attention_paged_forward
from .integrations.tensor_parallel import (
    ALL_PARALLEL_STYLES,
    _get_parameter_tp_plan,
    distribute_model,
    initialize_tensor_parallelism,
    repack_weights,
    replace_state_dict_local_with_dtensor,
    shard_and_distribute_module,
    verify_tp_plan,
)
from .loss.loss_utils import LOSS_MAPPING
from .modeling_flash_attention_utils import lazy_import_flash_attention, lazy_import_paged_flash_attention
from .modeling_rope_utils import ROPE_INIT_FUNCTIONS
from .pytorch_utils import id_tensor_storage
from .quantizers import HfQuantizer
from .quantizers.auto import get_hf_quantizer
from .quantizers.quantizers_utils import get_module_from_name
from .safetensors_conversion import auto_conversion
from .utils import (
    ADAPTER_SAFE_WEIGHTS_NAME,
    DUMMY_INPUTS,
    SAFE_WEIGHTS_INDEX_NAME,
    SAFE_WEIGHTS_NAME,
    WEIGHTS_INDEX_NAME,
    WEIGHTS_NAME,
    ContextManagers,
    KernelConfig,
    PushToHubMixin,
    cached_file,
    check_torch_load_is_safe,
    copy_func,
    has_file,
    is_accelerate_available,
    is_bitsandbytes_available,