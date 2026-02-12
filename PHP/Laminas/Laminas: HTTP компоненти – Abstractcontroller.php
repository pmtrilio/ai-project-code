<?php

namespace Laminas\Mvc\Controller;

use Laminas\EventManager\EventInterface as Event;
use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerInterface;
use Laminas\Http\Header\Accept\FieldValuePart\AbstractFieldValuePart;
use Laminas\Http\PhpEnvironment\Response as HttpResponse;
use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\Controller\Plugin\Forward;
use Laminas\Mvc\Controller\Plugin\Layout;
use Laminas\Mvc\Controller\Plugin\Params;
use Laminas\Mvc\Controller\Plugin\Redirect;
use Laminas\Mvc\Controller\Plugin\Url;
use Laminas\Mvc\InjectApplicationEventInterface;
use Laminas\Mvc\MvcEvent;
use Laminas\ServiceManager\ServiceManager;
use Laminas\Stdlib\DispatchableInterface as Dispatchable;
use Laminas\Stdlib\RequestInterface as Request;
use Laminas\Stdlib\ResponseInterface as Response;
use Laminas\View\Model\ModelInterface;
use Laminas\View\Model\ViewModel;

use function array_merge;
use function array_values;
use function call_user_func_array;
use function class_implements;
use function is_callable;
use function lcfirst;
use function str_replace;
use function strrpos;
use function strstr;
use function substr;
use function ucwords;

/**
 * Abstract controller
 *
 * Convenience methods for pre-built plugins (@see __call):
 * @codingStandardsIgnoreStart
 * @method ModelInterface acceptableViewModelSelector(array $matchAgainst = null, bool $returnDefault = true, AbstractFieldValuePart $resultReference = null)
 * @codingStandardsIgnoreEnd
 * @method Forward forward()
 * @method Layout|ModelInterface layout(string $template = null)
 * @method Params|mixed params(string $param = null, mixed $default = null)
 * @method Redirect redirect()
 * @method Url url()
 * @method ViewModel createHttpNotFoundModel(Response $response)
 */
abstract class AbstractController implements
    Dispatchable,
    EventManagerAwareInterface,
    InjectApplicationEventInterface
{
    /** @var PluginManager */
    protected $plugins;

    /** @var Request */