<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingQueryInterface;
use VideoGamesRecords\CoreBundle\Entity\Country;

class PlayerCountryBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingQueryInterface $rankingQuery; //PlayerCountryRankingQuery

    public function __construct(EntityManagerInterface $em, RankingQueryInterface $rankingQuery)
    {
        $this->em = $em;
        $this->rankingQuery = $rankingQuery;
    }

    public function process(Country $country): void
    {
        if ($country->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingQuery->getRankingPoints($country->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $country->getBadge());
    }
}
