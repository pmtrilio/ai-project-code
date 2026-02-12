        if (!str_contains($entered, ',')) {
            return $entered;
        }

        if (false === $lastCommaPos = strrpos($entered, ',')) {
            return $entered;
        }

        $lastChoice = trim(substr($entered, $lastCommaPos + 1));

        return '' !== $lastChoice ? $lastChoice : $entered;
    }

    /**
     * Gets a hidden response from user.
     *
     * @param resource $inputStream The handler resource
     * @param bool     $trimmable   Is the answer trimmable
     *
     * @throws RuntimeException In case the fallback is deactivated and the response cannot be hidden
     */
    private function getHiddenResponse(OutputInterface $output, $inputStream, bool $trimmable = true): string
    {
        if ('\\' === \DIRECTORY_SEPARATOR) {
            $exe = __DIR__.'/../Resources/bin/hiddeninput.exe';

            // handle code running from a phar
            if (str_starts_with(__FILE__, 'phar:')) {
                $tmpExe = sys_get_temp_dir().'/hiddeninput.exe';
                copy($exe, $tmpExe);
                $exe = $tmpExe;
            }

            $sExec = shell_exec('"'.$exe.'"');
            $value = $trimmable ? rtrim($sExec) : $sExec;
            $output->writeln('');

            if (isset($tmpExe)) {
                unlink($tmpExe);
            }

            return $value;
        }

        $inputHelper = null;

        if (self::$stty && Terminal::hasSttyAvailable()) {
            $inputHelper = new TerminalInputHelper($inputStream);
            shell_exec('stty -echo');
        } elseif ($this->isInteractiveInput($inputStream)) {
            throw new RuntimeException('Unable to hide the response.');
        }

        $value = $this->doReadInput($inputStream, helper: $inputHelper);

        if (4095 === \strlen($value)) {
            $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
            $errOutput->warning('The value was possibly truncated by your shell or terminal emulator');
        }

        // Restore the terminal so it behaves normally again
        $inputHelper?->finish();

        if ($trimmable) {
            $value = trim($value);
        }
        $output->writeln('');
