     * @var string
     */
    const INVISIBLE_CHARACTERS = '\x{0009}\x{0020}\x{00A0}\x{00AD}\x{034F}\x{061C}\x{115F}\x{1160}\x{17B4}\x{17B5}\x{180E}\x{2000}\x{2001}\x{2002}\x{2003}\x{2004}\x{2005}\x{2006}\x{2007}\x{2008}\x{2009}\x{200A}\x{200B}\x{200C}\x{200D}\x{200E}\x{200F}\x{202F}\x{205F}\x{2060}\x{2061}\x{2062}\x{2063}\x{2064}\x{2065}\x{206A}\x{206B}\x{206C}\x{206D}\x{206E}\x{206F}\x{3000}\x{2800}\x{3164}\x{FEFF}\x{FFA0}\x{1D159}\x{1D173}\x{1D174}\x{1D175}\x{1D176}\x{1D177}\x{1D178}\x{1D179}\x{1D17A}\x{E0020}';

    /**
     * The cache of snake-cased words.
     *
     * @var array<string, string>
     */
    protected static $snakeCache = [];

    /**
     * The cache of camel-cased words.
     *
     * @var array<string, string>
     */
    protected static $camelCache = [];

    /**
     * The cache of studly-cased words.
     *
     * @var array<string, string>
     */
    protected static $studlyCache = [];

    /**
     * The callback that should be used to generate UUIDs.
     *
     * @var (callable(): \Ramsey\Uuid\UuidInterface)|null
     */
    protected static $uuidFactory;

    /**
     * The callback that should be used to generate ULIDs.
     *
     * @var (callable(): \Symfony\Component\Uid\Ulid)|null
     */
    protected static $ulidFactory;

    /**
     * The callback that should be used to generate random strings.
     *
     * @var (callable(int): string)|null
     */
    protected static $randomStringFactory;

    /**
     * Get a new stringable object from the given string.
     *
     * @param  string  $string
     * @return \Illuminate\Support\Stringable
     */
    public static function of($string)
    {
        return new Stringable($string);
    }

    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function after($subject, $search)
    {
        return $search === '' ? $subject : array_reverse(explode($search, $subject, 2))[0];
    }

    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param  string  $subject
     * @param  string  $search
     * @return string
     */
    public static function afterLast($subject, $search)
    {
        if ($search === '') {
            return $subject;
        }