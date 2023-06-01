<?php

namespace VideoGamesRecords\CoreBundle\Security;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Security\Core\Security;
use VideoGamesRecords\CoreBundle\DataTransformer\UserToPlayerTransformer;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

class UserProvider
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
     * @return ?Player
     * @throws ORMException
     */
    public function getPlayer(): ?Player
    {
        if ($this->security->getUser()) {
            return $this->userToPlayerTransformer->transform($this->security->getUser());
        }
        return null;
    }

    /**
     * @return Team|null
     * @throws ORMException
     */
    public function getTeam(): ?Team
    {
        if ($this->security->getUser()) {
            $player = $this->userToPlayerTransformer->transform($this->security->getUser());
            return $player->getTeam();
        }
        return null;
    }
}
