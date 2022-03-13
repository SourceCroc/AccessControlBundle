<?php

namespace SourceCroc\Helpers;

if (!function_exists(__NAMESPACE__.'\\sourcecroc_interval_to_seconds')) {
    /**
     * @throws \Exception
     */
    function sourcecroc_interval_to_seconds(string $v): int
    {
        $ref = \DateTimeImmutable::createFromFormat(DATE_ISO8601, '2000-01-01T00:00:00-0000');
        return strtotime('+'.$v, $ref->getTimestamp()) - $ref->getTimestamp();
    }
}
