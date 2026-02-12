#
# This file is part of gunicorn released under the MIT license.
# See the NOTICE for more information.
import importlib.util
import importlib.machinery
import os
import sys
import traceback

from gunicorn import util
from gunicorn.arbiter import Arbiter
from gunicorn.config import Config, get_default_config_file
from gunicorn import debug


class BaseApplication:
    """
    An application interface for configuring and loading
    the various necessities for any given web framework.
    """
    def __init__(self, usage=None, prog=None):
        self.usage = usage
        self.cfg = None
        self.callable = None
        self.prog = prog
        self.logger = None
        self.do_load_config()

    def do_load_config(self):
        """
        Loads the configuration
        """
        try:
            self.load_default_config()
            self.load_config()
        except Exception as e:
            print("\nError: %s" % str(e), file=sys.stderr)
            sys.stderr.flush()
            sys.exit(1)

    def load_default_config(self):