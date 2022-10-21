<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerPlatformRanking;

/**
 * Class PlayerPlaformController
 */
class PlayerPlatformController extends DefaultController
{
    private PlayerPlatformRanking $playerPlaformRanking;

    public function __construct(PlayerPlatformRanking $playerPlaformRanking)
    {
        $this->playerPlaformRanking = $playerPlaformRanking;
    }

    /**
     * @param Platform $platform
     * @param Request $request
     * @return array
     */
    public function getRankingPoints(Platform $platform, Request $request): array
    {
        $options = [
            'maxRank' => $request->query->get('maxRank', 5),
            'player' => $this->getPlayer(),
        ];
        return $this->playerPlaformRanking->getRankingPoints($platform->getId(), $options);
    }
}
