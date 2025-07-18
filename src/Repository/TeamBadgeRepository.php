<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\Team;
use VideoGamesRecords\CoreBundle\Entity\TeamBadge;
use VideoGamesRecords\CoreBundle\Enum\BadgeType;

class TeamBadgeRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TeamBadge::class);
    }

    /**
     * Récupère les badges d'une team selon le type et avec tri personnalisé
     *
     * @param Team $team la team
     * @param string|array $badgeType Le type de badge (string) ou tableau de types
     * @param array $orderBy Tableau associatif pour le tri (ex: ['badge.value' => 'DESC', 'createdAt' => 'ASC'])
     * @param bool $onlyActive Si true, ne retourne que les badges actifs (ended_at = null)
     * @return array
     */
    public function findByTeamAndType(
        Team $team,
        string|array $badgeType,
        array $orderBy = [],
        bool $onlyActive = true
    ): array {
        $qb = $this->createQueryBuilder('tb')
            ->join('tb.badge', 'b')
            ->where('tb.team = :team')
            ->setParameter('team', $team);

        // Filtre sur le type de badge
        if (is_array($badgeType)) {
            $qb->andWhere('b.type IN (:badgeTypes)')
                ->setParameter('badgeTypes', $badgeType);
        } else {
            $qb->andWhere('b.type = :badgeType')
                ->setParameter('badgeType', $badgeType);

            // Si le type est Master, on ajoute la jointure avec game
            if ($badgeType === BadgeType::MASTER->value) {
                $qb->leftJoin('VideoGamesRecords\CoreBundle\Entity\Game', 'g', 'WITH', 'g.badge = b')
                    ->addSelect('g');
            }

            if ($badgeType === BadgeType::SERIE->value) {
                $qb->leftJoin('VideoGamesRecords\CoreBundle\Entity\Serie', 's', 'WITH', 's.badge = b')
                    ->addSelect('s');
            }
        }

        // Filtre sur les badges actifs si demandé
        if ($onlyActive) {
            $this->onlyActive($qb);
        }

        // Application du tri
        foreach ($orderBy as $field => $direction) {
            $qb->addOrderBy($field, $direction);
        }

        return $qb->getQuery()->getResult();
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
