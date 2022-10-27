<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\Select\PlayerPlatformRankingSelect;

/**
 * Class PlayerPlaformController
 */
class PlayerPlatformController extends AbstractController
{
    private PlayerPlatformRankingSelect $playerPlatformRankingSelect;

    public function __construct(PlayerPlatformRankingSelect $playerPlatformRankingSelect)
    {
        $this->playerPlatformRankingSelect = $playerPlatformRankingSelect;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Platform $platform, Request $request): array
    {
        return $this->playerPlatformRankingSelect->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
