use Cake\Utility\Text;
use Stringable;

/**
 * File Storage stream for Logging. Writes logs to different files
 * based on the level of log it is.
 */
class FileLog extends BaseLog
{
    /**
     * Default config for this class
     *
     * - `levels` string or array, levels the engine is interested in
     * - `scopes` string or array, scopes the engine is interested in
     * - `file` Log file name
     * - `path` The path to save logs on.
     * - `size` Used to implement basic log file rotation. If log file size
     *   reaches specified size the existing file is renamed by appending timestamp
     *   to filename and new log file is created. Can be integer bytes value or
     *   human-readable string values like '10MB', '100KB' etc.
     * - `rotate` Log files are rotated specified times before being removed.
     *   If value is 0, old versions are removed rather than rotated.
     * - `mask` A mask is applied when log files are created. Left empty no chmod
     *   is made.
     * - `dirMask` The mask used for created folders.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'path' => null,
        'file' => null,
        'types' => null,
        'levels' => [],
        'scopes' => [],
        'rotate' => 10,
        'size' => 10485760, // 10MB
        'mask' => null,
        'dirMask' => 0777,
        'formatter' => [
            'className' => DefaultFormatter::class,
        ],
    ];

    /**
     * Path to save log files on.
     *
     * @var string
     */
    protected string $_path;

    /**
     * The name of the file to save logs into.
     *
     * @var string|null
     */
    protected ?string $_file = null;

    /**
     * Max file size, used for log file rotation.
     *
     * @var int|null
     */
    protected ?int $_size = null;

    /**
     * Sets protected properties based on config provided
     *
     * @param array<string, mixed> $config Configuration array
     */
    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->_path = $this->getConfig('path', sys_get_temp_dir() . DIRECTORY_SEPARATOR);
        if (!is_dir($this->_path)) {
            mkdir($this->_path, $this->_config['dirMask'] ^ umask(), true);
        }

        if (!empty($this->_config['file'])) {
            $this->_file = $this->_config['file'];
            if (!str_ends_with($this->_file, '.log')) {