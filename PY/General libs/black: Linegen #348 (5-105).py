import re
import sys
from collections.abc import Collection, Iterator
from dataclasses import replace
from enum import Enum, auto
from functools import partial, wraps
from typing import Union, cast

from black.brackets import (
    COMMA_PRIORITY,
    DOT_PRIORITY,
    STRING_PRIORITY,
    get_leaves_inside_matching_brackets,
    max_delimiter_priority_in_atom,
)
from black.comments import (
    FMT_OFF,
    FMT_ON,
    contains_fmt_directive,
    generate_comments,
    list_comments,
)
from black.lines import (
    Line,
    RHSResult,
    append_leaves,
    can_be_split,
    can_omit_invisible_parens,
    is_line_short_enough,
    line_to_string,
)
from black.mode import Feature, Mode, Preview
from black.nodes import (
    ASSIGNMENTS,
    BRACKETS,
    CLOSING_BRACKETS,
    OPENING_BRACKETS,
    STANDALONE_COMMENT,
    STATEMENT,
    WHITESPACE,
    Visitor,
    ensure_visible,
    fstring_tstring_to_string,
    get_annotation_type,
    has_sibling_with_type,
    is_arith_like,
    is_async_stmt_or_funcdef,
    is_atom_with_invisible_parens,
    is_docstring,
    is_empty_tuple,
    is_generator,
    is_lpar_token,
    is_multiline_string,
    is_name_token,
    is_one_sequence_between,
    is_one_tuple,
    is_parent_function_or_class,
    is_part_of_annotation,
    is_rpar_token,
    is_stub_body,
    is_stub_suite,
    is_tuple,
    is_tuple_containing_star,
    is_tuple_containing_walrus,
    is_type_ignore_comment_string,
    is_vararg,
    is_walrus_assignment,
    is_yield,
    syms,
    wrap_in_parentheses,
)
from black.numerics import normalize_numeric_literal
from black.strings import (
    fix_multiline_docstring,
    get_string_prefix,
    normalize_string_prefix,
    normalize_string_quotes,
    normalize_unicode_escape_sequences,
)
from black.trans import (
    CannotTransform,
    StringMerger,
    StringParenStripper,
    StringParenWrapper,
    StringSplitter,
    Transformer,
    hug_power_op,
)
from blib2to3.pgen2 import token
from blib2to3.pytree import Leaf, Node

# types
LeafID = int
LN = Union[Leaf, Node]


class CannotSplit(CannotTransform):
    """A readable split that fits the allotted line length is impossible."""


# This isn't a dataclass because @dataclass + Generic breaks mypyc.