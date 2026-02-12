<?php

namespace Illuminate\Cache;

use Closure;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Redis\Connections\PhpRedisConnection;
use Illuminate\Support\Collection;
use Illuminate\Support\InteractsWithTime;

use function Illuminate\Support\enum_value;

class RateLimiter
{
    use InteractsWithTime;

    /**
     * The cache store implementation.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * The configured limit object resolvers.
     *
     * @var array
     */
    protected $limiters = [];

    /**
     * Create a new rate limiter instance.
     *
     * @param  \Illuminate\Contracts\Cache\Repository  $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**