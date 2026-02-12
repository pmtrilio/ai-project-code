                }
            }
        }
    }

    /**
     * Renames a file or a directory.
     *
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public function rename(string $origin, string $target, bool $overwrite = false): void
    {
        // we check that target does not exist
        if (!$overwrite && $this->isReadable($target)) {
            throw new IOException(\sprintf('Cannot rename because the target "%s" already exists.', $target), 0, null, $target);
        }

        if (!self::box('rename', $origin, $target)) {
            if (is_dir($origin)) {
                // See https://bugs.php.net/54097 & https://php.net/rename#113943
                $this->mirror($origin, $target, null, ['override' => $overwrite, 'delete' => $overwrite]);
                $this->remove($origin);

                return;
            }
            throw new IOException(\sprintf('Cannot rename "%s" to "%s": ', $origin, $target).self::$lastError, 0, null, $target);
        }
    }

    /**
     * Tells whether a file exists and is readable.
     *
     * @throws IOException When windows path is longer than 258 characters
     */
    private function isReadable(string $filename): bool
    {
        $maxPathLength = \PHP_MAXPATHLEN - 2;

        if (\strlen($filename) > $maxPathLength) {
            throw new IOException(\sprintf('Could not check if file is readable because path length exceeds %d characters.', $maxPathLength), 0, null, $filename);
        }

        return is_readable($filename);
    }

    /**
     * Creates a symbolic link or copy a directory.
     *
     * @throws IOException When symlink fails
     */
    public function symlink(string $originDir, string $targetDir, bool $copyOnWindows = false): void
    {
        self::assertFunctionExists('symlink');

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $originDir = strtr($originDir, '/', '\\');