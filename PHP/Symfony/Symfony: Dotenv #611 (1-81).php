<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Dotenv;

use Symfony\Component\Dotenv\Exception\FormatException;
use Symfony\Component\Dotenv\Exception\FormatExceptionContext;
use Symfony\Component\Dotenv\Exception\PathException;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessException;
use Symfony\Component\Process\Process;

/**
 * Manages .env files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class Dotenv
{
    public const VARNAME_REGEX = '(?i:_?[A-Z][A-Z0-9_]*+)';
    public const STATE_VARNAME = 0;
    public const STATE_VALUE = 1;

    private string $path;
    private int $cursor;
    private int $lineno;
    private string $data;
    private int $end;
    private array $values = [];
    private string $envKey;
    private string $debugKey;
    private array $prodEnvs = ['prod'];
    private bool $usePutenv = false;

    public function __construct(string $envKey = 'APP_ENV', string $debugKey = 'APP_DEBUG')
    {
        $this->envKey = $envKey;
        $this->debugKey = $debugKey;
    }

    /**
     * @return $this
     */
    public function setProdEnvs(array $prodEnvs): static
    {
        $this->prodEnvs = $prodEnvs;

        return $this;
    }

    /**
     * @param bool $usePutenv If `putenv()` should be used to define environment variables or not.
     *                        Beware that `putenv()` is not thread safe, that's why it's not enabled by default
     *
     * @return $this
     */
    public function usePutenv(bool $usePutenv = true): static
    {
        $this->usePutenv = $usePutenv;

        return $this;
    }

    /**
     * Loads one or several .env files.
     *
     * @param string $path          A file to load
     * @param string ...$extraPaths A list of additional files to load
     *
     * @throws FormatException when a file has a syntax error
     * @throws PathException   when a file does not exist or is not readable
     */
    public function load(string $path, string ...$extraPaths): void