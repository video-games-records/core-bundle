<?php

namespace VideoGamesRecords\CoreBundle\Service\Ranking\Select;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Player;

class PlayerChartRankingSelect extends DefaultRankingSelect
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
        $player = $this->getPlayer();
        $team = !empty($options['idTeam']) ? $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Team', $options['idTeam']) : null;

        $query = $this->em->createQueryBuilder()
            ->select('pc')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->join('pc.player', 'p')
            ->addSelect('p')
            ->orderBy('pc.rank');

        $query->where('pc.chart = :chart')
            ->setParameter('chart', $chart);

        if ($team != null) {
            $query->andWhere('(p.team = :team)')
                ->setParameter('team', $team);
        } elseif (($maxRank !== null) && ($player !== null)) {
            $query->andWhere('(pg.rankPointChart <= :maxRank OR pc.player= :player)')
                ->setParameter('maxRank', $maxRank)
                ->setParameter('player', $player);
        } elseif ($maxRank !== null) {
            $query->andWhere('pc.rank <= :maxRank')
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

    /**
     * @param Chart $chart
     * @param array $options
     * @return array
     * @throws ORMException
     */
    public function getRanking(Chart $chart, array $options = []): array
    {
        $maxRank = $options['maxRank'] ?? null;
        $player = $this->getPlayer();

        $queryBuilder = $this->getRankingBaseQuery($chart);
        $queryBuilder->andWhere('status.boolRanking = 1');

        if (null !== $maxRank && null !== $player) {
            $rank = $this->getRank($player, $chart);
            if ($rank) {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->orX(
                            '(pc.rank <= :maxRank)',
                            '(pc.rank IS NULL)',
                            '(:min <= pc.rank) AND (pc.rank <= :max)',
                            '(pc.player = :player)'
                        )
                    )
                    ->setParameter('maxRank', $maxRank)
                    ->setParameter('player', $player)
                    ->setParameter(':min', $rank - 5)
                    ->setParameter(':max', $rank + 5);
            } else {
                $queryBuilder
                    ->andWhere(
                        $queryBuilder->expr()->orX('(pc.rank <= :maxRank)', '(pc.rank IS NULL)', 'pc.player = :player')
                    )
                    ->setParameter('maxRank', $maxRank)
                    ->setParameter('player', $player);
            }
        } elseif (null !== $maxRank) {
            $queryBuilder
                ->andWhere('pc.rank <= :maxRank')
                ->setParameter('maxRank', $maxRank);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Chart $chart
     * @return array
     */
    public function getRankingDisabled(Chart $chart): array
    {
        $queryBuilder = $this->getRankingBaseQuery($chart);
        $queryBuilder
            ->andWhere('status.boolRanking = 0');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Chart $chart
     *
     * @return QueryBuilder
     */
    private function getRankingBaseQuery(Chart $chart): QueryBuilder
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pc')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->innerJoin('pc.player', 'p')
            ->addSelect('p')
            ->innerJoin('pc.chart', 'c')
            ->innerJoin('pc.status', 'status')
            ->addSelect('status')
            ->where('c.id = :idChart')
            ->setParameter('idChart', $chart->getId());

        if ($chart->isStatusPlayerNormal()) {
            $queryBuilder->orderBy('pc.rank', 'ASC')
                ->addOrderBy('status.sOrder', 'ASC')
                ->addOrderBy('pc.lastUpdate', 'ASC');
        }

        foreach ($chart->getLibs() as $lib) {
            $key             = 'value_' . $lib->getIdLibChart();
            $alias           = 'pcl_' . $lib->getIdLibChart();
            $subQueryBuilder = $this->em->createQueryBuilder()
                ->select(sprintf('%s.value', $alias))
                ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChartLib', $alias)
                ->where(sprintf('%s.libChart = :%s', $alias, $key))
                ->andWhere(sprintf('%s.playerChart = pc', $alias))
                ->setParameter($key, $lib);

            $queryBuilder
                ->addSelect(sprintf('(%s) as %s', $subQueryBuilder->getQuery()->getDQL(), $key))
                ->setParameter($key, $lib);
            if (!$chart->isStatusPlayerNormal()) {
                $queryBuilder->addOrderBy($key, $lib->getType()->getOrderBy());
            }
        }

        return $queryBuilder;
    }

    /**
     * @param Player $player
     * @param Chart  $chart
     * @return int|null
     */
    private function getRank(Player $player, Chart $chart): ?int
    {
        $query = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->select('pc.rank')
            ->where('p.id = :player')
            ->setParameter('pc.player', $player)
            ->andWhere('pc.chart = :chart')
            ->setParameter('chart', $chart);

        try {
            return $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException|NonUniqueResultException $e) {
            return null;
        }
    }
}
