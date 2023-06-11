<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team\TeamRankingQuery;

/**
 * Class TeamController
 */
class TeamController extends AbstractController
{
    private TeamRankingQuery $teamRankingQuery;

    public function __construct(TeamRankingQuery $teamRankingQuery)
    {
        $this->teamRankingQuery = $teamRankingQuery;
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPointChart(Request $request): array
    {
        return $this->teamRankingQuery->getRankingPointChart(
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPointGame(Request $request): array
    {
        return $this->teamRankingQuery->getRankingPointGame(
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingMedals(Request $request): array
    {
        return $this->teamRankingQuery->getRankingMedals(
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingCup(Request $request): array
    {
        return $this->teamRankingQuery->getRankingCup(
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingBadge(Request $request): array
    {
        return $this->teamRankingQuery->getRankingBadge(
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
