<?php

namespace Illuminate\Database\Schema;

use Closure;
use Illuminate\Container\Container;
use Illuminate\Database\Connection;
use Illuminate\Database\PostgresConnection;
use Illuminate\Support\Traits\Macroable;
use InvalidArgumentException;
use LogicException;
use RuntimeException;

class Builder
{
    use Macroable;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\Connection
     */
    protected $connection;

    /**
     * The schema grammar instance.
     *
     * @var \Illuminate\Database\Schema\Grammars\Grammar
     */
    protected $grammar;

    /**
     * The Blueprint resolver callback.
     *
     * @var \Closure(\Illuminate\Database\Connection, string, \Closure|null): \Illuminate\Database\Schema\Blueprint
     */
    protected $resolver;

    /**
     * The default string length for migrations.
     *
     * @var int|null
     */
    public static $defaultStringLength = 255;

    /**
     * The default time precision for migrations.
     */
    public static ?int $defaultTimePrecision = 0;

    /**
     * The default relationship morph key type.
     *
     * @var string
     */
    public static $defaultMorphKeyType = 'int';

    /**
     * Create a new database Schema manager.
     *
     * @param  \Illuminate\Database\Connection  $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->grammar = $connection->getSchemaGrammar();
    }

    /**
     * Set the default string length for migrations.
     *
     * @param  int  $length
     * @return void
     */
    public static function defaultStringLength($length)
    {
        static::$defaultStringLength = $length;
    }

    /**
     * Set the default time precision for migrations.