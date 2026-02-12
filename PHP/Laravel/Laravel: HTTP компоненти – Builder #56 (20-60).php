use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
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