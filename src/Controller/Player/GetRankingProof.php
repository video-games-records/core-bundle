<?php

namespace VideoGamesRecords\CoreBundle\Controller\Player;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Ranking\Provider\Player\PlayerRankingProvider;

class GetRankingProof extends AbstractController
{
    private PlayerRankingProvider $playerRankingProvider;

    public function __construct(PlayerRankingProvider $playerRankingProvider)
    {
        $this->playerRankingProvider = $playerRankingProvider;
    }

    /**
     * @param Request $request
     * @return array
     * @throws ORMException
     */
    public function __invoke(Request $request): array
    {
        return $this->playerRankingProvider->getRankingProof(
            [
                'maxRank' => $request->query->get('maxRank', '5'),
                'idTeam' => $request->query->get('idTeam'),
                'limit' => $request->query->get('limit')
            ]
        );
    }
}
