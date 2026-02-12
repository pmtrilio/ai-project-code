<?php
namespace Slim\Flash;

use ArrayAccess;
use RuntimeException;
use InvalidArgumentException;

/**
 * Flash messages
 */
class Messages
{
    /**
     * Messages from previous request
     *
     * @var string[]
     */
    protected $fromPrevious = [];

    /**
     * Messages for current request
     *
     * @var string[]
     */
    protected $forNow = [];

    /**
     * Messages for next request
     *
     * @var string[]
     */
    protected $forNext = [];

    /**
     * Message storage
     *
     * @var null|array|ArrayAccess
     */
    protected $storage;

    /**
     * Message storage key
     *
     * @var string
     */
    protected $storageKey = 'slimFlash';

    /**
     * Create new Flash messages service provider
     *
     * @param null|array|ArrayAccess $storage
     * @throws RuntimeException if the session cannot be found
     * @throws InvalidArgumentException if the store is not array-like
     */
    public function __construct(&$storage = null, $storageKey = null)
    {
        if (is_string($storageKey) && $storageKey) {
            $this->storageKey = $storageKey;
        }

        // Set storage