
The only thing you should ever use directly in this file is the Template class.
Create a compiled template object with a template_string, then call render()
with a context. In the compilation stage, the TemplateSyntaxError exception
will be raised if the template doesn't have proper syntax.

Sample code:

>>> from django import template
>>> s = '<html>{% if test %}<h1>{{ varvalue }}</h1>{% endif %}</html>'
>>> t = template.Template(s)

(t is now a compiled template, and its render() method can be called multiple
times with multiple contexts)

>>> c = template.Context({'test':True, 'varvalue': 'Hello'})
>>> t.render(c)
'<html><h1>Hello</h1></html>'
>>> c = template.Context({'test':False, 'varvalue': 'Hello'})
>>> t.render(c)
'<html></html>'
"""

import inspect
import logging
import re
import warnings
from enum import Enum

from django.template.context import BaseContext
from django.utils.deprecation import django_file_prefixes
from django.utils.formats import localize
from django.utils.html import conditional_escape
from django.utils.inspect import lazy_annotations
from django.utils.regex_helper import _lazy_re_compile
from django.utils.safestring import SafeData, SafeString, mark_safe
from django.utils.text import get_text_list, smart_split, unescape_string_literal
from django.utils.timezone import template_localtime
from django.utils.translation import gettext_lazy, pgettext_lazy

from .exceptions import TemplateSyntaxError

# template syntax constants
FILTER_SEPARATOR = "|"
FILTER_ARGUMENT_SEPARATOR = ":"
VARIABLE_ATTRIBUTE_SEPARATOR = "."
BLOCK_TAG_START = "{%"
BLOCK_TAG_END = "%}"
VARIABLE_TAG_START = "{{"
VARIABLE_TAG_END = "}}"
COMMENT_TAG_START = "{#"
COMMENT_TAG_END = "#}"
SINGLE_BRACE_START = "{"
SINGLE_BRACE_END = "}"

# what to report as the origin for templates that come from non-loader sources
# (e.g. strings)
UNKNOWN_SOURCE = "<unknown source>"

# Match BLOCK_TAG_*, VARIABLE_TAG_*, and COMMENT_TAG_* tags and capture the
# entire tag, including start/end delimiters. Using re.compile() is faster
# than instantiating SimpleLazyObject with _lazy_re_compile().
tag_re = re.compile(r"({%.*?%}|{{.*?}}|{#.*?#})")

logger = logging.getLogger("django.template")


class TokenType(Enum):
    TEXT = 0
    VAR = 1
    BLOCK = 2
    COMMENT = 3


class VariableDoesNotExist(Exception):
    def __init__(self, msg, params=()):
        self.msg = msg
        self.params = params

    def __str__(self):
        return self.msg % self.params