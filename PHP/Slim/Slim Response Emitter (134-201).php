        return $seekable ? $stream->read(1) === '' : $stream->eof();
    }
}
