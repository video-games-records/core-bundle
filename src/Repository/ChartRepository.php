<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use ApiPlatform\Doctrine\Orm\Paginator;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

class ChartRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chart::class);
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countStatusPlayerMaj(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatusPlayer($qb, ChartStatus::MAJ);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return mixed
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function countStatusTeamMaj(): mixed
    {
        $qb = $this->getCountQueryBuilder();
        $this->whereStatusTeam($qb, ChartStatus::MAJ);
        return $qb->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param Player $player
     * @param int $page
     * @param array $search
     * @param string $locale
     * @param int $itemsPerPage
     * @return Paginator
     */
    public function getList(
        Player $player,
        int $page = 1,
        array $search = array(),
        string $locale = 'en',
        int $itemsPerPage = 20
    ): Paginator {
        $firstResult = ($page - 1) * $itemsPerPage;

        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->join('gr.game', 'ga')
            ->leftJoin('ch.playerCharts', 'pc', 'WITH', 'pc.player = :player')
            ->addSelect('gr')
            ->addSelect('pc')
            ->setParameter('player', $player);

        $this->setOrder($query, $locale);

        if (isset($search['game'])) {
            $query->andWhere('gr.game = :game')
                ->setParameter('game', $search['game']);
        }
        if (isset($search['group'])) {
            $query->andWhere('ch.group = :group')
                ->setParameter('group', $search['group']);
        }
        if (isset($search['chart'])) {
            $query->andWhere('ch = :chart')
                ->setParameter('chart', $search['chart']);
        }
        if (isset($search['term'])) {
            $column = ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
            $query->andWhere("ch.$column LIKE :term")
                ->setParameter('term', '%' . $search['term'] . '%');
        }
        $query = $query->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($itemsPerPage);
        $doctrinePaginator = new DoctrinePaginator($query);
        return new Paginator($doctrinePaginator);
    }

    /*************************************/
    /************  PRIVATE  **************/
    /*************************************/

    /**
     * @return QueryBuilder
     */
    private function getCountQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->select('COUNT(c.id)');
    }

    /**
     * @param QueryBuilder $query
     * @param string       $status
     */
    private function whereStatusPlayer(QueryBuilder $query, string $status): void
    {
        $query
            ->andWhere('c.statusPlayer = :status')
            ->setParameter('status', $status);
    }

    /**
     * @param QueryBuilder $query
     * @param string       $status
     */
    private function whereStatusTeam(QueryBuilder $query, string $status): void
    {
        $query
            ->andWhere('c.statusTeam = :status')
            ->setParameter('status', $status);
    }

    /**
     * @param QueryBuilder $query
     * @param string       $locale
     * @return void
     */
    private function setOrder(QueryBuilder $query, string $locale = 'en'): void
    {
        $column = ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
        $query->orderBy("ch.$column", 'ASC');
    }
}
