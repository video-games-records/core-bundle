<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Security;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
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
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->security->getUser();
    }

    /**
     * @return Player
     * @throws ORMException
     */
    public function getPlayer(): Player
    {
        return $this->userToPlayerTransformer->transform($this->security->getUser());
    }

    /**
     * @return Team|null
     * @throws ORMException
     */
    public function getTeam(): ?Team
    {
        $player = $this->userToPlayerTransformer->transform($this->security->getUser());
        return $player->getTeam();
    }
}
