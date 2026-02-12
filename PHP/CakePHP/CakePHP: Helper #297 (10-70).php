 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\View;

use Cake\Core\InstanceConfigTrait;
use Cake\Event\EventListenerInterface;

/**
 * Abstract base class for all other Helpers in CakePHP.
 * Provides common methods and features.
 *
 * ### Callback methods
 *
 * Helpers support a number of callback methods. These callbacks allow you to hook into
 * the various view lifecycle events and either modify existing view content or perform
 * other application specific logic. The events are not implemented by this base class, as
 * implementing a callback method subscribes a helper to the related event. The callback methods
 * are as follows:
 *
 * - `beforeRender(EventInterface $event, $viewFile)` - beforeRender is called before the view file is rendered.
 * - `afterRender(EventInterface $event, $viewFile)` - afterRender is called after the view file is rendered
 *   but before the layout has been rendered.
 * - beforeLayout(EventInterface $event, $layoutFile)` - beforeLayout is called before the layout is rendered.
 * - `afterLayout(EventInterface $event, $layoutFile)` - afterLayout is called after the layout has rendered.
 * - `beforeRenderFile(EventInterface $event, $viewFile)` - Called before any view fragment is rendered.
 * - `afterRenderFile(EventInterface $event, $viewFile, $content)` - Called after any view fragment is rendered.
 *   If a listener returns a non-null value, the output of the rendered file will be set to that.
 */
class Helper implements EventListenerInterface
{
    use InstanceConfigTrait;

    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    protected array $helpers = [];

    /**
     * Default config for this helper.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [];

    /**
     * Loaded helper instances.
     *
     * @var array<string, \Cake\View\Helper>
     */
    protected array $helperInstances = [];

    /**
     * The View instance this helper is attached to
     *