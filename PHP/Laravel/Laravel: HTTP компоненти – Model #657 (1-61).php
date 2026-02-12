<?php

namespace Illuminate\Database\Eloquent;

use ArrayAccess;
use Closure;
use Exception;
use Illuminate\Contracts\Broadcasting\HasBroadcastChannel;
use Illuminate\Contracts\Queue\QueueableCollection;
use Illuminate\Contracts\Queue\QueueableEntity;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\CanBeEscapedWhenCastToString;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Illuminate\Database\Eloquent\Attributes\Initialize;
use Illuminate\Database\Eloquent\Attributes\Scope as LocalScope;
use Illuminate\Database\Eloquent\Attributes\UseEloquentBuilder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Concerns\AsPivot;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection as BaseCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable as SupportStringable;
use Illuminate\Support\Traits\ForwardsCalls;
use JsonException;
use JsonSerializable;
use LogicException;
use ReflectionClass;
use ReflectionMethod;
use Stringable;

use function Illuminate\Support\enum_value;

abstract class Model implements Arrayable, ArrayAccess, CanBeEscapedWhenCastToString, HasBroadcastChannel, Jsonable, JsonSerializable, QueueableEntity, Stringable, UrlRoutable
{
    use Concerns\HasAttributes,
        Concerns\HasEvents,
        Concerns\HasGlobalScopes,
        Concerns\HasRelationships,
        Concerns\HasTimestamps,
        Concerns\HasUniqueIds,
        Concerns\HidesAttributes,
        Concerns\GuardsAttributes,
        Concerns\PreventsCircularRecursion,
        Concerns\TransformsToResource,
        ForwardsCalls;
    /** @use HasCollection<\Illuminate\Database\Eloquent\Collection<array-key, static & self>> */
    use HasCollection;

    /**
     * The connection name for the model.
     *
     * @var \UnitEnum|string|null
     */
    protected $connection;
