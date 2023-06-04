<?php

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Country;
use VideoGamesRecords\CoreBundle\Service\Ranking\Read\CountryRankingQuery;

class PlayerCountryBadgeHandler
{
    private EntityManagerInterface $em;
    private CountryRankingQuery $countryRankingQuery;

    public function __construct(EntityManagerInterface $em, CountryRankingQuery $countryRankingQuery)
    {
        $this->em = $em;
        $this->countryRankingQuery = $countryRankingQuery;
    }

    public function process(Country $country): void
    {
        if ($country->getBadge() === null) {
            return;
        }

        $ranking = $this->countryRankingQuery->getRanking($country->getId(), array('maxRank' => 1));

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerBadge')->updateBadge($players, $country->getBadge());
    }
}
