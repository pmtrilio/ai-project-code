        return null;
    }

    final public function encode(mixed $data, string $format, array $context = []): string
    {
        return $this->encoder->encode($data, $format, $context);
    }

    final public function decode(string $data, string $format, array $context = []): mixed
    {
        return $this->decoder->decode($data, $format, $context);
    }

    public function supportsEncoding(string $format, array $context = []): bool
    {
        return $this->encoder->supportsEncoding($format, $context);
    }

    public function supportsDecoding(string $format, array $context = []): bool
    {
        return $this->decoder->supportsDecoding($format, $context);
    }
}
