<?php

namespace Illuminate\View\Engines;

use Illuminate\Database\RecordNotFoundException;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;
use Illuminate\View\Compilers\CompilerInterface;
use Illuminate\View\ViewException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class CompilerEngine extends PhpEngine
{
    /**
     * The Blade compiler instance.
     *
     * @var \Illuminate\View\Compilers\CompilerInterface
     */
    protected $compiler;

    /**
     * A stack of the last compiled templates.
     *
     * @var array
     */
    protected $lastCompiled = [];

    /**
     * The view paths that were compiled or are not expired, keyed by the path.
     *
     * @var array<string, true>
     */
    protected $compiledOrNotExpired = [];

    /**
     * Create a new compiler engine instance.
     *
     * @param  \Illuminate\View\Compilers\CompilerInterface  $compiler