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
