__version__ = "4.3.2"
__copyright__ = "Copyright (c) 2004-2013 Leonard Richardson"
__license__ = "MIT"

__all__ = ['BeautifulSoup']

import os
import re
import warnings

from .builder import builder_registry, ParserRejectedMarkup
from .dammit import UnicodeDammit
from .element import (
    CData,
    Comment,
    DEFAULT_OUTPUT_ENCODING,
    Declaration,
    Doctype,
    NavigableString,
    PageElement,
    ProcessingInstruction,
    ResultSet,
    SoupStrainer,
    Tag,
    )

# The very first thing we do is give a useful error if someone is
# running this code under Python 3 without converting it.
syntax_error = u'You are trying to run the Python 2 version of Beautiful Soup under Python 3. This will not work. You need to convert the code, either by installing it (`python setup.py install`) or by running 2to3 (`2to3 -w bs4`).'

class BeautifulSoup(Tag):
    """
    This class defines the basic interface called by the tree builders.

    These methods will be called by the parser:
      reset()
      feed(markup)

    The tree builder may call these methods from its feed() implementation:
      handle_starttag(name, attrs) # See note about return value
      handle_endtag(name)
      handle_data(data) # Appends to the current data node
      endData(containerClass=NavigableString) # Ends the current data node

    No matter how complicated the underlying parser is, you should be
    able to build a tree using 'start tag' events, 'end tag' events,
    'data' events, and "done with data" events.

    If you encounter an empty-element tag (aka a self-closing tag,
    like HTML's <br> tag), call handle_starttag and then
    handle_endtag.
    """
    ROOT_TAG_NAME = u'[document]'

    # If the end-user gives no indication which tree builder they
    # want, look for one with these features.
    DEFAULT_BUILDER_FEATURES = ['html', 'fast']

    ASCII_SPACES = '\x20\x0a\x09\x0c\x0d'

    def __init__(self, markup="", features=None, builder=None,
                 parse_only=None, from_encoding=None, **kwargs):
        """The Soup object is initialized as the 'root tag', and the
        provided markup (which can be a string or a file-like object)
        is fed into the underlying parser."""

        if 'convertEntities' in kwargs:
            warnings.warn(
                "BS4 does not respect the convertEntities argument to the "
                "BeautifulSoup constructor. Entities are always converted "
                "to Unicode characters.")

        if 'markupMassage' in kwargs:
            del kwargs['markupMassage']
            warnings.warn(
                "BS4 does not respect the markupMassage argument to the "
                "BeautifulSoup constructor. The tree builder is responsible "
                "for any necessary markup massage.")

        if 'smartQuotesTo' in kwargs:
            del kwargs['smartQuotesTo']