<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use DateTime;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use VideoGamesRecords\CoreBundle\Entity\Badge;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerBadge;
use VideoGamesRecords\CoreBundle\Enum\BadgeType;

class PlayerBadgeRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerBadge::class);
    }

    /**
     * Récupère les badges d'un joueur selon le type et avec tri personnalisé
     *
     * @param Player $player Le joueur
     * @param string|array $badgeType Le type de badge (string) ou tableau de types
     * @param array $orderBy Tableau associatif pour le tri (ex: ['badge.value' => 'DESC', 'createdAt' => 'ASC'])
     * @param bool $onlyActive Si true, ne retourne que les badges actifs (ended_at = null)
     * @return array
     */
    public function findByPlayerAndType(
        Player $player,
        string|array $badgeType,
        array $orderBy = [],
        bool $onlyActive = true
    ): array {
        $qb = $this->createQueryBuilder('pb')
            ->join('pb.badge', 'b')
            ->where('pb.player = :player')
            ->setParameter('player', $player);

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
     * @throws Exception|ORMException
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
                $this->getEntityManager()->persist($playerBadge);
            }
            $players[$idPlayer] = 1;
        }
        //----- Add badge
        foreach ($players as $idPlayer => $value) {
            if (0 === $value) {
                $playerBadge = new PlayerBadge();
                $playerBadge->setPlayer($this->getEntityManager()->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $idPlayer));
                $playerBadge->setBadge($badge);
                $this->_em->persist($playerBadge);
            }
        }
        $badge->setNbPlayer(count($players));
        $badge->majValue();
    }

    /**
     * @param QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query): void
    {
        $query->andWhere($query->expr()->isNull('pb.endedAt'));
    }
}
