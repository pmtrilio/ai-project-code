    }

    private static function traceUnshift(array &$trace, ?string $class, string $file, int $line): void
    {
        if (isset($trace[0]['file'], $trace[0]['line']) && $trace[0]['file'] === $file && $trace[0]['line'] === $line) {
            return;
        }
        array_unshift($trace, [
            'function' => $class ? 'new '.$class : null,
            'file' => $file,
            'line' => $line,
        ]);
    }

    private static function extractSource(string $srcLines, int $line, int $srcContext, string $lang, ?string $file, array $frame): EnumStub
    {
        $srcLines = explode("\n", $srcLines);
        $src = [];

        for ($i = $line - 1 - $srcContext; $i <= $line - 1 + $srcContext; ++$i) {
            $src[] = ($srcLines[$i] ?? '')."\n";
        }

        if ($frame['function'] ?? false) {
            $stub = new CutStub(new \stdClass());
            $stub->class = (isset($frame['class']) ? $frame['class'].$frame['type'] : '').$frame['function'];
            $stub->type = Stub::TYPE_OBJECT;
            $stub->attr['cut_hash'] = true;
            $stub->attr['file'] = $frame['file'];
            $stub->attr['line'] = $frame['line'];

            try {
                $caller = isset($frame['class']) ? new \ReflectionMethod($frame['class'], $frame['function']) : new \ReflectionFunction($frame['function']);
                $stub->class .= ReflectionCaster::getSignature(ReflectionCaster::castFunctionAbstract($caller, [], $stub, true, Caster::EXCLUDE_VERBOSE));

                if ($f = $caller->getFileName()) {
                    $stub->attr['file'] = $f;
                    $stub->attr['line'] = $caller->getStartLine();
                }
            } catch (\ReflectionException) {
                // ignore fake class/function
            }

            $srcLines = ["\0~separator=\0" => $stub];
        } else {
            $stub = null;
            $srcLines = [];
        }

        $ltrim = 0;
        do {
            $pad = null;
            for ($i = $srcContext << 1; $i >= 0; --$i) {
                if (isset($src[$i][$ltrim]) && "\r" !== ($c = $src[$i][$ltrim]) && "\n" !== $c) {
                    $pad ??= $c;
                    if ((' ' !== $c && "\t" !== $c) || $pad !== $c) {
                        break;
                    }
                }
            }
            ++$ltrim;
        } while (0 > $i && null !== $pad);

        --$ltrim;

        foreach ($src as $i => $c) {
            if ($ltrim) {