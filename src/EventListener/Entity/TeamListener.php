<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Team;

class TeamListener
{
    /**
     * @param Team       $team
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Team $team, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        $player = $team->getLeader();
        $player->setTeam($team);
        $em->flush();
    }
}
