use LogicException;
use RuntimeException;
use UnitEnum;

use function Illuminate\Support\enum_value;

class Builder implements BuilderContract
{
    /** @use \Illuminate\Database\Concerns\BuildsQueries<\stdClass> */
    use BuildsWhereDateClauses, BuildsQueries, ExplainsQueries, ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    public $connection;

    /**
     * The database query grammar instance.
     *
     * @var \Illuminate\Database\Query\Grammars\Grammar
     */
    public $grammar;

    /**
     * The database query post processor instance.
     *
     * @var \Illuminate\Database\Query\Processors\Processor
     */
    public $processor;

    /**
     * The current query value bindings.
     *
     * @var array{
     *     select: list<mixed>,
     *     from: list<mixed>,
     *     join: list<mixed>,
     *     where: list<mixed>,
     *     groupBy: list<mixed>,
     *     having: list<mixed>,
     *     order: list<mixed>,
     *     union: list<mixed>,
     *     unionOrder: list<mixed>,
     * }
     */
    public $bindings = [
        'select' => [],
        'from' => [],
        'join' => [],
        'where' => [],
        'groupBy' => [],
        'having' => [],
        'order' => [],
        'union' => [],
        'unionOrder' => [],
    ];

    /**
     * An aggregate function and column to be run.
     *
     * @var array{
     *     function: string,
     *     columns: array<\Illuminate\Contracts\Database\Query\Expression|string>
     * }|null
     */
    public $aggregate;

    /**
     * The columns that should be returned.
     *
     * @var array<string|\Illuminate\Contracts\Database\Query\Expression>|null
     */
    public $columns;

    /**
     * Indicates if the query returns distinct results.
     *