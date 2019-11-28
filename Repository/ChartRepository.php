<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Paginator;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class ChartRepository extends EntityRepository
{

    const ITEMS_PER_PAGE = 10;

    /**
     * @param int $id
     *
     * @return \VideoGamesRecords\CoreBundle\Entity\Chart
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getWithGame($id)
    {
        $query = $this->createQueryBuilder('ch')
            ->leftJoin('ch.translations', 'ch_translation')
            ->addSelect('ch_translation')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->leftJoin('gr.translations', 'gr_translation')
            ->addSelect('gr_translation')
            ->join('gr.game', 'ga')
            ->addSelect('ga')
            ->leftJoin('ga.translations', 'ga_translation')
            ->addSelect('ga_translation')
            ->where('ch.id = :idChart')
            ->setParameter('idChart', $id);

        return $query->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @param $id
     *
     * @return \VideoGamesRecords\CoreBundle\Entity\Chart
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getWithChartType($id)
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
     * @param $id
     *
     * @return array
     */
    public function getFromGroupWithChartType($id)
    {
        $query = $this->createQueryBuilder('c')
            ->join('c.libs', 'lib')
            ->addSelect('lib')
            ->join('lib.type', 'type')
            ->addSelect('type')
            ->where('c.idGroup = :idGroup')
            ->setParameter('idGroup', $id);

        return $query->getQuery()->getResult();
    }


    /**
     * @return bool
     */
    public function isMajPlayerRunning()
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isMajTeamRunning()
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
     * @throws \Doctrine\DBAL\DBALException
     */
    public function goToMajPlayer($limit)
    {
        $sql = sprintf("UPDATE vgr_chart SET statusPlayer = '%s' WHERE statusPlayer='%s' LIMIT %d", Chart::STATUS_GO_TO_MAJ, Chart::STATUS_MAJ, $limit);
        $this->_em->getConnection()->executeUpdate($sql);
    }

    /**
     * @param int $limit
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function goToMajTeam($limit)
    {
        $sql = sprintf("UPDATE vgr_chart SET statusTeam = '%s' WHERE statusPlayer='%s' AND statusTeam='%s' LIMIT %d", Chart::STATUS_GO_TO_MAJ, Chart::STATUS_NORMAL, Chart::STATUS_MAJ, $limit);
        $this->_em->getConnection()->executeUpdate($sql);
    }

    /**
     * @return \VideoGamesRecords\CoreBundle\Entity\Chart[]
     */
    public function getChartToMajPlayer()
    {
        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->andWhere('ch.statusPlayer = :status')
            ->setParameter('status', Chart::STATUS_GO_TO_MAJ);

        return $query->getQuery()->getResult();
    }

    /**
     * @return \VideoGamesRecords\CoreBundle\Entity\Chart[]
     */
    public function getChartToMajTeam()
    {
        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->addSelect('gr')
            ->andWhere('ch.statusTeam = :status')
            ->setParameter('status', Chart::STATUS_GO_TO_MAJ);

        return $query->getQuery()->getResult();
    }

    /**
     * @param int   $page
     * @param null  $game
     * @param null  $player
     * @param array $search
     * @return Paginator
     */
    public function getList(int $page = 1, $game = null, $player = null, $search = array()) : Paginator
    {
        $firstResult = ($page -1) * self::ITEMS_PER_PAGE;

        $query = $this->createQueryBuilder('ch')
            ->join('ch.group', 'gr')
            ->leftJoin('ch.playerCharts', 'pc', 'WITH', 'pc.player = :player')
            ->addSelect('gr')
            ->addSelect('pc')
            ->andWhere('gr.game = :game')
            ->setParameter('game', $game)
            ->setParameter('player', $player);
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
            ->setMaxResults(self::ITEMS_PER_PAGE);
        $doctrinePaginator = new DoctrinePaginator($query);
        $paginator = new Paginator($doctrinePaginator);

        return $paginator;
    }


    /**
     * @param        $game
     * @param string $status
     */
    public function majStatus($game, $status = 'MAJ')
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
