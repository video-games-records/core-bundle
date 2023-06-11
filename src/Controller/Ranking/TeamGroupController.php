<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team\TeamGroupRankingQuery;

/**
 * Class TeamGroupController
 */
class TeamGroupController extends AbstractController
{
    private TeamGroupRankingQuery $teamGroupRankingQuery;

    public function __construct(TeamGroupRankingQuery $teamGroupRankingQuery)
    {
        $this->teamGroupRankingQuery = $teamGroupRankingQuery;
    }

    /**
     * @param Group $group
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Group $group, Request $request): array
    {
        return $this->teamGroupRankingQuery->getRankingPoints(
            $group->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }

    /**
     * @param Group    $group
     * @param Request $request
     * @return array
     */
    public function getRankingMedals(Group $group, Request $request): array
    {
        return $this->teamGroupRankingQuery->getRankingMedals(
            $group->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
