 */

namespace Symfony\Component\Dotenv;

use Symfony\Component\Dotenv\Exception\FormatException;
use Symfony\Component\Dotenv\Exception\FormatExceptionContext;
use Symfony\Component\Dotenv\Exception\PathException;
use Symfony\Component\Process\Exception\ExceptionInterface as ProcessException;
use Symfony\Component\Process\Process;

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
     *                        Beware that `putenv()` is not thread safe, that's why it's not enabled by default
     *
     * @return $this
     */
    public function usePutenv(bool $usePutenv = true): static
    {
        $this->usePutenv = $usePutenv;

        return $this;
    }

    /**
     * Loads one or several .env files.
     *
     * @param string $path          A file to load
     * @param string ...$extraPaths A list of additional files to load
     *
     * @throws FormatException when a file has a syntax error
     * @throws PathException   when a file does not exist or is not readable
     */
    public function load(string $path, string ...$extraPaths): void
    {
        $this->doLoad(false, \func_get_args());
    }

    /**
     * Loads a .env file and the corresponding .env.local, .env.$env and .env.$env.local files if they exist.
     *
     * .env.local is always ignored in test env because tests should produce the same results for everyone.
     * .env.dist is loaded when it exists and .env is not found.
     *
     * @param string      $path                 A file to load
     * @param string|null $envKey               The name of the env vars that defines the app env
     * @param string      $defaultEnv           The app env to use when none is defined
     * @param array       $testEnvs             A list of app envs for which .env.local should be ignored
     * @param bool        $overrideExistingVars Whether existing environment variables set by the system should be overridden
     *
     * @throws FormatException when a file has a syntax error
     * @throws PathException   when a file does not exist or is not readable
     */
    public function loadEnv(string $path, ?string $envKey = null, string $defaultEnv = 'dev', array $testEnvs = ['test'], bool $overrideExistingVars = false): void
    {
        $k = $envKey ?? $this->envKey;

        if (is_file($path) || !is_file($p = "$path.dist")) {
            $this->doLoad($overrideExistingVars, [$path]);
        } else {
            $this->doLoad($overrideExistingVars, [$p]);
        }
