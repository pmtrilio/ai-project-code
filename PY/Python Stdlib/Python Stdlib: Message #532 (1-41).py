# Copyright (C) 2001 Python Software Foundation
# Author: Barry Warsaw
# Contact: email-sig@python.org

"""Basic message object for the email package object model."""

__all__ = ['Message', 'EmailMessage']

import binascii
import re
import quopri
from io import BytesIO, StringIO

# Intrapackage imports
from email import utils
from email import errors
from email._policybase import compat32
from email import charset as _charset
from email._encoded_words import decode_b
Charset = _charset.Charset

SEMISPACE = '; '

# Regular expression that matches 'special' characters in parameters, the
# existence of which force quoting of the parameter value.
tspecials = re.compile(r'[ \(\)<>@,;:\\"/\[\]\?=]')


def _splitparam(param):
    # Split header parameters.  BAW: this may be too simple.  It isn't
    # strictly RFC 2045 (section 5.1) compliant, but it catches most headers
    # found in the wild.  We may eventually need a full fledged parser.
    # RDM: we might have a Header here; for now just stringify it.
    a, sep, b = str(param).partition(';')
    if not sep:
        return a.strip(), None
    return a.strip(), b.strip()

def _formatparam(param, value=None, quote=True):
    """Convenience function to format and return a key=value pair.
