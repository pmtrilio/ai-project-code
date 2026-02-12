"""Worker Consumer Blueprint.

This module contains the components responsible for consuming messages
from the broker, processing the messages and keeping the broker connections
up and running.
"""
import errno
import logging
import os
import warnings
from collections import defaultdict
from time import sleep

from billiard.common import restart_state
from billiard.exceptions import RestartFreqExceeded
from kombu.asynchronous.semaphore import DummyLock
from kombu.exceptions import ContentDisallowed, DecodeError
from kombu.utils.compat import _detect_environment
from kombu.utils.encoding import safe_repr
from kombu.utils.limits import TokenBucket
from vine import ppartial, promise

from celery import bootsteps, signals
from celery.app.trace import build_tracer
from celery.exceptions import (CPendingDeprecationWarning, InvalidTaskError, NotRegistered, WorkerShutdown,
                               WorkerTerminate)
from celery.utils.functional import noop
from celery.utils.log import get_logger
from celery.utils.nodenames import gethostname
from celery.utils.objects import Bunch
from celery.utils.text import truncate
from celery.utils.time import humanize_seconds, rate
from celery.worker import loops
from celery.worker.state import (active_requests, maybe_shutdown, requests, reserved_requests, successful_requests,
                                 task_reserved)

__all__ = ('Consumer', 'Evloop', 'dump_body')

CLOSE = bootsteps.CLOSE
TERMINATE = bootsteps.TERMINATE
STOP_CONDITIONS = {CLOSE, TERMINATE}
logger = get_logger(__name__)
debug, info, warn, error, crit = (logger.debug, logger.info, logger.warning,
                                  logger.error, logger.critical)

CONNECTION_RETRY = """\
consumer: Connection to broker lost. \
Trying to re-establish the connection...\
"""

CONNECTION_RETRY_STEP = """\
Trying again {when}... ({retries}/{max_retries})\
"""

CONNECTION_ERROR = """\
consumer: Cannot connect to %s: %s.
%s
"""

CONNECTION_FAILOVER = """\
Will retry using next failover.\