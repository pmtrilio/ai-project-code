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