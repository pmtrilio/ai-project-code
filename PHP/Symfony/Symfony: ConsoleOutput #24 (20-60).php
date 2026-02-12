 *
 *     $output = new ConsoleOutput();
 *
 * This is equivalent to:
 *
 *     $output = new StreamOutput(fopen('php://stdout', 'w'));
 *     $stdErr = new StreamOutput(fopen('php://stderr', 'w'));
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ConsoleOutput extends StreamOutput implements ConsoleOutputInterface
{
    private OutputInterface $stderr;
    private array $consoleSectionOutputs = [];

    /**
     * @param int                           $verbosity The verbosity level (one of the VERBOSITY constants in OutputInterface)
     * @param bool|null                     $decorated Whether to decorate messages (null for auto-guessing)
     * @param OutputFormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct(int $verbosity = self::VERBOSITY_NORMAL, ?bool $decorated = null, ?OutputFormatterInterface $formatter = null)
    {
        parent::__construct($this->openOutputStream(), $verbosity, $decorated, $formatter);

        if (null === $formatter) {
            // for BC reasons, stdErr has it own Formatter only when user don't inject a specific formatter.
            $this->stderr = new StreamOutput($this->openErrorStream(), $verbosity, $decorated);

            return;
        }

        $actualDecorated = $this->isDecorated();
        $this->stderr = new StreamOutput($this->openErrorStream(), $verbosity, $decorated, $this->getFormatter());

        if (null === $decorated) {
            $this->setDecorated($actualDecorated && $this->stderr->isDecorated());
        }
    }

    /**
     * Creates a new output section.