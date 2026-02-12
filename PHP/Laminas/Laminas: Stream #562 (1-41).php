<?php

declare(strict_types=1);

namespace Laminas\Log\Writer;

use Laminas\Log\Exception;
use Laminas\Log\Formatter\Simple as SimpleFormatter;
use Laminas\Stdlib\ErrorHandler;
use Traversable;

use function chmod;
use function dirname;
use function fclose;
use function file_exists;
use function fopen;
use function fwrite;
use function get_resource_type;
use function gettype;
use function is_array;
use function is_resource;
use function is_string;
use function is_writable;
use function iterator_to_array;
use function sprintf;
use function touch;

use const PHP_EOL;

class Stream extends AbstractWriter
{
    /**
     * Separator between log entries
     *
     * @var string
     */
    protected $logSeparator = PHP_EOL;

    /**
     * Holds the PHP stream to log to.
     *