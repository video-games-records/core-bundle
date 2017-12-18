<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use VideoGamesRecords\CoreBundle\Entity\Chart;

class ChartRepository extends EntityRepository
{
    /**
     * @param int $id
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
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isMajPlayerRunning()
    {
        $nb = $this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->where('c.statusPlayer = :status')
            ->setParameter('status', Chart::STATUS_GO_TO_MAJ)
            ->getQuery()
            ->getSingleScalarResult();
        return ($nb > 0) ? true : false;
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
        return ($nb > 0) ? true : false;
    }

    /**
     * @param int $limit
     * @throws \Doctrine\DBAL\DBALException
     */
    public function goToMajPlayer($limit)
    {
        $sql = sprintf("UPDATE vgr_chart SET statusPlayer = '%s' WHERE statusPlayer='%s' LIMIT %d", Chart::STATUS_GO_TO_MAJ, Chart::STATUS_MAJ, $limit);
        $this->_em->getConnection()->executeUpdate($sql);
    }

    /**
     * @param int $limit
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
}
