<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class ChartRepository extends EntityRepository
{
    /**
     * @param $id
     * @return Chart
     * @throws NonUniqueResultException
     */
    public function getWithChartType($id): Chart
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.libs', 'lib')
            ->addSelect('lib')
            ->join('lib.type', 'type')
            ->addSelect('type')
            ->where('c.id = :idChart')
            ->setParameter('idChart', $id);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isMajPlayerRunning(): bool
    {
        $nb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statusPlayer = :status')
            ->setParameter('status', Chart::STATUS_GO_TO_MAJ)
            ->getQuery()
            ->getSingleScalarResult();

        return $nb > 0;
    }

    /**
     * @return bool
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function isMajTeamRunning(): bool
    {
        $nb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statusTeam = :status')
            ->setParameter('status', Chart::STATUS_GO_TO_MAJ)
            ->getQuery()
            ->getSingleScalarResult();

        return $nb > 0;
    }

    /**
     * @param int $limit
     *
     * @throws DBALException
     */
    public function goToMajPlayer(int $limit)
    {
        $sql = sprintf("UPDATE vgr_chart SET statusPlayer = '%s' WHERE statusPlayer='%s' LIMIT %d", Chart::STATUS_GO_TO_MAJ, Chart::STATUS_MAJ, $limit);
        $this->_em->getConnection()->executeStatement($sql);
    }

    /**
     * @throws DBALException
     */
    public function goToNormalPlayer()
    {
        $sql = sprintf("UPDATE vgr_chart SET statusPlayer = '%s' WHERE statusPlayer='%s'", Chart::STATUS_NORMAL, Chart::STATUS_GO_TO_MAJ);
        $this->_em->getConnection()->executeStatement($sql);
    }

    /**
     * @param int $limit
     *
     * @throws DBALException
     */
    public function goToMajTeam(int $limit)
    {
        $sql = sprintf(
            "UPDATE vgr_chart SET statusTeam = '%s' WHERE statusPlayer='%s' AND statusTeam='%s' LIMIT %d",
            Chart::STATUS_GO_TO_MAJ,
            Chart::STATUS_NORMAL,
            Chart::STATUS_MAJ,
            $limit
        );
        $this->_em->getConnection()->executeStatement($sql);
    }

    /**
     * @return Chart[]
     */
    public function getChartToMajPlayer(): array
    {
        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->andWhere('ch.statusPlayer = :status')
            ->setParameter('status', Chart::STATUS_GO_TO_MAJ);

        return $query->getQuery()->getResult();
    }

    /**
     * @return Chart[]
     */
    public function getChartToMajTeam(): array
    {
        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->andWhere('ch.statusTeam = :status')
            ->setParameter('status', Chart::STATUS_GO_TO_MAJ);

        return $query->getQuery()->getResult();
    }

    /**
     * @param int    $page
     * @param null   $player
     * @param array  $search
     * @param string $locale
     * @param int    $itemsPerPage
     * @return Paginator
     */
    public function getList(int $page = 1, $player = null, array $search = array(), $locale = 'en', int $itemsPerPage = 20) : Paginator
    {
        $firstResult = ($page - 1) * $itemsPerPage;

        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->join('gr.game', 'ga')
            ->leftJoin('ch.playerCharts', 'pc', 'WITH', 'pc.player = :player')
            ->addSelect('gr')
            ->addSelect('pc')
            ->setParameter('player', $player);

        $this->setOrder($query, $locale);

        if ($search['idGame'] != null) {
            $query->andWhere('ga.id = :idGame')
                ->setParameter('idGame', $search['idGame']);
        }
        if ($search['idGroup'] != null) {
            $query->andWhere('gr.id = :idGroup')
                ->setParameter('idGroup', $search['idGroup']);
        }
        if ($search['idChart'] != null) {
            $query->andWhere('ch.id = :idChart')
                ->setParameter('idChart', $search['idChart']);
        }
        $query = $query->getQuery()
            ->setFirstResult($firstResult)
            ->setMaxResults($itemsPerPage);
        $doctrinePaginator = new DoctrinePaginator($query);
        return new Paginator($doctrinePaginator);
    }


    /**
     * @param        $group
     * @param        $player
     * @param string $locale
     * @return int|mixed|string
     */
    public function getTopScore($group, $player, string $locale = 'en')
    {
        $query = $this->createQueryBuilder('ch')
             ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->addSelect('pc')
            ->andWhere('ch.group = :group')
            ->setParameter('group', $group);

        $this->setOrder($query, $locale);

        if ($player !== null) {
            $query->leftJoin('ch.playerCharts', 'pc', 'WITH', 'pc.rank = 1 OR pc.player = :player')
                ->setParameter('player', $player);
        } else {
            $query->leftJoin('ch.playerCharts', 'pc', 'WITH', 'pc.rank = 1');
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param QueryBuilder $query
     * @param string       $locale
     */
    private function setOrder(QueryBuilder $query, string $locale = 'en')
    {
        $column = ($locale == 'fr') ? 'libChartFr' : 'libChartEn';
        $query->orderBy("ch.$column", 'ASC');
    }


    /**
     * @param        $game
     * @param string $status
     */
    public function majStatus($game, string $status = 'MAJ')
    {
        $qb = $this->_em->createQueryBuilder();
        $query = $qb->update('VideoGamesRecords\CoreBundle\Entity\Chart', 'c')
            ->set('c.statusPlayer', ':status')
            ->set('c.statusTeam', ':status')
            ->setParameter('status', $status)
            ->where('c.group IN (
                            SELECT g FROM VideoGamesRecords\CoreBundle\Entity\Group g
                        WHERE g.game = :game)')
            ->setParameter('game', $game);

        $query->getQuery()->execute();
    }
}
