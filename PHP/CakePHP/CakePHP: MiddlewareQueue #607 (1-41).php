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
 * @since         3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Http;

use Cake\Core\App;
use Cake\Core\ContainerInterface;
use Cake\Http\Middleware\ClosureDecoratorMiddleware;
use Closure;
use Countable;
use InvalidArgumentException;
use LogicException;
use OutOfBoundsException;
use Psr\Http\Server\MiddlewareInterface;
use SeekableIterator;

/**
 * Provides methods for creating and manipulating a "queue" of middlewares.
 * This queue is used to process a request and generate response via \Cake\Http\Runner.
 *
 * @template-implements \SeekableIterator<int, \Psr\Http\Server\MiddlewareInterface>
 */
class MiddlewareQueue implements Countable, SeekableIterator
{
    /**
     * Internal position for iterator.
     *
     * @var int