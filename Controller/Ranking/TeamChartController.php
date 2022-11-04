<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\TeamChartRankingSelect;

/**
 * Class TeamChartController
 */
class TeamChartController extends AbstractController
{
    private TeamChartRankingSelect $teamChartRankingSelect;

    public function __construct(TeamChartRankingSelect $teamChartRankingSelect)
    {
        $this->teamChartRankingSelect = $teamChartRankingSelect;
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Chart $chart, Request $request): array
    {
        return $this->teamChartRankingSelect->getRankingPoints(
            $chart->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
