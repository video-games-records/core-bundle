<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;

class TeamRequestListener
{
    /**
     * @param TeamRequest        $teamRequest
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function postUpdate(TeamRequest $teamRequest, LifecycleEventArgs $event)
    {
        $em = $event->getEntityManager();
        if ($teamRequest->getStatus() == TeamRequest::STATUS_ACCEPTED) {
            $player = $teamRequest->getPlayer();
            $player->setTeam($teamRequest->getTeam());
            $em->flush();
        }
    }
}
