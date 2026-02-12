    {
        if (null === $ulid) {
            $this->uid = static::generate();
        } elseif (self::NIL === $ulid) {
            $this->uid = $ulid;
        } elseif (self::MAX === strtr($ulid, 'z', 'Z')) {
            $this->uid = $ulid;
        } else {
            if (!self::isValid($ulid)) {
                throw new \InvalidArgumentException('Invalid ULID.');
            }

            $this->uid = strtoupper($ulid);
        }
    }

    public static function isValid(string $ulid): bool
    {
        if (26 !== \strlen($ulid)) {
            return false;
        }

        if (26 !== strspn($ulid, '0123456789ABCDEFGHJKMNPQRSTVWXYZabcdefghjkmnpqrstvwxyz')) {
            return false;
        }

        return $ulid[0] <= '7';
    }

    public static function fromString(string $ulid): static
    {
        if (36 === \strlen($ulid) && preg_match('{^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$}Di', $ulid)) {
            $ulid = uuid_parse($ulid);
        } elseif (22 === \strlen($ulid) && 22 === strspn($ulid, BinaryUtil::BASE58[''])) {
            $ulid = str_pad(BinaryUtil::fromBase($ulid, BinaryUtil::BASE58), 16, "\0", \STR_PAD_LEFT);
        }

        if (16 !== \strlen($ulid)) {
            return match (strtr($ulid, 'z', 'Z')) {
                self::NIL => new NilUlid(),
                self::MAX => new MaxUlid(),
                default => new static($ulid),
            };
        }

        $ulid = bin2hex($ulid);
        $ulid = \sprintf('%02s%04s%04s%04s%04s%04s%04s',
            base_convert(substr($ulid, 0, 2), 16, 32),
            base_convert(substr($ulid, 2, 5), 16, 32),
            base_convert(substr($ulid, 7, 5), 16, 32),
            base_convert(substr($ulid, 12, 5), 16, 32),
            base_convert(substr($ulid, 17, 5), 16, 32),
            base_convert(substr($ulid, 22, 5), 16, 32),
            base_convert(substr($ulid, 27, 5), 16, 32)
        );

        if (self::NIL === $ulid) {
            return new NilUlid();
        }

        if (self::MAX === $ulid = strtr($ulid, 'abcdefghijklmnopqrstuv', 'ABCDEFGHJKMNPQRSTVWXYZ')) {
            return new MaxUlid();
        }

        $u = new static(self::NIL);
        $u->uid = $ulid;

        return $u;
    }

    public function toBinary(): string
    {
        $ulid = strtr($this->uid, 'ABCDEFGHJKMNPQRSTVWXYZ', 'abcdefghijklmnopqrstuv');

        $ulid = \sprintf('%02s%05s%05s%05s%05s%05s%05s',
            base_convert(substr($ulid, 0, 2), 32, 16),
            base_convert(substr($ulid, 2, 4), 32, 16),
            base_convert(substr($ulid, 6, 4), 32, 16),
            base_convert(substr($ulid, 10, 4), 32, 16),
            base_convert(substr($ulid, 14, 4), 32, 16),
            base_convert(substr($ulid, 18, 4), 32, 16),
            base_convert(substr($ulid, 22, 4), 32, 16)
        );

        return hex2bin($ulid);
    }

    /**
     * Returns the identifier as a base32 case insensitive string.
     *
     * @see https://tools.ietf.org/html/rfc4648#section-6
     *
     * @example 09EJ0S614A9FXVG9C5537Q9ZE1 (len=26)
     */
    public function toBase32(): string
    {
        return $this->uid;
    }

    public function getDateTime(): \DateTimeImmutable
    {
        $time = strtr(substr($this->uid, 0, 10), 'ABCDEFGHJKMNPQRSTVWXYZ', 'abcdefghijklmnopqrstuv');

        if (\PHP_INT_SIZE >= 8) {
            $time = (string) hexdec(base_convert($time, 32, 16));
        } else {
            $time = \sprintf('%02s%05s%05s',
                base_convert(substr($time, 0, 2), 32, 16),
                base_convert(substr($time, 2, 4), 32, 16),
                base_convert(substr($time, 6, 4), 32, 16)
            );
            $time = BinaryUtil::toBase(hex2bin($time), BinaryUtil::BASE10);
        }

        if (4 > \strlen($time)) {
            $time = '000'.$time;
        }

        return \DateTimeImmutable::createFromFormat('U.u', substr_replace($time, '.', -3, 0));
    }
