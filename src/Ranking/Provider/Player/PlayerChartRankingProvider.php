<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Ranking\Provider\Player;

use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Ranking\Provider\AbstractRankingProvider;

class PlayerChartRankingProvider extends AbstractRankingProvider
{
    public const ORDER_BY_RANK = 'RANK';
    public const ORDER_BY_SCORE = 'SCORE';

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
        $player = $this->getPlayer($options['user'] ?? null);
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
        $player = $this->getPlayer($options['user'] ?? null);

        $orderBy = $chart->getStatusPlayer()->isNormal() ? self::ORDER_BY_RANK : self::ORDER_BY_SCORE;
        $queryBuilder = $this->getRankingBaseQuery($chart, $orderBy);
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
        $queryBuilder = $this->getRankingBaseQuery($chart, self::ORDER_BY_SCORE);
        $queryBuilder
            ->andWhere('status.boolRanking = 0');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Chart  $chart
     * @param string $orderBy
     * @return QueryBuilder
     */
    private function getRankingBaseQuery(Chart $chart, string $orderBy = self::ORDER_BY_RANK): QueryBuilder
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('pc')
            ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChart', 'pc')
            ->innerJoin('pc.player', 'p')
            ->addSelect('p')
            ->innerJoin('pc.chart', 'c')
            ->innerJoin('pc.status', 'status')
            ->addSelect('status')
            ->leftJoin('pc.proof', 'proof')
            ->addSelect('proof')
            ->leftJoin('pc.platform', 'platform')
            ->addSelect('platform')
            ->where('c.id = :idChart')
            ->setParameter('idChart', $chart->getId());

        if (self::ORDER_BY_RANK === $orderBy) {
            $queryBuilder->orderBy('pc.rank', 'ASC')
                ->addOrderBy('status.sOrder', 'ASC')
                ->addOrderBy('pc.lastUpdate', 'ASC');
        }

        foreach ($chart->getLibs() as $lib) {
            $key             = 'value_' . $lib->getId();
            $alias           = 'pcl_' . $lib->getId();
            $subQueryBuilder = $this->em->createQueryBuilder()
                ->select(sprintf('%s.value', $alias))
                ->from('VideoGamesRecords\CoreBundle\Entity\PlayerChartLib', $alias)
                ->where(sprintf('%s.libChart = :%s', $alias, $key))
                ->andWhere(sprintf('%s.playerChart = pc', $alias))
                ->setParameter($key, $lib);

            $queryBuilder
                ->addSelect(sprintf('(%s) as %s', $subQueryBuilder->getQuery()->getDQL(), $key))
                ->setParameter($key, $lib);
            if (self::ORDER_BY_SCORE === $orderBy) {
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
            ->where('pc.player = :player')
            ->setParameter('player', $player)
            ->andWhere('pc.chart = :chart')
            ->setParameter('chart', $chart);

        try {
            return $query->getQuery()->getSingleScalarResult();
        } catch (NoResultException | NonUniqueResultException $e) {
            return null;
        }
    }
}
