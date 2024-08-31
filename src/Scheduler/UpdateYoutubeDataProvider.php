<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Scheduler;

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use VideoGamesRecords\CoreBundle\Scheduler\Message\UpdateYoutubeData;

#[AsSchedule]
class UpdateYoutubeDataProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        $store = new SemaphoreStore();
        $factory = new LockFactory($store);

        return $this->schedule ??= (new Schedule())
            ->with(
                RecurringMessage::cron(
                    '00 8 * * 1',
                    new UpdateYoutubeData()
                )
            )
            ->lock($factory->createLock('update-youtube-data'));
    }
}