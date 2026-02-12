<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Middleware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;

use function get_class;
use function is_subclass_of;

/** @api */
class ErrorMiddleware implements MiddlewareInterface
{
    protected CallableResolverInterface $callableResolver;

    protected ResponseFactoryInterface $responseFactory;

    protected bool $displayErrorDetails;

    protected bool $logErrors;

    protected bool $logErrorDetails;

    protected ?LoggerInterface $logger = null;

    /**
     * @var ErrorHandlerInterface[]|callable[]|string[]
     */
    protected array $handlers = [];

    /**
     * @var ErrorHandlerInterface[]|callable[]|string[]
     */
    protected array $subClassHandlers = [];

    /**
     * @var ErrorHandlerInterface|callable|string|null
     */
    protected $defaultErrorHandler;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,