<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\web;

use Yii;
use yii\base\InvalidConfigException;
use yii\validators\IpValidator;

/**
 * The web Request class represents an HTTP request.
 *
 * It encapsulates the $_SERVER variable and resolves its inconsistency among different Web servers.
 * Also it provides an interface to retrieve request parameters from $_POST, $_GET, $_COOKIES and REST
 * parameters sent via other HTTP methods like PUT or DELETE.
 *
 * Request is configured as an application component in [[\yii\web\Application]] by default.
 * You can access that instance via `Yii::$app->request`.
 *
 * For more details and usage information on Request, see the [guide article on requests](guide:runtime-requests).
 *
 * @property string|null $hostInfo Schema and hostname part (with port number if needed) of the request URL
 * (e.g. `https://www.yiiframework.com`), null if can't be obtained from `$_SERVER` and wasn't set. See
 * [[getHostInfo()]] for security related notes on this property.
 * @property-read string $absoluteUrl The currently requested absolute URL.
 * @property array $acceptableContentTypes The content types ordered by the quality score. Types with the
 * highest scores will be returned first. The array keys are the content types, while the array values are the
 * corresponding quality score and other parameters as given in the header.
 * @property array $acceptableLanguages The languages ordered by the preference level. The first element
 * represents the most preferred language.
 * @property-read array $authCredentials That contains exactly two elements: - 0: the username sent via HTTP
 * authentication, `null` if the username is not given - 1: the password sent via HTTP authentication, `null` if
 * the password is not given.
 * @property-read string|null $authPassword The password sent via HTTP authentication, `null` if the password
 * is not given.
 * @property-read string|null $authUser The username sent via HTTP authentication, `null` if the username is
 * not given.
 * @property string $baseUrl The relative URL for the application.
 * @property array|object $bodyParams The request parameters given in the request body.
 * @property-read string $contentType Request content-type. Empty string is returned if this information is
 * not available.
 * @property-read CookieCollection $cookies The cookie collection.
 * @property-read null|string $csrfToken The token used to perform CSRF validation. Null is returned if the
 * [[validateCsrfHeaderOnly]] is true.
 * @property-read string|null $csrfTokenFromHeader The CSRF token sent via [[csrfHeader]] by browser. Null is
 * returned if no such header is sent.
 * @property-read array $eTags The entity tags.
 * @property-read HeaderCollection $headers The header collection.
 * @property-read string|null $hostName Hostname part of the request URL (e.g. `www.yiiframework.com`).
 * @property-read bool $isAjax Whether this is an AJAX (XMLHttpRequest) request.
 * @property-read bool $isDelete Whether this is a DELETE request.
 * @property-read bool $isFlash Whether this is an Adobe Flash or Adobe Flex request.
 * @property-read bool $isGet Whether this is a GET request.
 * @property-read bool $isHead Whether this is a HEAD request.
 * @property-read bool $isOptions Whether this is a OPTIONS request.