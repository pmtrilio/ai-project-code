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