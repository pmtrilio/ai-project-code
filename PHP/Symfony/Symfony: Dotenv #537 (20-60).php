/**
 * Manages .env files.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Kévin Dunglas <dunglas@gmail.com>
 */
final class Dotenv
{
    public const VARNAME_REGEX = '(?i:_?[A-Z][A-Z0-9_]*+)';
    public const STATE_VARNAME = 0;
    public const STATE_VALUE = 1;

    private string $path;
    private int $cursor;
    private int $lineno;
    private string $data;
    private int $end;
    private array $values = [];
    private string $envKey;
    private string $debugKey;
    private array $prodEnvs = ['prod'];
    private bool $usePutenv = false;

    public function __construct(string $envKey = 'APP_ENV', string $debugKey = 'APP_DEBUG')
    {
        $this->envKey = $envKey;
        $this->debugKey = $debugKey;
    }

    /**
     * @return $this
     */
    public function setProdEnvs(array $prodEnvs): static
    {
        $this->prodEnvs = $prodEnvs;

        return $this;
    }

    /**
     * @param bool $usePutenv If `putenv()` should be used to define environment variables or not.