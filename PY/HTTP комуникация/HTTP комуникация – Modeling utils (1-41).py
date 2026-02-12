# Copyright 2018 The Google AI Language Team Authors, Facebook AI Research authors and The HuggingFace Inc. team.
# Copyright (c) 2018, NVIDIA CORPORATION.  All rights reserved.
#
# Licensed under the Apache License, Version 2.0 (the "License");
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