{
    use Concerns\CanBePrecognitive,
        Concerns\InteractsWithContentTypes,
        Concerns\InteractsWithFlashData,
        Concerns\InteractsWithInput,
        Conditionable,
        Macroable;

    /**
     * The decoded JSON content for the request.
     *
     * @var \Symfony\Component\HttpFoundation\InputBag|null
     */
    protected $json;

    /**
     * All of the converted files for the request.
     *
     * @var array<int, \Illuminate\Http\UploadedFile|\Illuminate\Http\UploadedFile[]>
     */
    protected $convertedFiles;

    /**
     * The user resolver callback.
     *
     * @var \Closure
     */
    protected $userResolver;

    /**
     * The route resolver callback.
     *
     * @var \Closure
     */
    protected $routeResolver;

    /**
     * The cached "Accept" header value.
     *
     * @var string|null
     */