<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Level;
use Monolog\Utils;
use Monolog\LogRecord;

/**
 * Stores to any stream resource
 *
 * Can be used to store into php://stderr, remote and local files, etc.
 *
 * @author Jordi Boggiano <j.boggiano@seld.be>
 */
class StreamHandler extends AbstractProcessingHandler
{
    protected const MAX_CHUNK_SIZE = 2147483647;
    /** 10MB */
    protected const DEFAULT_CHUNK_SIZE = 10 * 1024 * 1024;
    protected int $streamChunkSize;
    /** @var resource|null */
    protected $stream;
    protected string|null $url = null;
    private string|null $errorMessage = null;
    protected int|null $filePermission;
    protected bool $useLocking;
    protected string $fileOpenMode;
    /** @var true|null */
    private bool|null $dirCreated = null;
    private bool $retrying = false;
    private int|null $inodeUrl = null;