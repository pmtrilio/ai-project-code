 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Exception\RuntimeException;

/**
 * ArgvInput represents an input coming from the CLI arguments.
 *
 * Usage:
 *
 *     $input = new ArgvInput();
 *
 * By default, the `$_SERVER['argv']` array is used for the input values.
 *
 * This can be overridden by explicitly passing the input values in the constructor:
 *
 *     $input = new ArgvInput($_SERVER['argv']);
 *
 * If you pass it yourself, don't forget that the first element of the array
 * is the name of the running application.
 *
 * When passing an argument to the constructor, be sure that it respects
 * the same rules as the argv one. It's almost always better to use the
 * `StringInput` when you want to provide your own input.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @see http://www.gnu.org/software/libc/manual/html_node/Argument-Syntax.html
 * @see http://www.opengroup.org/onlinepubs/009695399/basedefs/xbd_chap12.html#tag_12_02
 */
class ArgvInput extends Input
{
    private array $tokens;
    private array $parsed;

    public function __construct(?array $argv = null, ?InputDefinition $definition = null)
    {
        $argv ??= $_SERVER['argv'] ?? [];

        // strip the application name
        array_shift($argv);

        $this->tokens = $argv;

        parent::__construct($definition);
    }

    /**
     * @return void
     */
    protected function setTokens(array $tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @return void
     */
    protected function parse()
    {
        $parseOptions = true;
        $this->parsed = $this->tokens;
        while (null !== $token = array_shift($this->parsed)) {
            $parseOptions = $this->parseToken($token, $parseOptions);
        }
    }

    protected function parseToken(string $token, bool $parseOptions): bool
    {
        if ($parseOptions && '' == $token) {
            $this->parseArgument($token);
        } elseif ($parseOptions && '--' == $token) {
            return false;
        } elseif ($parseOptions && str_starts_with($token, '--')) {
            $this->parseLongOption($token);
        } elseif ($parseOptions && '-' === $token[0] && '-' !== $token) {
            $this->parseShortOption($token);
        } else {
            $this->parseArgument($token);
        }

        return $parseOptions;
    }

    /**
     * Parses a short option.
     */
    private function parseShortOption(string $token): void
    {
        $name = substr($token, 1);

        if (\strlen($name) > 1) {
            if ($this->definition->hasShortcut($name[0]) && $this->definition->getOptionForShortcut($name[0])->acceptValue()) {
                // an option with a value (with no space)
                $this->addShortOption($name[0], substr($name, 1));