<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         2.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Controller;

use Cake\Controller\Exception\MissingComponentException;
use Cake\Core\App;
use Cake\Core\ContainerInterface;
use Cake\Core\Exception\CakeException;
use Cake\Core\ObjectRegistry;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use League\Container\Argument\ArgumentReflectorTrait;
use League\Container\Argument\ArgumentResolverTrait;
use League\Container\Argument\LiteralArgument;
use League\Container\Argument\ResolvableArgument;
use League\Container\Exception\NotFoundException;
use League\Container\ReflectionContainer;
use ReflectionClass;
use ReflectionFunctionAbstract;
use ReflectionMethod;
use ReflectionNamedType;
use RuntimeException;

/**
 * ComponentRegistry is a registry for loaded components
 *
 * Handles loading, constructing and binding events for component class objects.
 *
 * @template TSubject of \Cake\Controller\Controller
 * @extends \Cake\Core\ObjectRegistry<\Cake\Controller\Component>
 * @implements \Cake\Event\EventDispatcherInterface<TSubject>
 */
class ComponentRegistry extends ObjectRegistry implements EventDispatcherInterface
{
    /**
     * @use \Cake\Event\EventDispatcherTrait<TSubject>
     */
    use EventDispatcherTrait;

    use ArgumentResolverTrait;

    use ArgumentReflectorTrait;

    /**
     * The controller that this collection is associated with.
     *
     * @var \Cake\Controller\Controller|null
     */
    protected ?Controller $_Controller = null;

    /**
     * @var \Cake\Core\ContainerInterface|null
     */
    protected ?ContainerInterface $container = null;

    /**
     * Constructor.
     *
     * @param \Cake\Controller\Controller|null $controller Controller instance.
     * @param \Cake\Core\ContainerInterface|null $container Container instance.
     */
    public function __construct(?Controller $controller = null, ?ContainerInterface $container = null)
    {
        if ($controller !== null) {
            $this->setController($controller);
        }
        $this->container = $container;
    }

    /**
     * Set the controller associated with the collection.
     *
     * @param \Cake\Controller\Controller $controller Controller instance.
     * @return $this
     */
    public function setController(Controller $controller)
    {
        $this->_Controller = $controller;
        $this->setEventManager($controller->getEventManager());

        return $this;
    }

    /**
     * Get the controller associated with the collection.
     *
     * @return \Cake\Controller\Controller Controller instance.