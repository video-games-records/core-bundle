<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Badge;
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
    public function getFromBadge($badge): array
    {
        $query = $this->createQueryBuilder('pb');
        $query
            ->where('pb.badge = :badge')
            ->setParameter('badge', $badge);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }

    /**
     * @param array $players
     * @param Badge $badge
     * @throws Exception
     */
    public function updateBadge(array $players, Badge $badge): void
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
    private function onlyActive(QueryBuilder $query): void
    {
        $query->andWhere($query->expr()->isNull('pb.endedAt'));
    }
}
