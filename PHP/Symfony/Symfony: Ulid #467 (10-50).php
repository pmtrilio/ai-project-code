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