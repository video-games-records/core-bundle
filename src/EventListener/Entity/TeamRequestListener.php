<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;

class TeamRequestListener
{
    /**
     * @param TeamRequest        $teamRequest
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(TeamRequest $teamRequest, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        if ($teamRequest->getStatus() == TeamRequest::STATUS_ACCEPTED) {
            $player = $teamRequest->getPlayer();
            $player->setTeam($teamRequest->getTeam());
            $em->flush();
        }
    }
}
