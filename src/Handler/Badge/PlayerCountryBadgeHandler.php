<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingProviderInterface;
use VideoGamesRecords\CoreBundle\Entity\Country;

class PlayerCountryBadgeHandler
{
    private EntityManagerInterface $em;
    private RankingProviderInterface $rankingProvider;

    public function __construct(EntityManagerInterface $em, RankingProviderInterface $rankingProvider)
    {
        $this->em = $em;
        $this->rankingProvider = $rankingProvider;
    }

    public function process(Country $country): void
    {
        if ($country->getBadge() === null) {
            return;
        }

        $ranking = $this->rankingProvider->getRankingPoints($country->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $country->getBadge());
    }
}
