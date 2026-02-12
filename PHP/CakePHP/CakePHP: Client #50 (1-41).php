<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         3.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Http;

use Cake\Core\App;
use Cake\Core\Exception\CakeException;
use Cake\Core\InstanceConfigTrait;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Http\Client\Adapter\Curl;
use Cake\Http\Client\Adapter\Mock as MockAdapter;
use Cake\Http\Client\Adapter\Stream;
use Cake\Http\Client\AdapterInterface;
use Cake\Http\Client\ClientEvent;
use Cake\Http\Client\Request;
use Cake\Http\Client\Response;
use Cake\Http\Cookie\CookieCollection;
use Cake\Http\Cookie\CookieInterface;
use Cake\Utility\Hash;
use InvalidArgumentException;
use Laminas\Diactoros\Uri;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * The end user interface for doing HTTP requests.
 *