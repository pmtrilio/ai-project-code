<?php

/**
 * Slim Framework (https://slimframework.com)
 *
 * @license https://github.com/slimphp/Slim/blob/4.x/LICENSE.md (MIT License)
 */

declare(strict_types=1);

namespace Slim\Handlers;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\Error\Renderers\HtmlErrorRenderer;
use Slim\Error\Renderers\JsonErrorRenderer;
use Slim\Error\Renderers\PlainTextErrorRenderer;
use Slim\Error\Renderers\XmlErrorRenderer;
use Slim\Exception\HttpException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Interfaces\ErrorRendererInterface;
use Slim\Logger;
use Throwable;

use function array_intersect;
use function array_key_exists;
use function array_keys;
use function call_user_func;
use function count;
use function current;
use function explode;
use function implode;
use function next;
use function preg_match;

/**
 * Default Slim application error handler
 *
 * It outputs the error message and diagnostic information in one of the following formats:
 * JSON, XML, Plain Text or HTML based on the Accept header.
 * @api
 */
class ErrorHandler implements ErrorHandlerInterface
{
    protected string $defaultErrorRendererContentType = 'text/html';

    /**
     * @var ErrorRendererInterface|string|callable
     */
    protected $defaultErrorRenderer = HtmlErrorRenderer::class;

    /**
     * @var ErrorRendererInterface|string|callable
     */
    protected $logErrorRenderer = PlainTextErrorRenderer::class;

    /**
     * @var array<string|callable>
     */
    protected array $errorRenderers = [
        'application/json' => JsonErrorRenderer::class,
        'application/xml' => XmlErrorRenderer::class,
        'text/xml' => XmlErrorRenderer::class,
        'text/html' => HtmlErrorRenderer::class,
        'text/plain' => PlainTextErrorRenderer::class,
    ];

    protected bool $displayErrorDetails = false;

    protected bool $logErrors;

    protected bool $logErrorDetails = false;

    protected ?string $contentType = null;

    protected ?string $method = null;

    protected ServerRequestInterface $request;

    protected Throwable $exception;

    protected int $statusCode;

    protected CallableResolverInterface $callableResolver;

    protected ResponseFactoryInterface $responseFactory;

    protected LoggerInterface $logger;

    public function __construct(
        CallableResolverInterface $callableResolver,
        ResponseFactoryInterface $responseFactory,
        ?LoggerInterface $logger = null
    ) {
        $this->callableResolver = $callableResolver;
        $this->responseFactory = $responseFactory;