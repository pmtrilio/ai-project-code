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