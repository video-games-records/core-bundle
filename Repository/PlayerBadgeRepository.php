<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Country;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;

class PlayerBadgeRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerBadge::class);
    }

    /**
     * @param $badge
     * @return array
     */
    public function getFromBadge($badge) : array
    {
        $query = $this->createQueryBuilder('pb');
        $query
            ->where('pb.badge = :badge')
            ->setParameter('badge', $badge);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }


    /**
     * @param Country $country
     * @throws Exception
     */
    public function majCountryBadge(Country $country)
    {
        if ($country->getBadge() === null) {
            return;
        }

        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecords\CoreBundle\Entity\Player')->getRankingCountry($country, 1);

        $players = array();
        foreach ($ranking as $player) {
            $players[$player->getId()] = 0;
        }

        $this->updateBadge($players, $country->getBadge());
    }


    /**
     * @param Platform $platform
     * @throws Exception
     */
    public function majPlatformBadge(Platform $platform)
    {
        if ($platform->getBadge() === null) {
            return;
        }

        //----- get ranking with maxRank = 1
        $ranking = $this->_em->getRepository('VideoGamesRecords\CoreBundle\Entity\PlayerPlatform')->getRankingPointPlatform($platform, 1);

        $players = array();
        foreach ($ranking as $playerPlatform) {
            $players[$playerPlatform->getPlayer()->getId()] = 0;
        }

        $this->updateBadge($players, $platform->getBadge());
    }

    /**
     * @param array $players
     * @param Badge $badge
     * @throws Exception
     */
    public function updateBadge(array $players, Badge $badge)
    {
        //----- get players with badge
        $list = $this->getFromBadge($badge);

        //----- Remove badge
        foreach ($list as $playerBadge) {
            $idPlayer = $playerBadge->getPlayer()->getId();
            //----- Remove badge
            if (!array_key_exists($idPlayer, $players)) {
                $playerBadge->setEndedAt(new DateTime());
                $this->_em->persist($playerBadge);
            }
            $players[$idPlayer] = 1;
        }
        //----- Add badge
        foreach ($players as $idPlayer => $value) {
            if (0 === $value) {
                $playerBadge = new PlayerBadge();
                $playerBadge->setPlayer($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $idPlayer));
                $playerBadge->setBadge($badge);
                $this->_em->persist($playerBadge);
            }
        }
        $badge->setNbPlayer(count($players));
        $badge->majValue();

        $this->_em->flush();
    }

    /**
     * @param QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query->andWhere($query->expr()->isNull('pb.ended_at'));
    }
}
