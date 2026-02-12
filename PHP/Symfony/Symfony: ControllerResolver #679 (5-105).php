 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * This implementation uses the '_controller' request attribute to determine
 * the controller to execute.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Tobias Schultze <http://tobion.de>
 */
class ControllerResolver implements ControllerResolverInterface
{
    private ?LoggerInterface $logger;
    private array $allowedControllerTypes = [];
    private array $allowedControllerAttributes = [AsController::class => AsController::class];

    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param array<class-string> $types
     * @param array<class-string> $attributes
     */
    public function allowControllers(array $types = [], array $attributes = []): void
    {
        foreach ($types as $type) {
            $this->allowedControllerTypes[$type] = $type;
        }

        foreach ($attributes as $attribute) {
            $this->allowedControllerAttributes[$attribute] = $attribute;
        }
    }

    /**
     * @throws BadRequestException when the request has attribute "_check_controller_is_allowed" set to true and the controller is not allowed
     */
    public function getController(Request $request): callable|false
    {
        if (!$controller = $request->attributes->get('_controller')) {
            $this->logger?->warning('Unable to look for the controller as the "_controller" parameter is missing.');

            return false;
        }

        if (\is_array($controller)) {
            if (isset($controller[0]) && \is_string($controller[0]) && isset($controller[1])) {
                try {
                    $controller[0] = $this->instantiateController($controller[0]);
                } catch (\Error|\LogicException $e) {
                    if (\is_callable($controller)) {
                        return $this->checkController($request, $controller);
                    }

                    throw $e;
                }
            }

            if (!\is_callable($controller)) {
                throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($controller));
            }

            return $this->checkController($request, $controller);
        }

        if (\is_object($controller)) {
            if (!\is_callable($controller)) {
                throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($controller));
            }

            return $this->checkController($request, $controller);
        }

        if (\function_exists($controller)) {
            return $this->checkController($request, $controller);
        }

        try {
            $callable = $this->createController($controller);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$e->getMessage(), 0, $e);
        }

        if (!\is_callable($callable)) {
            throw new \InvalidArgumentException(\sprintf('The controller for URI "%s" is not callable: ', $request->getPathInfo()).$this->getControllerError($callable));
        }

        return $this->checkController($request, $callable);