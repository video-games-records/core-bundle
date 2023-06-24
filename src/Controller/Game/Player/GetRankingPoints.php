<?php

namespace VideoGamesRecords\CoreBundle\Controller\Game\Player;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;


class GetRankingPoints extends AbstractController
{
    private RankingProviderInterface $rankingProvider;

    public function __construct(RankingProviderInterface $rankingProvider)
    {
        $this->rankingProvider = $rankingProvider;
    }
    /**
     * @param Game    $game
     * @param Request $request
     * @return array
     */
    public function __invoke(Game $game, Request $request): array
    {
        return $this->rankingProvider->getRankingPoints(
            $game->getId(),
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam')
            ]
        );
    }
}
