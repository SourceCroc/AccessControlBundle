<?php declare(strict_types=1);

namespace SourceCroc\AccessControlBundle\Helper;

abstract class TimeHelper
{
    /**
     * @throws \Exception
     */
    public static function writtenIntervalToSeconds(string $interval): int
    {
        $ref = \DateTimeImmutable::createFromFormat(DATE_ATOM, '2000-01-01T00:00:00-0000');
        return strtotime('+'.$interval, $ref->getTimestamp()) - $ref->getTimestamp();
    }
}
