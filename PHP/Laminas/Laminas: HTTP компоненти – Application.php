<?php

namespace Laminas\Mvc;

use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Mvc\Service\ServiceManagerConfig;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\RequestInterface;
use Laminas\Stdlib\ResponseInterface;

use function array_merge;
use function array_unique;

/**
 * Main application class for invoking applications
 *
 * Expects the user will provide a configured ServiceManager, configured with
 * the following services:
 *
 * - EventManager
 * - ModuleManager
 * - Request
 * - Response
 * - RouteListener
 * - Router
 * - DispatchListener
 * - MiddlewareListener
 * - ViewManager
 *
 * The most common workflow is:
 * <code>
 * $services = new Laminas\ServiceManager\ServiceManager($servicesConfig);
 * $app      = new Application($appConfig, $services);
 * $app->bootstrap();
 * $response = $app->run();
 * $response->send();
 * </code>
 *
 * bootstrap() opts in to the default route, dispatch, and view listeners,
 * sets up the MvcEvent, and triggers the bootstrap event. This can be omitted
 * if you wish to setup your own listeners and/or workflow; alternately, you
 * can simply extend the class to override such behavior.
 */
class Application implements
    ApplicationInterface,
    EventManagerAwareInterface
{
    public const ERROR_CONTROLLER_CANNOT_DISPATCH = 'error-controller-cannot-dispatch';
    public const ERROR_CONTROLLER_NOT_FOUND       = 'error-controller-not-found';
    public const ERROR_CONTROLLER_INVALID         = 'error-controller-invalid';
    public const ERROR_EXCEPTION                  = 'error-exception';
    public const ERROR_ROUTER_NO_MATCH            = 'error-router-no-match';
    public const ERROR_MIDDLEWARE_CANNOT_DISPATCH = 'error-middleware-cannot-dispatch';

    /**
     * Default application event listeners
     *
     * @var array
     */