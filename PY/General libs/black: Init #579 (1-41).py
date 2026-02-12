import io
import json
import platform
import re
import sys
import tokenize
import traceback
from collections.abc import (
    Collection,
    Generator,
    MutableMapping,
    Sequence,
)
from contextlib import nullcontext
from dataclasses import replace
from datetime import datetime, timezone
from enum import Enum
from json.decoder import JSONDecodeError
from pathlib import Path
from re import Pattern
from typing import Any

import click
from click.core import ParameterSource
from mypy_extensions import mypyc_attr
from pathspec import GitIgnoreSpec
from pathspec.patterns.gitignore import GitIgnorePatternError

from _black_version import version as __version__
from black.cache import Cache
from black.comments import normalize_fmt_off
from black.const import (
    DEFAULT_EXCLUDES,
    DEFAULT_INCLUDES,
    DEFAULT_LINE_LENGTH,
    STDIN_PLACEHOLDER,
)
from black.files import (
    best_effort_relative_path,
    find_project_root,
    find_pyproject_toml,