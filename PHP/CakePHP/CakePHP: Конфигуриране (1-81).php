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
 * @since         1.0.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Core;

use Cake\Cache\Cache;
use Cake\Core\Configure\ConfigEngineInterface;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Exception\CakeException;
use Cake\Utility\Hash;

/**
 * Configuration class. Used for managing runtime configuration information.
 *
 * Provides features for reading and writing to the runtime configuration, as well
 * as methods for loading additional configuration files or storing runtime configuration
 * for future use.
 *
 * @link https://book.cakephp.org/5/en/development/configuration.html
 */
class Configure
{
    /**
     * Array of values currently stored in Configure.
     *
     * @var array<string, mixed>
     */
    protected static array $_values = [
        'debug' => false,
    ];

    /**
     * Configured engine classes, used to load config files from resources
     *
     * @see \Cake\Core\Configure::load()
     * @var array<\Cake\Core\Configure\ConfigEngineInterface>
     */
    protected static array $_engines = [];

    /**
     * Flag to track whether ini_set exists.
     *
     * @var bool|null
     */
    protected static ?bool $_hasIniSet = null;

    /**
     * Used to store a dynamic variable in Configure.
     *
     * Usage:
     * ```
     * Configure::write('One.key1', 'value of the Configure::One[key1]');
     * Configure::write(['One.key1' => 'value of the Configure::One[key1]']);
     * Configure::write('One', [
     *     'key1' => 'value of the Configure::One[key1]',
     *     'key2' => 'value of the Configure::One[key2]'
     * ]);
     *
     * Configure::write([
     *     'One.key1' => 'value of the Configure::One[key1]',
     *     'One.key2' => 'value of the Configure::One[key2]'
     * ]);
     * ```
     *
     * @param array<string, mixed>|string $config The key to write, can be a dot notation value.
     * Alternatively can be an array containing key(s) and value(s).
     * @param mixed $value Value to set for the given key.
     * @return void