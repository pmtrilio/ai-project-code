 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid;

/**
 * A ULID is lexicographically sortable and contains a 48-bit timestamp and 80-bit of crypto-random entropy.
 *
 * @see https://github.com/ulid/spec
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class Ulid extends AbstractUid implements TimeBasedUidInterface
{
    protected const NIL = '00000000000000000000000000';
    protected const MAX = '7ZZZZZZZZZZZZZZZZZZZZZZZZZ';

    private static string $time = '';
    private static array $rand = [];

    public function __construct(?string $ulid = null)
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