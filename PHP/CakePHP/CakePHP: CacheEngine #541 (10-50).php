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