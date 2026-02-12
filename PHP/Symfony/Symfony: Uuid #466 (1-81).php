<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid;

/**
 * @author Grégoire Pineau <lyrixx@lyrixx.info>
 *
 * @see https://datatracker.ietf.org/doc/html/rfc9562/#section-6.6 for details about namespaces
 */
class Uuid extends AbstractUid
{
    public const NAMESPACE_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    public const NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    public const NAMESPACE_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    public const NAMESPACE_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';

    protected const TYPE = 0;
    protected const NIL = '00000000-0000-0000-0000-000000000000';
    protected const MAX = 'ffffffff-ffff-ffff-ffff-ffffffffffff';

    public function __construct(string $uuid, bool $checkVariant = false)
    {
        $type = preg_match('{^[0-9a-f]{8}(?:-[0-9a-f]{4}){3}-[0-9a-f]{12}$}Di', $uuid) ? (int) $uuid[14] : false;

        if (false === $type || (static::TYPE ?: $type) !== $type) {
            throw new \InvalidArgumentException(\sprintf('Invalid UUID%s.', static::TYPE ? 'v'.static::TYPE : ''));
        }

        $this->uid = strtolower($uuid);

        if ($checkVariant && !\in_array($this->uid[19], ['8', '9', 'a', 'b'], true)) {
            throw new \InvalidArgumentException(\sprintf('Invalid UUID%s.', static::TYPE ? 'v'.static::TYPE : ''));
        }
    }

    public static function fromString(string $uuid): static
    {
        if (22 === \strlen($uuid) && 22 === strspn($uuid, BinaryUtil::BASE58[''])) {
            $uuid = str_pad(BinaryUtil::fromBase($uuid, BinaryUtil::BASE58), 16, "\0", \STR_PAD_LEFT);
        }

        if (16 === \strlen($uuid)) {
            // don't use uuid_unparse(), it's slower
            $uuid = bin2hex($uuid);
            $uuid = substr_replace($uuid, '-', 8, 0);
            $uuid = substr_replace($uuid, '-', 13, 0);
            $uuid = substr_replace($uuid, '-', 18, 0);
            $uuid = substr_replace($uuid, '-', 23, 0);
        } elseif (26 === \strlen($uuid) && Ulid::isValid($uuid)) {
            $ulid = new NilUlid();
            $ulid->uid = strtoupper($uuid);
            $uuid = $ulid->toRfc4122();
        }

        if (__CLASS__ !== static::class || 36 !== \strlen($uuid)) {
            return new static($uuid);
        }

        if (self::NIL === $uuid) {
            return new NilUuid();
        }

        if (self::MAX === $uuid = strtr($uuid, 'F', 'f')) {
            return new MaxUuid();
        }

        if (!\in_array($uuid[19], ['8', '9', 'a', 'b', 'A', 'B'], true)) {
            return new self($uuid);
        }

        return match ((int) $uuid[14]) {
            UuidV1::TYPE => new UuidV1($uuid),