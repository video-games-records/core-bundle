<?php

namespace VideoGamesRecords\CoreBundle\Service\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlatformRankingQuery;

class PlatformBadgeHandler
{
    private EntityManagerInterface $em;
    private PlatformRankingQuery $platformRankingQuery;

    public function __construct(EntityManagerInterface $em, PlatformRankingQuery $platformRankingQuery)
    {
        $this->em = $em;
        $this->platformRankingQuery = $platformRankingQuery;
    }

    /**
     * @throws ORMException
     */
    public function process(Platform $platform): void
    {
        if ($platform->getBadge() === null) {
            return;
        }

        $ranking = $this->platformRankingQuery->getRankingPoints($platform->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $playerPlatform) {
            $players[$playerPlatform->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $platform->getBadge());
    }
}
