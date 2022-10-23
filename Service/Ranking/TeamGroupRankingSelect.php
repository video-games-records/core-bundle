<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Interface\RankingSelectInterface;

class TeamGroupRankingSelect implements RankingSelectInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $group = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->find($id);
        if (null === $group) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $team = $options['team'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('tg')
            ->from('VideoGamesRecords\CoreBundle\Entity\TeamGroup', 'tg')
            ->join('tg.team', 't')
            ->addSelect('t')
            ->orderBy('tg.rankPointChart');

        $query->where('tg.group = :group')
            ->setParameter('group', $group);

        if (($maxRank !== null) && ($team !== null)) {
            $query->andWhere('(tg.rankPointChart <= :maxRank OR tg.team = :team)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } elseif ($maxRank !== null) {
            $query->andWhere('tg.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }

    public function getRankingMedals(int $id = null, array $options = []): array
    {
        $group = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->find($id);
        if (null === $group) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $team = $options['team'] ?? null;

        $query = $this->em->createQueryBuilder()
            ->select('tg')
            ->from('VideoGamesRecords\CoreBundle\Entity\TeamGroup', 'tg')
            ->join('tg.team', 't')
            ->addSelect('t')
            ->orderBy('tg.rankMedal');

        $query->where('tg.group = :group')
            ->setParameter('group', $group);

        if (($maxRank !== null) && ($team !== null)) {
            $query->andWhere('(tg.rankMedal <= :maxRank OR tg.team = :team)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } elseif ($maxRank !== null) {
            $query->andWhere('tg.rankMedal <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }

        return $query->getQuery()->getResult();
    }
}
