<?php

declare(strict_types=1);

namespace Doctrine\ORM;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\Psr6\CacheAdapter;
use Doctrine\Common\Cache\Psr6\DoctrineProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\LockMode;
use Doctrine\DBAL\Types\Type;
use Doctrine\Deprecations\Deprecation;
use Doctrine\ORM\Internal\Hydration\IterableResult;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\AST\DeleteStatement;
use Doctrine\ORM\Query\AST\SelectStatement;
use Doctrine\ORM\Query\AST\UpdateStatement;
use Doctrine\ORM\Query\Exec\AbstractSqlExecutor;
use Doctrine\ORM\Query\Exec\SqlFinalizer;
use Doctrine\ORM\Query\OutputWalker;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ParameterTypeInferer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\ParserResult;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Utility\HierarchyDiscriminatorResolver;
use Psr\Cache\CacheItemPoolInterface;

use function array_keys;
use function array_values;
use function assert;
use function count;
use function get_debug_type;
use function in_array;
use function is_a;
use function is_int;
use function ksort;
use function md5;