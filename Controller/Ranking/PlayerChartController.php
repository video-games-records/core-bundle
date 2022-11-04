<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlayerChartRankingSelect;
use VideoGamesRecords\CoreBundle\Tools\Score;

/**
 * Class PlayerChartController
 */
class PlayerChartController extends AbstractController
{
    private PlayerChartRankingSelect $playerChartRankingSelect;

    public function __construct(PlayerChartRankingSelect $playerChartRankingSelect)
    {
        $this->playerChartRankingSelect = $playerChartRankingSelect;
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Chart $chart, Request $request): array
    {
        return $this->playerChartRankingSelect->getRankingPoints(
            $chart->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }


    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRanking(Chart $chart, Request $request): array
    {
        $ranking = $this->playerChartRankingSelect->getRanking(
            $chart,
            [
                'maxRank' => $request->query->get('maxRank', 100),
            ]
        );

        if (!$chart->isStatusPlayerNormal()) {
            $i = 1;
            foreach ($ranking as $row) {
                $row[0]->setRank($i);
                $i++;
            }
        }

        for ($i=0; $i<=count($ranking)-1; $i++) {
            foreach ($chart->getLibs() as $lib) {
                $key = $lib->getIdLibChart();
                // format value
                $ranking[$i]['values'][] = Score::formatScore(
                    $ranking[$i]["value_$key"],
                    $lib->getType()->getMask()
                );
            }
        }
        return $ranking;
    }

    /**
     * @param Chart    $chart
     * @return array
     */
    public function getRankingDisabled(Chart $chart)
    {
        $ranking = $this->playerChartRankingSelect->getRankingDisabled($chart);

        for ($i=0; $i<=count($ranking)-1; $i++) {
            foreach ($chart->getLibs() as $lib) {
                $key = $lib->getIdLibChart();
                // format value
                $ranking[$i]['values'][] = Score::formatScore(
                    $ranking[$i]["value_$key"],
                    $lib->getType()->getMask()
                );
            }
        }

        return $ranking;
    }
}
