                    throw new ParseException('Complex mappings are not supported.', $this->getRealCurrentLineNb() + 1, $this->currentLine);
                }

                // 1-liner optionally followed by newline(s)
                if (\is_string($value) && $this->lines[0] === trim($value)) {
                    try {
                        $value = Inline::parse($this->lines[0], $flags, $this->refs);
                    } catch (ParseException $e) {
                        $e->setParsedLine($this->getRealCurrentLineNb() + 1);
                        $e->setSnippet($this->currentLine);

                        throw $e;
                    }

                    return $value;
                }

                // try to parse the value as a multi-line string as a last resort
                if (0 === $this->currentLineNb) {
                    $previousLineWasNewline = false;
                    $previousLineWasTerminatedWithBackslash = false;
                    $value = '';

                    foreach ($this->lines as $line) {
                        $trimmedLine = trim($line);
                        if ('#' === ($trimmedLine[0] ?? '')) {
                            continue;
                        }
                        // If the indentation is not consistent at offset 0, it is to be considered as a ParseError
                        if (0 === $this->offset && isset($line[0]) && ' ' === $line[0]) {
                            throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        if (str_contains($line, ': ')) {
                            throw new ParseException('Mapping values are not allowed in multi-line blocks.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        if ('' === $trimmedLine) {
                            $value .= "\n";
                        } elseif (!$previousLineWasNewline && !$previousLineWasTerminatedWithBackslash) {
                            $value .= ' ';
                        }

                        if ('' !== $trimmedLine && str_ends_with($line, '\\')) {
                            $value .= ltrim(substr($line, 0, -1));
                        } elseif ('' !== $trimmedLine) {
                            $value .= $trimmedLine;
                        }

                        if ('' === $trimmedLine) {
                            $previousLineWasNewline = true;
                            $previousLineWasTerminatedWithBackslash = false;
                        } elseif (str_ends_with($line, '\\')) {
                            $previousLineWasNewline = false;
                            $previousLineWasTerminatedWithBackslash = true;
                        } else {
                            $previousLineWasNewline = false;
                            $previousLineWasTerminatedWithBackslash = false;
                        }
                    }

                    try {
                        return Inline::parse(trim($value));
                    } catch (ParseException) {
                        // fall-through to the ParseException thrown below
                    }
                }