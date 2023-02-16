<?php

namespace VideoGamesRecords\CoreBundle\EventListener\Entity;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\DataTransformer\UserToPlayerTransformer;

class TeamListener
{
    private Security $security;
    private UserToPlayerTransformer $userToPlayerTransformer;

    /**
     * @param Security      $security
     * @param UserToPlayerTransformer $userToPlayerTransformer
     */
    public function __construct(Security $security, UserToPlayerTransformer $userToPlayerTransformer)
    {
        $this->security = $security;
        $this->userToPlayerTransformer = $userToPlayerTransformer;
    }

    /**
     * @param Team               $team
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Team $team, LifecycleEventArgs $event): void
    {
        $team->setLeader($this->userToPlayerTransformer->transform($this->security->getUser()));
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
