 */

namespace Symfony\Component\String;

use Symfony\Component\String\Exception\ExceptionInterface;
use Symfony\Component\String\Exception\InvalidArgumentException;

/**
 * Represents a string of Unicode grapheme clusters encoded as UTF-8.
 *
 * A letter followed by combining characters (accents typically) form what Unicode defines
 * as a grapheme cluster: a character as humans mean it in written texts. This class knows
 * about the concept and won't split a letter apart from its combining accents. It also
 * ensures all string comparisons happen on their canonically-composed representation,
 * ignoring e.g. the order in which accents are listed when a letter has many of them.
 *
 * @see https://unicode.org/reports/tr15/
 *
 * @author Nicolas Grekas <p@tchwork.com>
 * @author Hugo Hamon <hugohamon@neuf.fr>
 *
 * @throws ExceptionInterface
 */
class UnicodeString extends AbstractUnicodeString
{
    public function __construct(string $string = '')
    {
        if ('' === $string || normalizer_is_normalized($this->string = $string)) {
            return;
        }

        if (false === $string = normalizer_normalize($string)) {
            throw new InvalidArgumentException('Invalid UTF-8 string.');
        }

        $this->string = $string;
    }

    public function append(string ...$suffix): static
    {
        $str = clone $this;
        $str->string = $this->string.(1 >= \count($suffix) ? ($suffix[0] ?? '') : implode('', $suffix));

        if (normalizer_is_normalized($str->string)) {
            return $str;
        }

        if (false === $string = normalizer_normalize($str->string)) {
            throw new InvalidArgumentException('Invalid UTF-8 string.');
        }

        $str->string = $string;

        return $str;
    }

    public function chunk(int $length = 1): array
    {
        if (1 > $length) {
            throw new InvalidArgumentException('The chunk length must be greater than zero.');
        }

        if ('' === $this->string) {
            return [];
        }

        $rx = '/(';
        while (65535 < $length) {
            $rx .= '\X{65535}';
            $length -= 65535;
        }
        $rx .= '\X{'.$length.'})/u';

        $str = clone $this;
        $chunks = [];

        foreach (preg_split($rx, $this->string, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY) as $chunk) {
            $str->string = $chunk;
            $chunks[] = clone $str;
        }

        return $chunks;
    }

    public function endsWith(string|iterable|AbstractString $suffix): bool
    {
        if ($suffix instanceof AbstractString) {
            $suffix = $suffix->string;
        } elseif (!\is_string($suffix)) {
            return parent::endsWith($suffix);
        }

        $form = null === $this->ignoreCase ? \Normalizer::NFD : \Normalizer::NFC;
        normalizer_is_normalized($suffix, $form) ?: $suffix = normalizer_normalize($suffix, $form);

        if ('' === $suffix || false === $suffix) {
            return false;
        }

        if ($this->ignoreCase) {
            return 0 === mb_stripos(grapheme_extract($this->string, \strlen($suffix), \GRAPHEME_EXTR_MAXBYTES, \strlen($this->string) - \strlen($suffix)), $suffix, 0, 'UTF-8');