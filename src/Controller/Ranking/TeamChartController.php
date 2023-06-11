<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Team\TeamChartRankingQuery;

/**
 * Class TeamChartController
 */
class TeamChartController extends AbstractController
{
    private TeamChartRankingQuery $teamChartRankingQuery;

    public function __construct(TeamChartRankingQuery $teamChartRankingQuery)
    {
        $this->teamChartRankingQuery = $teamChartRankingQuery;
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Chart $chart, Request $request): array
    {
        return $this->teamChartRankingQuery->getRankingPoints(
            $chart->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
