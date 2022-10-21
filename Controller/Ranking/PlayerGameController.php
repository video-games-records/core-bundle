<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerGameRanking;

/**
 * Class PlayerGameController
 */
class PlayerGameController extends DefaultController
{
    private PlayerGameRanking $playerGameRanking;

    public function __construct(PlayerGameRanking $playerGameRanking)
    {
        $this->playerGameRanking = $playerGameRanking;
    }

    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Game $game, Request $request)
    {
        $idTeam = $request->query->get('idTeam', null);
        $options = [
            'maxRank' => $request->query->get('maxRank', 5),
            'player' => $this->getPlayer(),
            'team' => $idTeam ? $this->getDoctrine()->getManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam) : null,
        ];
        return $this->playerGameRanking->getRankingPoints($game->getId(), $options);
    }


    /**
     * @param Game    $game
     * @param Request $request
     * @return mixed
     */
    public function playerRankingMedals(Game $game, Request $request)
    {
        $maxRank = $request->query->get('maxRank', 5);
        return $this->getDoctrine()->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerGame')->getRankingMedals($game, $maxRank, $this->getPlayer());
    }


}
