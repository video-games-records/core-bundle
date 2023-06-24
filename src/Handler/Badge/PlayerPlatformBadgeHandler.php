<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Platform;

class PlayerPlatformBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingProviderInterface $rankingProvider; //PlayerPlatformRankingQuery

    public function __construct(EntityManagerInterface $em, RankingProviderInterface $rankingProvider)
    {
        $this->em = $em;
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @throws ORMException
     */
    public function process(Platform $platform): void
    {
        if ($platform->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingProvider->getRankingPoints($platform->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $playerPlatform) {
            $players[$playerPlatform->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $platform->getBadge());
    }
}
