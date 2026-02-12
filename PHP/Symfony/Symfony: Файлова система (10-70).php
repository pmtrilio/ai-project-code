 */

namespace Symfony\Component\Filesystem;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Provides basic utility to manipulate the file system.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Filesystem
{
    private static ?string $lastError = null;

    /**
     * Copies a file.
     *
     * If the target file is older than the origin file, it's always overwritten.
     * If the target file is newer, it is overwritten only when the
     * $overwriteNewerFiles option is set to true.
     *
     * @return void
     *
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws IOException           When copy fails
     */
    public function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false)
    {
        $originIsLocal = stream_is_local($originFile) || 0 === stripos($originFile, 'file://');
        if ($originIsLocal && !is_file($originFile)) {
            throw new FileNotFoundException(\sprintf('Failed to copy "%s" because file does not exist.', $originFile), 0, null, $originFile);
        }

        $this->mkdir(\dirname($targetFile));

        $doCopy = true;
        if (!$overwriteNewerFiles && !parse_url($originFile, \PHP_URL_HOST) && is_file($targetFile)) {
            $doCopy = filemtime($originFile) > filemtime($targetFile);
        }

        if ($doCopy) {
            // https://bugs.php.net/64634
            if (!$source = self::box('fopen', $originFile, 'r')) {
                throw new IOException(\sprintf('Failed to copy "%s" to "%s" because source file could not be opened for reading: ', $originFile, $targetFile).self::$lastError, 0, null, $originFile);
            }

            // Stream context created to allow files overwrite when using FTP stream wrapper - disabled by default
            if (!$target = self::box('fopen', $targetFile, 'w', false, stream_context_create(['ftp' => ['overwrite' => true]]))) {
                throw new IOException(\sprintf('Failed to copy "%s" to "%s" because target file could not be opened for writing: ', $originFile, $targetFile).self::$lastError, 0, null, $originFile);
            }

            $bytesCopied = stream_copy_to_stream($source, $target);
            fclose($source);
            fclose($target);
            unset($source, $target);

            if (!is_file($targetFile)) {
                throw new IOException(\sprintf('Failed to copy "%s" to "%s".', $originFile, $targetFile), 0, null, $originFile);