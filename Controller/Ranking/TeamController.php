<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\TeamRankingSelect;

/**
 * Class TeamController
 */
class TeamController extends AbstractController
{
    private TeamRankingSelect $teamRankingSelect;

    public function __construct(TeamRankingSelect $teamRankingSelect)
    {
        $this->teamRankingSelect = $teamRankingSelect;
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPointChart(Request $request): array
    {
        return $this->teamRankingSelect->getRankingPointChart(
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
        return $this->teamRankingSelect->getRankingPointGame(
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
        return $this->teamRankingSelect->getRankingMedals(
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
        return $this->teamRankingSelect->getRankingCup(
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
        return $this->teamRankingSelect->getRankingBadge(
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
