<?php

namespace VideoGamesRecords\CoreBundle\Controller\Chart\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerChartRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Tools\Score;

class GetRankingDisabled extends AbstractController
{
    private PlayerChartRankingProvider $playerChartRankingProvider;

    public function __construct(PlayerChartRankingProvider $playerChartRankingProvider)
    {
        $this->playerChartRankingProvider = $playerChartRankingProvider;
    }

    /**
     * @param Chart    $chart
     * @return array
     */
    public function __invoke(Chart $chart): array
    {
        $ranking = $this->playerChartRankingProvider->getRankingDisabled($chart);

        for ($i = 0; $i <= count($ranking) - 1; $i++) {
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
