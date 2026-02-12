<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Pipes\UnixPipes;
use Symfony\Component\Process\Pipes\WindowsPipes;

/**
 * Process is a thin wrapper around proc_* functions to easily
 * start independent PHP processes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Romain Neutron <imprec@gmail.com>
 *
 * @implements \IteratorAggregate<string, string>
 */
class Process implements \IteratorAggregate
{
    public const ERR = 'err';
    public const OUT = 'out';

    public const STATUS_READY = 'ready';
    public const STATUS_STARTED = 'started';
    public const STATUS_TERMINATED = 'terminated';

    public const STDIN = 0;
    public const STDOUT = 1;
    public const STDERR = 2;

    // Timeout Precision in seconds.
    public const TIMEOUT_PRECISION = 0.2;

    public const ITER_NON_BLOCKING = 1; // By default, iterating over outputs is a blocking call, use this flag to make it non-blocking
    public const ITER_KEEP_OUTPUT = 2;  // By default, outputs are cleared while iterating, use this flag to keep them in memory
    public const ITER_SKIP_OUT = 4;     // Use this flag to skip STDOUT while iterating
    public const ITER_SKIP_ERR = 8;     // Use this flag to skip STDERR while iterating

    private ?\Closure $callback = null;
    private array|string $commandline;
    private ?string $cwd;
    private array $env = [];
    /** @var resource|string|\Iterator|null */
    private $input;
    private ?float $starttime = null;
    private ?float $lastOutputTime = null;
    private ?float $timeout = null;
    private ?float $idleTimeout = null;
    private ?int $exitcode = null;
    private array $fallbackStatus = [];
    private array $processInformation;
    private bool $outputDisabled = false;
    /** @var resource */
    private $stdout;
    /** @var resource */
    private $stderr;
    /** @var resource|null */
    private $process;
    private string $status = self::STATUS_READY;
    private int $incrementalOutputOffset = 0;
    private int $incrementalErrorOutputOffset = 0;
    private bool $tty = false;
    private bool $pty;
    private array $options = ['suppress_errors' => true, 'bypass_shell' => true];

    private WindowsPipes|UnixPipes $processPipes;
