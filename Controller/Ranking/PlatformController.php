<?php

namespace VideoGamesRecords\CoreBundle\Controller\Ranking;

use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlatformRankingSelect;

/**
 * Class PlaformController
 */
class PlatformController extends AbstractController
{
    private PlatformRankingSelect $platformRankingSelect;

    public function __construct(PlatformRankingSelect $platformRankingSelect)
    {
        $this->platformRankingSelect = $platformRankingSelect;
    }

    /**
     * @param Platform $platform
     * @param Request  $request
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(Platform $platform, Request $request): array
    {
        return $this->platformRankingSelect->getRankingPoints(
            $platform->getId(),
            [
                'maxRank' => $request->query->get('maxRank', 5),
            ]
        );
    }
}
