<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Accessor;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

trait SetEventDispacther
{
    private readonly EventDispatcherInterface $eventDispatcher;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

}
