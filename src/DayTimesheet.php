<?php

declare(strict_types=1);

namespace App;

class DayTimesheet
{

    private const DT_FORMAT = 'Y-m-d H:i:s';

    private \DateTimeImmutable $startDateTime;
    private \DateTimeImmutable $endDateTime;
    /**
     * @var \DateInterval[]
     */
    private array $durationTimes;
    private \DateTimeImmutable $duration;

    public function __construct(string $startDate, string $startTime)
    {
        $this->startDateTime = \DateTimeImmutable::createFromFormat(
            self::DT_FORMAT,
            sprintf('%s %s', $startDate, $startTime)
        );

        $this->endDateTime = clone $this->startDateTime;
        $this->durationTimes = [];
        $this->duration = (clone $this->startDateTime);
    }

    public function addEndWithDuration(string $endTime, string $timeDuration): void
    {
        $this->endDateTime = \DateTimeImmutable::createFromFormat(
            self::DT_FORMAT,
            sprintf('%s %s', $this->startDateTime->format('Y-m-d'), $endTime)
        );

        $exploded = explode(':', $timeDuration);
        $newDuration = new \DateInterval(
            sprintf(
                'PT%dH%dM%dS',
                $exploded[0],
                $exploded[1],
                $exploded[2]
            )
        );
        $this->durationTimes[] = $newDuration;
        $this->duration = $this->duration->add($newDuration);
    }

    public function getSpentWorkingTimeToday(): \DateInterval
    {
        return $this->duration->diff($this->startDateTime);
    }

    public function getTotalBreaksFromStartToEnd(): \DateInterval
    {
        return $this->endDateTime->diff($this->duration);
    }

    public function getStartTime(): \DateTimeImmutable
    {
        return $this->startDateTime;
    }

    public function getEndTime(): \DateTimeImmutable
    {
        return $this->endDateTime;
    }
}