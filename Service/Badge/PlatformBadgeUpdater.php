<?php

namespace VideoGamesRecords\CoreBundle\Service\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlatformRankingSelect;

class PlatformBadgeUpdater
{
    private EntityManagerInterface $em;
    private PlatformRankingSelect $platformRankingSelect;

    public function __construct(EntityManagerInterface $em, PlatformRankingSelect $platformRankingSelect)
    {
        $this->em = $em;
        $this->platformRankingSelect = $platformRankingSelect;
    }

    /**
     * @throws ORMException
     */
    public function process(Platform $platform): void
    {
        if ($platform->getBadge() === null) {
            return;
        }

        $ranking = $this->platformRankingSelect->getRankingPoints($platform->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $platform->getBadge());
    }
}
