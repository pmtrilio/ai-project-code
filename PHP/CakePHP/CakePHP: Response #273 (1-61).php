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
namespace Cake\Http\Client;

use Cake\Core\Exception\CakeException;
use Cake\Http\Cookie\CookieCollection;
use Laminas\Diactoros\MessageTrait;
use Laminas\Diactoros\Stream;
use Psr\Http\Message\ResponseInterface;
use SimpleXMLElement;

/**
 * Implements methods for HTTP responses.
 *
 * All the following examples assume that `$response` is an
 * instance of this class.
 *
 * ### Get header values
 *
 * Header names are case-insensitive, but normalized to Title-Case
 * when the response is parsed.
 *
 * ```
 * $val = $response->getHeaderLine('content-type');
 * ```
 *
 * Will read the Content-Type header. You can get all set
 * headers using:
 *
 * ```
 * $response->getHeaders();
 * ```
 *
 * ### Get the response body
 *
 * You can access the response body stream using:
 *
 * ```
 * $content = $response->getBody();
 * ```
 *
 * You can get the body string using:
 *
 * ```
 * $content = $response->getStringBody();
 * ```
 *
 * If your response body is in XML or JSON you can use