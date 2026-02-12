 * // compose the query
 * $query->select('id, name')
 *     ->from('user')
 *     ->limit(10);
 * // build and execute the query
 * $rows = $query->all();
 * // alternatively, you can create DB command and execute it
 * $command = $query->createCommand();
 * // $command->sql returns the actual SQL
 * $rows = $command->queryAll();
 * ```
 *
 * Query internally uses the [[QueryBuilder]] class to generate the SQL statement.
 *
 * A more detailed usage guide on how to work with Query can be found in the [guide article on Query Builder](guide:db-query-builder).
 *
 * @property-read string[] $tablesUsedInFrom Table names indexed by aliases.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class Query extends Component implements QueryInterface, ExpressionInterface
{
    use QueryTrait;

    /**
     * @var array|null the columns being selected. For example, `['id', 'name']`.
     * This is used to construct the SELECT clause in a SQL statement. If not set, it means selecting all columns.
     * @see select()
     */
    public $select;
    /**
     * @var string|null additional option that should be appended to the 'SELECT' keyword. For example,
     * in MySQL, the option 'SQL_CALC_FOUND_ROWS' can be used.
     */
    public $selectOption;
    /**
     * @var bool whether to select distinct rows of data only. If this is set true,
     * the SELECT clause would be changed to SELECT DISTINCT.
     */
    public $distinct = false;
    /**
     * @var array|null the table(s) to be selected from. For example, `['user', 'post']`.
     * This is used to construct the FROM clause in a SQL statement.
     * @see from()
     */
    public $from;
    /**
     * @var array|null how to group the query results. For example, `['company', 'department']`.
     * This is used to construct the GROUP BY clause in a SQL statement.
     */
    public $groupBy;
    /**
     * @var array|null how to join with other tables. Each array element represents the specification
     * of one join which has the following structure:
     *
     * ```
     * [$joinType, $tableName, $joinCondition]
     * ```
     *
     * For example,
     *
     * ```
     * [
     *     ['INNER JOIN', 'user', 'user.id = author_id'],
     *     ['LEFT JOIN', 'team', 'team.id = team_id'],
     * ]
     * ```
     */
    public $join;
    /**
     * @var string|array|ExpressionInterface|null the condition to be applied in the GROUP BY clause.
     * It can be either a string or an array. Please refer to [[where()]] on how to specify the condition.
     */
    public $having;
    /**
     * @var array|null this is used to construct the UNION clause(s) in a SQL statement.
     * Each array element is an array of the following structure:
     *
     * - `query`: either a string or a [[Query]] object representing a query
     * - `all`: boolean, whether it should be `UNION ALL` or `UNION`
     */
    public $union;
    /**
     * @var array|null this is used to construct the WITH section in a SQL query.
     * Each array element is an array of the following structure:
     *
     * - `query`: either a string or a [[Query]] object representing a query
     * - `alias`: string, alias of query for further usage
     * - `recursive`: boolean, whether it should be `WITH RECURSIVE` or `WITH`
     * @see withQuery()
     * @since 2.0.35
     */
    public $withQueries;
    /**
     * @var array|null list of query parameter values indexed by parameter placeholders.
     * For example, `[':name' => 'Dan', ':age' => 31]`.
     */
    public $params = [];
    /**