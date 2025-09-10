<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataProvider\Ranking\Player;

use Doctrine\ORM\Exception\ORMException;
use VideoGamesRecords\CoreBundle\DataProvider\Ranking\AbstractRankingProvider;

class PlayerGroupRankingProvider extends AbstractRankingProvider
{
    /**
     * @param int|null $id
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingPoints(?int $id = null, array $options = []): array
    {
        $group = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->find($id);
        if (null === $group) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer($options['user'] ?? null);
        $team = !empty($options['idTeam']) ? $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $options['idTeam']) : null;

        $query = $this->em->createQueryBuilder()
            ->select('pg')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerGroup', 'pg')
            ->join('pg.player', 'p')
            ->addSelect('p')
            ->orderBy('pg.rankPointChart');

        $query->where('pg.group = :group')
            ->setParameter('group', $group);

        if ($team != null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankPointChart <= :maxRank OR pg.player= :player OR p.id IN (:friends))')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player)
                ->setParameter('friends', $player->getFriends());
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }

    /**
     * @param int|null $id
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRankingMedals(?int $id = null, array $options = []): array
    {
        $group = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->find($id);
        if (null === $group) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer($options['user'] ?? null);

        $query = $this->em->createQueryBuilder()
            ->select('pg')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerGroup', 'pg')
            ->join('pg.player', 'p')
            ->addSelect('p')
            ->orderBy('pg.rankMedal');

        $query->where('pg.group = :group')
            ->setParameter('group', $group);

        if (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankMedal <= :maxRank OR pg.player= :player OR p.id IN (:friends))')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player)
                ->setParameter('friends', $player->getFriends());
        } elseif ($maxRank !== null) {
            $query->andWhere('pg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }
}
