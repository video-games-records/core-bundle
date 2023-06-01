<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

class TeamListener
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
     * @param Team               $team
     * @param LifecycleEventArgs $event
     * @return void
     */
    public function prePersist(Team $team, LifecycleEventArgs $event): void
    {
        $team->setLeader($this->userProvider->getPlayer());
    }


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
