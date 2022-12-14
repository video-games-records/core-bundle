<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Read;

use Doctrine\ORM\Exception\ORMException;

class TeamChartRankingQuery extends DefaultRankingQuery
{
    /**
     * @throws ORMException
     */
    public function getRankingPoints(int $id = null, array $options = []): array
    {
        $chart = $this->em->getRepository('VideoGamesRecords\CoreBundle\Entity\Chart')->find($id);
        if (null === $chart) {
            return [];
        }

        $maxRank = $options['maxRank'] ?? null;
        $team = $this->getTeam();

        $query = $this->em->createQueryBuilder()
            ->select('tc')
            ->from('VideoGamesRecords\CoreBundle\Entity\TeamChart', 'tc')
            ->join('tc.team', 't')
            ->addSelect('t')
            ->orderBy('tc.rankPointChart');

        $query->where('tc.chart = :chart')
            ->setParameter('chart', $chart);

        if (($maxRank !== null) && ($team !== null)) {
            $query->andWhere('(tc.rankPointChart <= :maxRank OR tc.team = :team)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('team', $team);
        } elseif ($maxRank !== null) {
            $query->andWhere('tc.rankPointChart <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        } else {
            $query->setMaxResults(100);
        }
        return $query->getQuery()->getResult();
    }

    public function getRankingMedals(int $id = null, array $options = []): array
    {
        return [];
    }
}
