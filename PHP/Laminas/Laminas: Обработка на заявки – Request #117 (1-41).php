<?php

namespace Laminas\Http;

use ArrayIterator;
use Laminas\Http\Header\HeaderInterface;
use Laminas\Http\Headers;
use Laminas\Stdlib\Parameters;
use Laminas\Stdlib\ParametersInterface;
use Laminas\Stdlib\RequestInterface;
use Laminas\Uri\Exception as UriException;
use Laminas\Uri\Http as HttpUri;

use function array_key_exists;
use function array_shift;
use function defined;
use function explode;
use function implode;
use function is_string;
use function parse_str;
use function parse_url;
use function preg_match;
use function sprintf;
use function stristr;
use function strtoupper;

/**
 * HTTP Request
 *
 * @link      http://www.w3.org/Protocols/rfc2616/rfc2616-sec5.html#sec5
 */
class Request extends AbstractMessage implements RequestInterface
{
    /**#@+
     *
     * @const string METHOD constant names
     */
    public const METHOD_OPTIONS  = 'OPTIONS';
    public const METHOD_GET      = 'GET';
    public const METHOD_HEAD     = 'HEAD';
    public const METHOD_POST     = 'POST';