use ErrorException;
use FilesystemIterator;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;
use RuntimeException;
use SplFileObject;
use Symfony\Component\Filesystem\Filesystem as SymfonyFilesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Mime\MimeTypes;

class Filesystem
{
    use Conditionable, Macroable;

    /**
     * Determine if a file or directory exists.
     *
     * @param  string  $path
     * @return bool
     */
    public function exists($path)
    {
        return file_exists($path);
    }

    /**
     * Determine if a file or directory is missing.
     *
     * @param  string  $path
     * @return bool
     */
    public function missing($path)
    {
        return ! $this->exists($path);
    }

    /**
     * Get the contents of a file.
     *
     * @param  string  $path
     * @param  bool  $lock
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function get($path, $lock = false)
    {
        if ($this->isFile($path)) {
            return $lock ? $this->sharedGet($path) : file_get_contents($path);
        }

        throw new FileNotFoundException("File does not exist at path {$path}.");
    }

    /**
     * Get the contents of a file as decoded JSON.
     *
     * @param  string  $path
     * @param  int  $flags
     * @param  bool  $lock
     * @return array
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function json($path, $flags = 0, $lock = false)
    {
        return json_decode($this->get($path, $lock), true, 512, $flags);
    }

    /**
     * Get contents of a file with shared access.
     *
     * @param  string  $path
     * @return string
     */
    public function sharedGet($path)
    {
        $contents = '';
