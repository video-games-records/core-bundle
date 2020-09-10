<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Team;

class TeamListener
{
    /**
     * @param Team       $team
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function postPersist(Team $team, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        $player = $team->getLeader();
        $player->setTeam($team);
        $em->flush();
    }
}
