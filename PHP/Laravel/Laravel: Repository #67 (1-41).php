<?php

namespace Illuminate\Config;

use ArrayAccess;
use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;

class Repository implements ArrayAccess, ConfigContract
{
    use Macroable;

    /**
     * All of the configuration items.
     *
     * @var array<string,mixed>
     */
    protected $items = [];

    /**
     * Create a new configuration repository.
     *
     * @param  array  $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->items, $key);