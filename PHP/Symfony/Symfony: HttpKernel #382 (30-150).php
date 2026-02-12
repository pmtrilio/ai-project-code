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

        try {
            $response = $this->handleThrowable($exception, $request, self::MAIN_REQUEST);
        } finally {
            if ($pop) {
                $this->requestStack->pop();
            }
        }

        $response->sendHeaders();
        $response->sendContent();

        $this->terminate($request, $response);
    }

    /**
     * Handles a request to convert it to a response.
     *
     * Exceptions are not caught.
     *
     * @throws \LogicException       If one of the listener does not behave as expected