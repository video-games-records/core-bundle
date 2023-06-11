<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerGroupRankingQuery;

/**
 * Class PlayerGroupController
 */
class PlayerGroupController extends AbstractController
{
    private PlayerGroupRankingQuery $playerGroupRankingQuery;

    public function __construct(PlayerGroupRankingQuery $playerGroupRankingQuery)
    {
        $this->playerGroupRankingQuery = $playerGroupRankingQuery;
    }

    /**
     * @param Group   $group
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Group $group, Request $request): array
    {
        return $this->playerGroupRankingQuery->getRankingPoints(
            $group->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }


    /**
     * @param Group   $group
     * @param Request $request
     * @return array
     */
    public function getRankingMedals(Group $group, Request $request): array
    {
        return $this->playerGroupRankingQuery->getRankingMedals(
            $group->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }
}
