    public static function getSignature(array $a): string
    {
        $prefix = Caster::PREFIX_VIRTUAL;
        $signature = '';

        if (isset($a[$prefix.'parameters'])) {
            foreach ($a[$prefix.'parameters']->value as $k => $param) {
                $signature .= ', ';
                if ($type = $param->getType()) {
                    if (!$type instanceof \ReflectionNamedType) {
                        $signature .= $type.' ';
                    } else {
                        if ($param->allowsNull() && !\in_array($type->getName(), ['mixed', 'null'], true)) {
                            $signature .= '?';
                        }
                        $signature .= substr(strrchr('\\'.$type->getName(), '\\'), 1).' ';
                    }
                }
                $signature .= $k;

                if (!$param->isDefaultValueAvailable()) {
                    continue;
                }
                $v = $param->getDefaultValue();
                $signature .= ' = ';

                if ($param->isDefaultValueConstant()) {
                    $signature .= substr(strrchr('\\'.$param->getDefaultValueConstantName(), '\\'), 1);
                } elseif (null === $v) {
                    $signature .= 'null';
                } elseif (\is_array($v)) {
                    $signature .= $v ? '[…'.\count($v).']' : '[]';
                } elseif (\is_string($v)) {
                    $signature .= 10 > \strlen($v) && !str_contains($v, '\\') ? "'{$v}'" : "'…".\strlen($v)."'";
                } elseif (\is_bool($v)) {
                    $signature .= $v ? 'true' : 'false';
                } elseif (\is_object($v)) {
                    $signature .= 'new '.substr(strrchr('\\'.get_debug_type($v), '\\'), 1);
                } else {
                    $signature .= $v;
                }
            }
        }
        $signature = (empty($a[$prefix.'returnsReference']) ? '' : '&').'('.substr($signature, 2).')';

        if (isset($a[$prefix.'returnType'])) {
            $signature .= ': '.substr(strrchr('\\'.$a[$prefix.'returnType'], '\\'), 1);
        }

        return $signature;
    }

    private static function addExtra(array &$a, \Reflector $c): void
    {
        $x = isset($a[Caster::PREFIX_VIRTUAL.'extra']) ? $a[Caster::PREFIX_VIRTUAL.'extra']->value : [];

        if (method_exists($c, 'getFileName') && $m = $c->getFileName()) {
            $x['file'] = new LinkStub($m, $c->getStartLine());
            $x['line'] = $c->getStartLine().' to '.$c->getEndLine();
        }

        self::addMap($x, $c, self::EXTRA_MAP, '');

        if ($x) {
            $a[Caster::PREFIX_VIRTUAL.'extra'] = new EnumStub($x);
        }
    }
