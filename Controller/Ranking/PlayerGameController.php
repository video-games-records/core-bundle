<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlayerGameRankingQuery;

/**
 * Class PlayerGameController
 */
class PlayerGameController extends AbstractController
{
    private PlayerGameRankingQuery $playerGameRankingQuery;

    public function __construct(PlayerGameRankingQuery $playerGameRankingQuery)
    {
        $this->playerGameRankingQuery = $playerGameRankingQuery;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Game $game, Request $request): array
    {
        return $this->playerGameRankingQuery->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function getRankingMedals(Game $game, Request $request): array
    {
        return $this->playerGameRankingQuery->getRankingMedals(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }
}
