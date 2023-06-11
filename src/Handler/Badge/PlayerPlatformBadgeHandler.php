<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingQueryInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\PlatformRankingQuery;

class PlayerPlatformBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingQueryInterface $rankingQuery; //PlayerPlatformRankingQuery

    public function __construct(EntityManagerInterface $em, RankingQueryInterface $rankingQuery)
    {
        $this->em = $em;
        $this->rankingQuery = $rankingQuery;
    }

    /**
     * @throws ORMException
     */
    public function process(Platform $platform): void
    {
        if ($platform->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingQuery->getRankingPoints($platform->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $playerPlatform) {
            $players[$playerPlatform->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $platform->getBadge());
    }
}
