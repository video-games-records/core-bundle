<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Serie;

class PlayerSerieBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingProviderInterface $rankingProvider; //PlayerSerieRankingQuery

    public function __construct(EntityManagerInterface $em, RankingProviderInterface $rankingProvider)
    {
        $this->em = $em;
        $this->rankingProvider = $rankingProvider;
    }

    /**
     * @throws ORMException
     */
    public function process(Serie $serie): void
    {
        if ($serie->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingProvider->getRankingPoints($serie->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $playerSerie) {
            $players[$playerSerie->getPlayer()->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $serie->getBadge());
    }
}
