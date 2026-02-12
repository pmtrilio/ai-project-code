 *
 * Command supports SQL statement preparation and parameter binding.
 * Call [[bindValue()]] to bind a value to a SQL parameter;
 * Call [[bindParam()]] to bind a PHP variable to a SQL parameter.
 * When binding a parameter, the SQL statement is automatically prepared.
 * You may also call [[prepare()]] explicitly to prepare a SQL statement.
 *
 * Command also supports building SQL statements by providing methods such as [[insert()]],
 * [[update()]], etc. For example, the following code will create and execute an INSERT SQL statement:
 *
 * ```
 * $connection->createCommand()->insert('user', [
 *     'name' => 'Sam',
 *     'age' => 30,
 * ])->execute();
 * ```
 *
 * To build SELECT SQL statements, please use [[Query]] instead.
 *
 * For more details and usage information on Command, see the [guide article on Database Access Objects](guide:db-dao).
 *
 * @property string $rawSql The raw SQL with parameter values inserted into the corresponding placeholders in
 * [[sql]].
 * @property string $sql The SQL statement to be executed.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Command extends Component
{
    /**
     * @var Connection the DB connection that this command is associated with
     */
    public $db;
    /**
     * @var \PDOStatement|null the PDOStatement object that this command is associated with
     */
    public $pdoStatement;
    /**
     * @var int the default fetch mode for this command.
     * @see https://www.php.net/manual/en/pdostatement.setfetchmode.php
     */
    public $fetchMode = \PDO::FETCH_ASSOC;
    /**
     * @var array the parameters (name => value) that are bound to the current PDO statement.
     * This property is maintained by methods such as [[bindValue()]]. It is mainly provided for logging purpose
     * and is used to generate [[rawSql]]. Do not modify it directly.
     */
    public $params = [];
    /**
     * @var int the default number of seconds that query results can remain valid in cache.
     * Use 0 to indicate that the cached data will never expire. And use a negative number to indicate
     * query cache should not be used.
     * @see cache()
     */
    public $queryCacheDuration;
    /**
     * @var \yii\caching\Dependency the dependency to be associated with the cached query result for this command
     * @see cache()
     */
    public $queryCacheDependency;