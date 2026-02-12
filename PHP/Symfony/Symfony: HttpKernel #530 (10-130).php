 */

namespace Symfony\Component\HttpKernel;

use Symfony\Component\HttpFoundation\Exception\RequestExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolverInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ControllerDoesNotReturnResponseException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

// Help opcache.preload discover always-needed symbols
class_exists(ControllerArgumentsEvent::class);
class_exists(ControllerEvent::class);
class_exists(ExceptionEvent::class);
class_exists(FinishRequestEvent::class);
class_exists(RequestEvent::class);
class_exists(ResponseEvent::class);
class_exists(TerminateEvent::class);
class_exists(ViewEvent::class);
class_exists(KernelEvents::class);

/**
 * HttpKernel notifies events to convert a Request object to a Response one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class HttpKernel implements HttpKernelInterface, TerminableInterface
{
    protected $dispatcher;
    protected $resolver;
    protected $requestStack;
    private ArgumentResolverInterface $argumentResolver;
    private bool $handleAllThrowables;

    public function __construct(EventDispatcherInterface $dispatcher, ControllerResolverInterface $resolver, ?RequestStack $requestStack = null, ?ArgumentResolverInterface $argumentResolver = null, bool $handleAllThrowables = false)
    {
        $this->dispatcher = $dispatcher;
        $this->resolver = $resolver;
        $this->requestStack = $requestStack ?? new RequestStack();
        $this->argumentResolver = $argumentResolver ?? new ArgumentResolver();
        $this->handleAllThrowables = $handleAllThrowables;
    }

    public function handle(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST, bool $catch = true): Response
    {
        $request->headers->set('X-Php-Ob-Level', (string) ob_get_level());

        $this->requestStack->push($request);
        $response = null;
        try {
            return $response = $this->handleRaw($request, $type);
        } catch (\Throwable $e) {
            if ($e instanceof \Error && !$this->handleAllThrowables) {
                throw $e;
            }

            if ($e instanceof RequestExceptionInterface) {
                $e = new BadRequestHttpException($e->getMessage(), $e);
            }
            if (false === $catch) {
                $this->finishRequest($request, $type);

                throw $e;
            }

            return $response = $this->handleThrowable($e, $request, $type);
        } finally {
            $this->requestStack->pop();

            if ($response instanceof StreamedResponse && $callback = $response->getCallback()) {
                $requestStack = $this->requestStack;

                $response->setCallback(static function () use ($request, $callback, $requestStack) {
                    $requestStack->push($request);
                    try {
                        $callback();
                    } finally {
                        $requestStack->pop();
                    }
                });
            }
        }
    }

    /**
     * @return void
     */
    public function terminate(Request $request, Response $response)
    {
        $this->dispatcher->dispatch(new TerminateEvent($this, $request, $response), KernelEvents::TERMINATE);
    }

    /**
     * @internal
     */
    public function terminateWithException(\Throwable $exception, ?Request $request = null): void
    {
        if (!$request ??= $this->requestStack->getMainRequest()) {
            throw $exception;
        }

        if ($pop = $request !== $this->requestStack->getMainRequest()) {
            $this->requestStack->push($request);
        }
