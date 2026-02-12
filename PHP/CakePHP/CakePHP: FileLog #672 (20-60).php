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