                return $this;
            }

            $out = fopen('php://output', 'w');

            if ($this->tempFileObject) {
                $file = $this->tempFileObject;
                $file->rewind();
            } else {
                $file = new \SplFileObject($this->file->getPathname(), 'r');
            }

            ignore_user_abort(true);

            if (0 !== $this->offset) {
                $file->fseek($this->offset);
            }

            $length = $this->maxlen;
            while ($length && !$file->eof()) {
                $read = $length > $this->chunkSize || 0 > $length ? $this->chunkSize : $length;

                if (false === $data = $file->fread($read)) {
                    break;
                }
                while ('' !== $data) {
                    $read = fwrite($out, $data);
                    if (false === $read || connection_aborted()) {
                        break 2;
                    }
                    if (0 < $length) {
                        $length -= $read;
                    }
                    $data = substr($data, $read);
                }
            }

            fclose($out);
        } finally {
            if (null === $this->tempFileObject && $this->deleteFileAfterSend && is_file($this->file->getPathname())) {
                unlink($this->file->getPathname());
            }
        }

        return $this;
    }

    /**
     * @throws \LogicException when the content is not null
     */
    public function setContent(?string $content): static
    {
        if (null !== $content) {
            throw new \LogicException('The content cannot be set on a BinaryFileResponse instance.');
        }

        return $this;
    }

    public function getContent(): string|false
    {
        return false;
    }

    /**
     * Trust X-Sendfile-Type header.
     */