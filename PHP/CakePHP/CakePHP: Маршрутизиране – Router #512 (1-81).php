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
 * @since         0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Routing;

use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\Http\ServerRequest;
use Cake\Routing\Exception\MissingRouteException;
use Cake\Routing\Route\Route;
use Closure;
use InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use ReflectionFunction;
use Throwable;

/**
 * Parses the request URL into controller, action, and parameters. Uses the connected routes
 * to match the incoming URL string to parameters that will allow the request to be dispatched. Also
 * handles converting parameter lists into URL strings, using the connected routes. Routing allows you to decouple
 * the way the world interacts with your application (URLs) and the implementation (controllers and actions).
 */
class Router
{
    /**
     * Default route class.
     *
     * @var string
     */
    protected static string $_defaultRouteClass = Route::class;

    /**
     * Contains the base string that will be applied to all generated URLs
     * For example `https://example.com`
     *
     * @var string|null
     */
    protected static ?string $_fullBaseUrl = null;

    /**
     * Regular expression for action names
     *
     * @var string
     */
    public const ACTION = 'index|show|add|create|edit|update|remove|del|delete|view|item';

    /**
     * Regular expression for years
     *
     * @var string
     */
    public const YEAR = '[12][0-9]{3}';

    /**
     * Regular expression for months
     *
     * @var string
     */
    public const MONTH = '0[1-9]|1[012]';

    /**
     * Regular expression for days
     *
     * @var string
     */
    public const DAY = '0[1-9]|[12][0-9]|3[01]';

    /**