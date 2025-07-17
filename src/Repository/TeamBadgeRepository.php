<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\TeamBadge;

class TeamBadgeRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamBadge::class);
    }

    /**
     * @param $badge
     * @return array
     */
    public function getFromBadge($badge): array
    {
        $query = $this->createQueryBuilder('tb');
        $query
            ->where('tb.badge = :badge')
            ->setParameter('badge', $badge);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }


    /**
     * @param array $teams
     * @param Badge $badge
     * @throws Exception
     */
    public function updateBadge(array $teams, Badge $badge): void
    {
        //----- get players with badge
        $list = $this->getFromBadge($badge);

        //----- Remove badge
        foreach ($list as $teamBadge) {
            $idTeam = $teamBadge->getTeam()->getId();
            //----- Remove badge
            if (!array_key_exists($idTeam, $teams)) {
                $teamBadge->setEndedAt(new DateTime());
                $this->getEntityManager()->persist($teamBadge);
            }
            $teams[$idTeam] = 1;
        }
        //----- Add badge
        foreach ($teams as $idTeam => $value) {
            if ($value == 0) {
                $teamBadge = new TeamBadge();
                $teamBadge->setTeam(
                    $this->getEntityManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $idTeam)
                );
                $teamBadge->setBadge($badge);
                $this->getEntityManager()->persist($teamBadge);
            }
        }
    }

    /**
     * @param QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query): void
    {
        $query->andWhere($query->expr()->isNull('tb.endedAt'));
    }
}
