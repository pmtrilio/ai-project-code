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