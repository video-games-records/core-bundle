<?php

namespace VideoGamesRecords\CoreBundle\Service\Badge;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Country;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\CountryRankingSelect;

class CountryBadgeUpdater
{
    private EntityManagerInterface $em;
    private CountryRankingSelect $countryRankingSelect;

    public function __construct(EntityManagerInterface $em, CountryRankingSelect $countryRankingSelect)
    {
        $this->em = $em;
        $this->countryRankingSelect = $countryRankingSelect;
    }

    public function process(Country $country): void
    {
        if ($country->getBadge() === null) {
            return;
        }

        $ranking = $this->countryRankingSelect->getRanking($country->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $country->getBadge());
    }
}
