
    public function asDebugString(): string
    {
        $str = parent::asDebugString();
        if (null !== $this->charset) {
            $str .= ' charset: '.$this->charset;
        }
        if (null !== $this->disposition) {
            $str .= ' disposition: '.$this->disposition;
        }

        return $str;
    }

    private function getEncoder(): ContentEncoderInterface
    {
        if ('8bit' === $this->encoding) {
            return self::$encoders[$this->encoding] ??= new EightBitContentEncoder();
        }

        if ('quoted-printable' === $this->encoding) {
            return self::$encoders[$this->encoding] ??= new QpContentEncoder();
        }

        if ('base64' === $this->encoding) {
            return self::$encoders[$this->encoding] ??= new Base64ContentEncoder();
        }

        return self::$encoders[$this->encoding];
    }

    public static function addEncoder(ContentEncoderInterface $encoder): void
    {
        if (\in_array($encoder->getName(), self::DEFAULT_ENCODERS, true)) {
            throw new InvalidArgumentException('You are not allowed to change the default encoders ("quoted-printable", "base64", and "8bit").');
        }

        self::$encoders[$encoder->getName()] = $encoder;
    }

    private function chooseEncoding(): string
    {
        if (null === $this->charset) {
            return 'base64';
        }

        return 'quoted-printable';
    }

    public function __serialize(): array
    {
        // convert resources to strings for serialization
        if (null !== $this->seekable) {
            $this->body = $this->getBody();
            $this->seekable = null;
        }

        return [
            '_headers' => $this->getHeaders(),
            'body' => $this->body,
            'charset' => $this->charset,
            'subtype' => $this->subtype,
            'disposition' => $this->disposition,
            'name' => $this->name,
            'encoding' => $this->encoding,
        ];
    }
