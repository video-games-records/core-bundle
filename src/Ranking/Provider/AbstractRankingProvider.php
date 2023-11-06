<?php

namespace VideoGamesRecords\CoreBundle\Ranking\Provider;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\DataTransformer\UserToPlayerTransformer;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Security\UserProvider;

abstract class AbstractRankingProvider implements RankingProviderInterface
{
    protected EntityManagerInterface $em;
    protected UserToPlayerTransformer $userToPlayerTransformer;

    public function __construct(
        EntityManagerInterface $em,
        UserToPlayerTransformer $userToPlayerTransformer
    ) {
        $this->em = $em;
        $this->userToPlayerTransformer = $userToPlayerTransformer;
    }

    /**
     * @throws ORMException
     */
    protected function getPlayer($user = null): ?Player
    {
        if ($user === null) return null;
        return $this->userToPlayerTransformer->transform($user);
    }

    /**
     * @throws ORMException
     */
    protected function getTeam($user = null): ?Team
    {
        if ($user === null) return null;
        $player = $this->userToPlayerTransformer->transform($user);
        return $player->getTeam();
    }
}
