<?php

declare(strict_types=1);

namespace Laminas\Permissions\Acl;

use Laminas\Permissions\Acl\Assertion\AssertionInterface;
use Laminas\Permissions\Acl\Exception\ExceptionInterface;
use Laminas\Permissions\Acl\Exception\InvalidArgumentException;
use Laminas\Permissions\Acl\Exception\RuntimeException;
use Laminas\Permissions\Acl\Resource\ResourceInterface;
use Throwable;

use function array_key_exists;
use function array_keys;
use function array_pop;
use function is_array;
use function is_string;
use function sprintf;
use function strtoupper;

class Acl implements AclInterface
{
    /**
     * Rule type: allow
     */
    public const TYPE_ALLOW = 'TYPE_ALLOW';

    /**
     * Rule type: deny
     */
    public const TYPE_DENY = 'TYPE_DENY';

    /**
     * Rule operation: add
     */
    public const OP_ADD = 'OP_ADD';

    /**
     * Rule operation: remove
     */