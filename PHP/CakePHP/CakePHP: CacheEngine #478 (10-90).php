 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Cache;

use Cake\Cache\Event\CacheAfterAddEvent;
use Cake\Cache\Event\CacheBeforeAddEvent;
use Cake\Cache\Exception\InvalidArgumentException;
use Cake\Core\InstanceConfigTrait;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use DateInterval;
use DateTime;
use Psr\SimpleCache\CacheInterface;
use function Cake\Core\triggerWarning;

/**
 * Storage engine for CakePHP caching
 *
 * @template TSubject of \Cake\Cache\CacheEngine
 * @implements \Cake\Event\EventDispatcherInterface<TSubject>
 */
abstract class CacheEngine implements CacheInterface, CacheEngineInterface, EventDispatcherInterface
{
    /**
     * @use \Cake\Event\EventDispatcherTrait<TSubject>
     */
    use EventDispatcherTrait;
    use InstanceConfigTrait;

    /**
     * @var string
     */
    protected const CHECK_KEY = 'key';

    /**
     * @var string
     */
    protected const CHECK_VALUE = 'value';

    /**
     * The default cache configuration is overridden in most cache adapters. These are
     * the keys that are common to all adapters. If overridden, this property is not used.
     *
     * - `duration` Specify how long items in this cache configuration last.
     * - `groups` List of groups or 'tags' associated to every key stored in this config.
     *    handy for deleting a complete group from cache.
     * - `prefix` Prefix appended to all entries. Good for when you need to share a keyspace
     *    with either another cache config or another application.
     * - `warnOnWriteFailures` Some engines, such as ApcuEngine, may raise warnings on
     *    write failures.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'duration' => 3600,
        'groups' => [],
        'prefix' => 'cake_',
        'warnOnWriteFailures' => true,
    ];

    /**
     * Contains the compiled string with all group
     * prefixes to be prepended to every key in this cache engine
     *
     * @var string
     */
    protected string $_groupPrefix = '';

    /**
     * Initialize the cache engine
     *
     * Called automatically by the cache frontend. Merge the runtime config with the defaults
     * before use.
     *
     * @param array<string, mixed> $config Associative array of parameters for the engine
     * @return bool True if the engine has been successfully initialized, false if not