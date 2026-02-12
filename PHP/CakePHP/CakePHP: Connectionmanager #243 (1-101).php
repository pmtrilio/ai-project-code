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
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Datasource;

use Cake\Core\StaticConfigTrait;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Database\Driver\Sqlite;
use Cake\Database\Driver\Sqlserver;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Closure;

/**
 * Manages and loads instances of Connection
 *
 * Provides an interface to loading and creating connection objects. Acts as
 * a registry for the connections defined in an application.
 *
 * Provides an interface for loading and enumerating connections defined in
 * config/app.php
 */
class ConnectionManager
{
    use StaticConfigTrait {
        setConfig as protected _setConfig;
        parseDsn as protected _parseDsn;
    }

    /**
     * A map of connection aliases.
     *
     * @var array<string, string>
     */
    protected static array $_aliasMap = [];

    /**
     * An array mapping url schemes to fully qualified driver class names
     *
     * @var array<string, string>
     * @phpstan-var array<string, class-string>
     */
    protected static array $_dsnClassMap = [
        'mysql' => Mysql::class,
        'postgres' => Postgres::class,
        'sqlite' => Sqlite::class,
        'sqlserver' => Sqlserver::class,
    ];

    /**
     * The ConnectionRegistry used by the manager.
     *
     * @var \Cake\Datasource\ConnectionRegistry
     */
    protected static ConnectionRegistry $_registry;

    /**
     * Configure a new connection object.
     *
     * The connection will not be constructed until it is first used.
     *
     * @param array<string, mixed>|string $key The name of the connection config, or an array of multiple configs.
     * @param \Cake\Datasource\ConnectionInterface|\Closure|array<string, mixed>|null $config An array of name => config data for adapter.
     * @return void
     * @throws \Cake\Core\Exception\CakeException When trying to modify an existing config.
     * @see \Cake\Core\StaticConfigTrait::config()
     */
    public static function setConfig(array|string $key, ConnectionInterface|Closure|array|null $config = null): void
    {
        if (is_array($config)) {
            $config['name'] = $key;
        }

        static::_setConfig($key, $config);
    }

    /**
     * Parses a DSN into a valid connection configuration
     *
     * This method allows setting a DSN using formatting similar to that used by PEAR::DB.
     * The following is an example of its usage:
     *
     * ```
     * $dsn = 'mysql://user:pass@localhost/database';
     * $config = ConnectionManager::parseDsn($dsn);
     *
     * $dsn = 'Cake\Database\Driver\Mysql://localhost:3306/database?className=Cake\Database\Connection';