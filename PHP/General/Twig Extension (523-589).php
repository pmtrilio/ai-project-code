        }

        if ($date instanceof \DateInterval) {
            return $date->format($format);
        }

        return $this->convertDate($date, $timezone)->format($format);
    }

    /**
     * Returns a new date object modified.
     *
     *   {{ post.published_at|date_modify("-1day")|date("m/d/Y") }}
     *
     * @param \DateTimeInterface|string|int|null $date     A date, a timestamp or null to use the current time
     * @param string                             $modifier A modifier string
     *
     * @return \DateTime|\DateTimeImmutable
     *
     * @internal
     */
    public function modifyDate($date, $modifier)
    {
        return $this->convertDate($date, false)->modify($modifier);
    }

    /**
     * Returns a formatted string.
     *
     * @param string|null $format
     *
     * @internal
     */
    public static function sprintf($format, ...$values): string
    {
        return \sprintf($format ?? '', ...$values);
    }

    /**
     * @internal
     */
    public static function dateConverter(Environment $env, $date, $format = null, $timezone = null): string
    {
        return $env->getExtension(self::class)->formatDate($date, $format, $timezone);
    }

    /**
     * Converts an input to a \DateTime instance.
     *
     *    {% if date(user.created_at) < date('+2days') %}
     *      {# do something #}
     *    {% endif %}
     *
     * @param \DateTimeInterface|string|int|null $date     A date, a timestamp or null to use the current time
     * @param \DateTimeZone|string|false|null    $timezone The target timezone, null to use the default, false to leave unchanged
     *
     * @return \DateTime|\DateTimeImmutable
     */
    public function convertDate($date = null, $timezone = null)
    {
        // determine the timezone
        if (false !== $timezone) {
            if (null === $timezone) {
                $timezone = $this->getTimezone();
            } elseif (!$timezone instanceof \DateTimeZone) {
                $timezone = new \DateTimeZone($timezone);
            }