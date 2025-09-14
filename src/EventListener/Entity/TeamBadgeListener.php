<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use VideoGamesRecords\CoreBundle\Entity\TeamBadge;
use VideoGamesRecords\CoreBundle\Event\TeamBadgeLost;
use VideoGamesRecords\CoreBundle\Event\TeamBadgeObtained;

class TeamBadgeListener
{
    private array $changeSet = array();

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param TeamBadge $teamBadge
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(TeamBadge $teamBadge, PreUpdateEventArgs $event): void
    {
        $this->changeSet = $event->getEntityChangeSet();
    }

    /**
     * @param TeamBadge $teamBadge
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(TeamBadge $teamBadge, LifecycleEventArgs $event): void
    {
        if ($teamBadge->getBadge()->isTypeMaster() && array_key_exists('endedAt', $this->changeSet)) {
            $this->eventDispatcher->dispatch(new TeamBadgeLost($teamBadge));
        }
    }

    /**
     * @param TeamBadge $teamBadge
     * @param LifecycleEventArgs $event
     */
    public function postPersist(TeamBadge $teamBadge, LifecycleEventArgs $event): void
    {
        $this->eventDispatcher->dispatch(new TeamBadgeObtained($teamBadge));
    }
}
