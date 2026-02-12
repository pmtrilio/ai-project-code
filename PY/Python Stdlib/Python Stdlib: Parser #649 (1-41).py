# Copyright (C) 2001 Python Software Foundation
# Author: Barry Warsaw, Thomas Wouters, Anthony Baxter
# Contact: email-sig@python.org

"""A parser of RFC 5322 and MIME email messages."""

__all__ = ['Parser', 'HeaderParser', 'BytesParser', 'BytesHeaderParser',
           'FeedParser', 'BytesFeedParser']

from io import StringIO, TextIOWrapper

from email.feedparser import FeedParser, BytesFeedParser
from email._policybase import compat32


class Parser:
    def __init__(self, _class=None, *, policy=compat32):
        """Parser of RFC 5322 and MIME email messages.

        Creates an in-memory object tree representing the email message, which
        can then be manipulated and turned over to a Generator to return the
        textual representation of the message.

        The string must be formatted as a block of RFC 5322 headers and header
        continuation lines, optionally preceded by a 'Unix-from' header.  The
        header block is terminated either by the end of the string or by a
        blank line.

        _class is the class to instantiate for new message objects when they
        must be created.  This class must have a constructor that can take
        zero arguments.  Default is Message.Message.

        The policy keyword specifies a policy object that controls a number of
        aspects of the parser's operation.  The default policy maintains
        backward compatibility.

        """
        self._class = _class
        self.policy = policy

    def parse(self, fp, headersonly=False):