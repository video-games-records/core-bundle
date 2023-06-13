<?php

namespace VideoGamesRecords\CoreBundle\Ranking\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

abstract class AbstractRankingProvider implements RankingProviderInterface
{
    protected EntityManagerInterface $em;
    protected UserProvider $userProvider;

    public function __construct(
        EntityManagerInterface $em,
        UserProvider $userProvider
    ) {
        $this->em = $em;
        $this->userProvider = $userProvider;
    }

    /**
     * @throws ORMException
     */
    protected function getPlayer(): ?Player
    {
        if ($this->userProvider->getUser()) {
            return $this->userProvider->getPlayer();
        }
        return null;
    }

    /**
     * @throws ORMException
     */
    protected function getTeam(): ?Team
    {
        if ($this->userProvider->getUser()) {
            return $this->userProvider->getTeam();
        }
        return null;
    }
}
