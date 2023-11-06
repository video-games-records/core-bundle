<?php

namespace VideoGamesRecords\CoreBundle\Controller\Chart\Player;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerChartRankingProvider;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class GetRankingPoints extends AbstractController
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
        return $this->playerChartRankingProvider->getRankingPoints(
            $chart->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'user' => $this->getUser()
            ]
        );
    }
}
