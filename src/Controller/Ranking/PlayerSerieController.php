<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Serie;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player\PlayerSerieRankingQuery;

/**
 * Class PlayerSerieController
 */
class PlayerSerieController extends AbstractController
{
    private PlayerSerieRankingQuery $playerSerieRankingQuery;

    public function __construct(PlayerSerieRankingQuery $playerSerieRankingQuery)
    {
        $this->playerSerieRankingQuery = $playerSerieRankingQuery;
    }

    /**
     * @param Serie    $serie
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Serie $serie, Request $request): array
    {
        return $this->playerSerieRankingQuery->getRankingPoints(
            $serie->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
                'limit' => $request->query->get('limit', 100),
            ]
        );
    }


    /**
     * @param Serie    $serie
     * @param Request $request
     * @return array
     */
    public function getRankingMedals(Serie $serie, Request $request): array
    {
        return $this->playerSerieRankingQuery->getRankingMedals(
            $serie->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 100),
                'limit' => $request->query->get('limit', 100),
            ]
        );
    }
}
