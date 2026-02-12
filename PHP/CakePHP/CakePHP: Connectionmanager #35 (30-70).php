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
