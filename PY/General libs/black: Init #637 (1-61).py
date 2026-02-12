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
    find_user_pyproject_toml,
    gen_python_files,
    get_gitignore,
    parse_pyproject_toml,
    path_is_excluded,
    resolves_outside_root_or_cannot_stat,
    wrap_stream_for_windows,
)
from black.handle_ipynb_magics import (
    PYTHON_CELL_MAGICS,
    jupyter_dependencies_are_installed,
    mask_cell,
    put_trailing_semicolon_back,
    remove_trailing_semicolon,
    unmask_cell,
    validate_cell,
)
from black.linegen import LN, LineGenerator, transform_line
from black.lines import EmptyLineTracker, LinesBlock
from black.mode import FUTURE_FLAG_TO_FEATURE, VERSION_TO_FEATURES, Feature