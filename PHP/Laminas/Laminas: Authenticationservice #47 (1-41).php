<?php

declare(strict_types=1);

namespace Laminas\Authentication;

use Override;

use function assert;

class AuthenticationService implements AuthenticationServiceInterface
{
    /**
     * Persistent storage handler
     *
     * @var Storage\StorageInterface|null
     */
    protected $storage;

    /**
     * Authentication adapter
     *
     * @var Adapter\AdapterInterface|null
     */
    protected $adapter;

    /**
     * Constructor
     */
    public function __construct(?Storage\StorageInterface $storage = null, ?Adapter\AdapterInterface $adapter = null)
    {
        if (null !== $storage) {
            $this->setStorage($storage);
        }
        if (null !== $adapter) {
            $this->setAdapter($adapter);
        }
    }

    /**
     * Returns the authentication adapter