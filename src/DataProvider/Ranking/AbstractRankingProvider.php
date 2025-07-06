<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataProvider\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\DataTransformer\UserToPlayerTransformer;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;

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
        if ($user === null) {
            return null;
        }
        return $this->userToPlayerTransformer->transform($user);
    }

    /**
     * @throws ORMException
     */
    protected function getTeam($user = null): ?Team
    {
        if ($user === null) {
            return null;
        }
        $player = $this->userToPlayerTransformer->transform($user);
        return $player->getTeam();
    }
}
