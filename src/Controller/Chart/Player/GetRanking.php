<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Chart\Player;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerChartRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Tools\Score;

class GetRanking extends AbstractController
{
    private PlayerChartRankingProvider $playerChartRankingProvider;

    public function __construct(PlayerChartRankingProvider $playerChartRankingProvider)
    {
        $this->playerChartRankingProvider = $playerChartRankingProvider;
    }

    /**
     * @param Chart   $chart
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function __invoke(Chart $chart, Request $request): array
    {
        $ranking = $this->playerChartRankingProvider->getRanking(
            $chart,
            [
                'maxRank' => $request->query->get('maxRank', '1000'),
                'user' => $this->getUser()
            ]
        );

        if (!$chart->getStatusPlayer()->isNormal()) {
            $i = 1;
            foreach ($ranking as $row) {
                $row[0]->setRank($i);
                $i++;
            }
        }

        for ($i = 0; $i <= count($ranking) - 1; $i++) {
            foreach ($chart->getLibs() as $lib) {
                $key = $lib->getId();
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
