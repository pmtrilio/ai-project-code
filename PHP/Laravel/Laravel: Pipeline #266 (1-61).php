<?php

namespace Illuminate\Pipeline;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Pipeline\Pipeline as PipelineContract;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use Throwable;

class Pipeline implements PipelineContract
{
    use Conditionable;
    use Macroable;

    /**
     * The container implementation.
     *
     * @var \Illuminate\Contracts\Container\Container|null
     */
    protected $container;

    /**
     * The object being passed through the pipeline.
     *
     * @var mixed
     */
    protected $passable;

    /**
     * The array of class pipes.
     *
     * @var array
     */
    protected $pipes = [];

    /**
     * The method to call on each pipe.
     *
     * @var string
     */
    protected $method = 'handle';

    /**
     * The final callback to be executed after the pipeline ends regardless of the outcome.
     *
     * @var \Closure|null
     */
    protected $finally;

    /**
     * Indicates whether to wrap the pipeline in a database transaction.
     *
     * @var string|null|\UnitEnum|false
     */
    protected $withinTransaction = false;

    /**
     * Create a new class instance.