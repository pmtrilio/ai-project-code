<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml;

use Symfony\Component\Yaml\Exception\ParseException;

/**
 * Yaml offers convenience methods to load and dump YAML.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class Yaml
{
    public const DUMP_OBJECT = 1;
    public const PARSE_EXCEPTION_ON_INVALID_TYPE = 2;
    public const PARSE_OBJECT = 4;
    public const PARSE_OBJECT_FOR_MAP = 8;
    public const DUMP_EXCEPTION_ON_INVALID_TYPE = 16;
    public const PARSE_DATETIME = 32;
    public const DUMP_OBJECT_AS_MAP = 64;
    public const DUMP_MULTI_LINE_LITERAL_BLOCK = 128;
    public const PARSE_CONSTANT = 256;
    public const PARSE_CUSTOM_TAGS = 512;
    public const DUMP_EMPTY_ARRAY_AS_SEQUENCE = 1024;
    public const DUMP_NULL_AS_TILDE = 2048;
    public const DUMP_NUMERIC_KEY_AS_STRING = 4096;

    /**
     * Parses a YAML file into a PHP value.
     *