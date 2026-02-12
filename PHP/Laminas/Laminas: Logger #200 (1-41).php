<?php

declare(strict_types=1);

namespace Laminas\Log;

use DateTime;
use ErrorException;
use Exception;
use Laminas\Log\Exception\InvalidArgumentException;
use Laminas\Log\Exception\RuntimeException;
use Laminas\Log\Processor\ProcessorInterface;
use Laminas\Log\Writer\WriterInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Stdlib\SplPriorityQueue;
use Traversable;

use function array_reverse;
use function count;
use function error_get_last;
use function error_reporting;
use function get_class;
use function gettype;
use function in_array;
use function is_array;
use function is_int;
use function is_object;
use function is_string;
use function method_exists;
use function register_shutdown_function;
use function restore_error_handler;
use function restore_exception_handler;
use function set_error_handler;
use function set_exception_handler;
use function sprintf;
use function var_export;

use const E_COMPILE_ERROR;
use const E_COMPILE_WARNING;