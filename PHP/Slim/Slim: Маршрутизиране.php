<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Routing;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use RuntimeException;
use Slim\Handlers\Strategies\RequestResponse;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteParserInterface;

use function array_pop;
use function dirname;
use function file_exists;
use function is_readable;
use function is_writable;
use function sprintf;

/**
 * RouteCollector is used to collect routes and route groups
 * as well as generate paths and URLs relative to its environment
 * @template TContainerInterface of (ContainerInterface|null)
 */
class RouteCollector implements RouteCollectorInterface
{
    protected RouteParserInterface $routeParser;

    protected CallableResolverInterface $callableResolver;

    protected ?ContainerInterface $container = null;

    protected InvocationStrategyInterface $defaultInvocationStrategy;

    /**
     * Base path used in pathFor()
     */
    protected string $basePath = '';

    /**
     * Path to fast route cache file. Set to null to disable route caching
     */
    protected ?string $cacheFile = null;

    /**
     * Routes
     *
     * @var RouteInterface[]