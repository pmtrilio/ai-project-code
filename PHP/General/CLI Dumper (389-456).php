                                if (isset($key[0][1])) {
                                    parse_str(substr($key[0], 1), $attr);
                                    $attr += ['binary' => $cursor->hashKeyIsBinary];
                                }
                                break;
                            case '*':
                                $style = 'protected';
                                $bin = '#'.$bin;
                                break;
                            default:
                                $attr['class'] = $key[0];
                                $style = 'private';
                                $bin = '-'.$bin;
                                break;
                        }

                        if (isset($attr['collapse'])) {
                            if ($attr['collapse']) {
                                $this->collapseNextHash = true;
                            } else {
                                $this->expandNextHash = true;
                            }
                        }

                        $this->line .= $bin.$this->style($style, $key[1], $attr).($attr['separator'] ?? ': ');
                    } else {
                        // This case should not happen
                        $this->line .= '-'.$bin.'"'.$this->style('private', $key, ['class' => '']).'": ';
                    }
                    break;
            }

            if ($cursor->hardRefTo) {
                $this->line .= $this->style('ref', '&'.($cursor->hardRefCount ? $cursor->hardRefTo : ''), ['count' => $cursor->hardRefCount]).' ';
            }
        }
    }

    /**
     * Decorates a value with some style.
     *
     * @param string $style The type of style being applied
     * @param string $value The value being styled
     * @param array  $attr  Optional context information
     */
    protected function style(string $style, string $value, array $attr = []): string
    {
        $this->colors ??= $this->supportsColors();

        $this->handlesHrefGracefully ??= 'JetBrains-JediTerm' !== getenv('TERMINAL_EMULATOR')
            && (!getenv('KONSOLE_VERSION') || (int) getenv('KONSOLE_VERSION') > 201100)
            && !isset($_SERVER['IDEA_INITIAL_DIRECTORY']);

        if (isset($attr['ellipsis'], $attr['ellipsis-type'])) {
            $prefix = substr($value, 0, -$attr['ellipsis']);
            if ('cli' === \PHP_SAPI && 'path' === $attr['ellipsis-type'] && isset($_SERVER[$pwd = '\\' === \DIRECTORY_SEPARATOR ? 'CD' : 'PWD']) && str_starts_with($prefix, $_SERVER[$pwd])) {
                $prefix = '.'.substr($prefix, \strlen($_SERVER[$pwd]));
            }
            if (!empty($attr['ellipsis-tail'])) {
                $prefix .= substr($value, -$attr['ellipsis'], $attr['ellipsis-tail']);
                $value = substr($value, -$attr['ellipsis'] + $attr['ellipsis-tail']);
            } else {
                $value = substr($value, -$attr['ellipsis']);
            }

            $value = $this->style('default', $prefix).$this->style($style, $value);

            goto href;