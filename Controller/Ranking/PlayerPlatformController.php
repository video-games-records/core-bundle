<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use VideoGamesRecords\CoreBundle\Controller\DefaultController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\PlayerPlatformRankingSelect;

/**
 * Class PlayerPlaformController
 */
class PlayerPlatformController extends DefaultController
{
    private PlayerPlatformRankingSelect $playerPlatformRankingSelect;

    public function __construct(PlayerPlatformRankingSelect $playerPlatformRankingSelect)
    {
        $this->playerPlatformRankingSelect = $playerPlatformRankingSelect;
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
        return $this->playerPlatformRankingSelect->getRankingPoints($platform->getId(), $options);
    }
}
