<?php

namespace Laminas\Http;

use Laminas\Stdlib\ErrorHandler;
use Laminas\Stdlib\ResponseInterface;

use function array_shift;
use function count;
use function explode;
use function function_exists;
use function gettype;
use function gzdecode;
use function gzinflate;
use function gzuncompress;
use function hexdec;
use function implode;
use function in_array;
use function is_array;
use function is_float;
use function is_numeric;
use function is_scalar;
use function ord;
use function preg_match;
use function sprintf;
use function strlen;
use function strtolower;
use function substr;
use function trim;
use function unpack;

/**
 * HTTP Response
 *
 * @link      http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html#sec6
 */
class Response extends AbstractMessage implements ResponseInterface
{
    /**#@+
     *
     * @const int Status codes
     */
    public const STATUS_CODE_CUSTOM = 0;
    public const STATUS_CODE_100    = 100;
    public const STATUS_CODE_101    = 101;
    public const STATUS_CODE_102    = 102;
    public const STATUS_CODE_200    = 200;
    public const STATUS_CODE_201    = 201;
    public const STATUS_CODE_202    = 202;
    public const STATUS_CODE_203    = 203;
    public const STATUS_CODE_204    = 204;
    public const STATUS_CODE_205    = 205;
    public const STATUS_CODE_206    = 206;
    public const STATUS_CODE_207    = 207;
    public const STATUS_CODE_208    = 208;
    public const STATUS_CODE_226    = 226;
    public const STATUS_CODE_300    = 300;
    public const STATUS_CODE_301    = 301;
    public const STATUS_CODE_302    = 302;
    public const STATUS_CODE_303    = 303;