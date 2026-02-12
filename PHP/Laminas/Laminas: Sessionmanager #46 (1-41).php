<?php

namespace Laminas\Session;

use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Stdlib\ArrayUtils;
use Traversable;

use function array_key_exists;
use function array_merge;
use function assert;
use function constant;
use function defined;
use function headers_sent;
use function is_array;
use function is_string;
use function iterator_to_array;
use function preg_match;
use function register_shutdown_function;
use function session_destroy;
use function session_id;
use function session_name;
use function session_regenerate_id;
use function session_set_save_handler;
use function session_start;
use function session_status;
use function session_write_close;
use function setcookie;

use const PHP_SESSION_ACTIVE;

/**
 * Session ManagerInterface implementation utilizing ext/session
 *
 * @final
 */
class SessionManager extends AbstractManager
{
    /**
     * Default options when a call to {@link destroy()} is made