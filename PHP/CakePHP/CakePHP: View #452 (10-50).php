 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\View;

use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Core\Exception\CakeException;
use Cake\Core\InstanceConfigTrait;
use Cake\Core\Plugin;
use Cake\Event\EventDispatcherInterface;
use Cake\Event\EventDispatcherTrait;
use Cake\Event\EventManagerInterface;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Log\LogTrait;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\View\Exception\MissingElementException;
use Cake\View\Exception\MissingLayoutException;
use Cake\View\Exception\MissingTemplateException;
use Generator;
use InvalidArgumentException;
use LogicException;
use Throwable;
use function Cake\Core\pluginSplit;

/**
 * View, the V in the MVC triad. View interacts with Helpers and view variables passed
 * in from the controller to render the results of the controller action. Often this is HTML,
 * but can also take the form of JSON, XML, PDF's or streaming files.
 *
 * CakePHP uses a two-step-view pattern. This means that the template content is rendered first,
 * and then inserted into the selected layout. This also means you can pass data from the template to the
 * layout using `$this->set()`
 *
 * View class supports using plugins as themes. You can set