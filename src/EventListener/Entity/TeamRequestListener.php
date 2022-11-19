<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;

class TeamRequestListener
{
    /**
     * @param TeamRequest        $teamRequest
     * @param LifecycleEventArgs $event
     * @throws ORMException
     * @throws OptimisticLockException
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
