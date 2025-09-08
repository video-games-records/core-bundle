<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class TeamRequestListener
{
    private UserProvider $userProvider;

    /**
     * @param UserProvider $userProvider
     */
    public function __construct(UserProvider $userProvider)
    {
        $this->userProvider = $userProvider;
    }


    /**
     * @param TeamRequest $teamRequest
     * @param LifecycleEventArgs $event
     * @return void
     * @throws ORMException
     */
    public function prePersist(TeamRequest $teamRequest, LifecycleEventArgs $event): void
    {
        $teamRequest->setPlayer($this->userProvider->getPlayer());
    }

    /**
     * @param TeamRequest        $teamRequest
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(TeamRequest $teamRequest, LifecycleEventArgs $event): void
    {
        $em = $event->getObjectManager();
        if ($teamRequest->getTeamRequestStatus()->isAccepted()) {
            $player = $teamRequest->getPlayer();
            $player->setTeam($teamRequest->getTeam());
            $em->flush();
        }
    }
}
