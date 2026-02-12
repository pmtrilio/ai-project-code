<?php
namespace Slim\HttpCache;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Cache
{
    /**
     * Cache-Control type (public or private)
     *
     * @var string
     */
    protected $type;

    /**
     * Cache-Control max age in seconds
     *
     * @var int
     */
    protected $maxAge;

    /**
     * Cache-Control includes must-revalidate flag
     *
     * @var bool
     */
    protected $mustRevalidate;

    /**
     * Create new HTTP cache
     *
     * @param string $type           The cache type: "public" or "private"
     * @param int    $maxAge         The maximum age of client-side cache
     * @param bool   $mustRevalidate must-revalidate
     */
    public function __construct($type = 'private', $maxAge = 86400, $mustRevalidate = false)
    {
        $this->type = $type;
        $this->maxAge = $maxAge;
        $this->mustRevalidate = $mustRevalidate;