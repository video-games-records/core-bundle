<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * UserSerieRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserSerieRepository extends EntityRepository
{
    /**
     * @param array $params idSerie|idLogin|limit|maxRank
     * @return array
     */
    public function getRankingPoints($params = array())
    {
        $query = $this->createQueryBuilder('us')
            ->join('us.user', 'u')
            ->addSelect('u')//----- for using ->getUser() on each result
            ->orderBy('us.rankPoint');

        $query->where('us.idSerie = :idSerie')
            ->setParameter('idSerie', $params['idSerie']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        $row = null;
        if ((array_key_exists('idLogin', $params)) && ($params['idLogin'] != null)) {
            $row = $this->findOneBy(
                array(
                    'idSerie' => $params['idSerie'],
                    'idMembre' => $params['idLogin']
                )
            );
        }

        if ((array_key_exists('maxRank', $params)) && ($row)) {
            $query->andWhere('(us.rankPoint <= :maxRank OR us.rankPoint BETWEEN :min AND :max)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('min', $row->getRankPoint() - 5)
                ->setParameter('max', $row->getRankPoint() + 5);
        } else if (array_key_exists('maxRank', $params)) {
            $query->andWhere('us.rankPoint <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @param array $params idSerie|idLogin|limit|maxRank
     * @return array
     */
    public function getRankingMedals($params = array())
    {
        $query = $this->createQueryBuilder('us')
            ->join('us.user', 'u')
            ->addSelect('u')//----- for using ->getUser() on each result
            ->orderBy('us.rankMedal');

        $query->where('us.idSerie = :idSerie')
            ->setParameter('idSerie', $params['idSerie']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        $row = null;
        if ((array_key_exists('idLogin', $params)) && ($params['idLogin'] != null)) {
            $row = $this->findOneBy(
                array(
                    'idSerie' => $params['idSerie'],
                    'idMembre' => $params['idLogin']
                )
            );
        }

        if ((array_key_exists('maxRank', $params)) && ($row)) {
            $query->andWhere('(us.rankMedal <= :maxRank OR us.rankMedal BETWEEN :min AND :max)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('min', $row->getRankMedal() - 5)
                ->setParameter('max', $row->getRankMedal() + 5);
        } else if (array_key_exists('maxRank', $params)) {
            $query->andWhere('us.rankMedal <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * @param $idSerie
     */
    public function maj($idSerie)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\UserSerie us WHERE us.idSerie = :idSerie');
        $query->setParameter('idSerie', $idSerie);
        $query->execute();

        //----- select ans save result in table
        $query = $this->_em->createQuery("
            SELECT
                ug.idUser,
                (g.idSerie) as idSerie,
                '' as rankPoint,
                '' as rankMedal,
                SUM(ug.rank0) as rank0,
                SUM(ug.rank1) as rank1,
                SUM(ug.rank2) as rank2,
                SUM(ug.rank3) as rank3,
                SUM(ug.rank4) as rank4,
                SUM(ug.rank5) as rank5,
                SUM(ug.pointGame) as pointGame,
                SUM(ug.pointChart) as pointChart,
                SUM(ug.pointChartWithoutDlc) as pointChartWithoutDlc,
                SUM(ug.nbChart) as nbChart,
                SUM(ug.nbChartWithoutDlc) as nbChartWithoutDlc,
                SUM(ug.nbChartProven) as nbChartProven,
                SUM(ug.nbChartProvenWithoutDlc) as nbChartProvenWithoutDlc,
                COUNT(DISTINCT ug.idGame) as nbGame
            FROM VideoGamesRecords\CoreBundle\Entity\UserGame ug
            JOIN ug.game g
            WHERE g.idSerie = :idSerie
            GROUP BY ug.idUser
            ORDER BY pointChart DESC");


        $query->setParameter('idSerie', $idSerie);
        $result = $query->getResult();

        $list = array();
        foreach ($result as $row) {
            $list[] = $row;
        }

        $list = \VideoGamesRecords\CoreBundle\Tools\Ranking::addRank($list, 'rankPoint', array('pointChart'));
        $list = \VideoGamesRecords\CoreBundle\Tools\Ranking::order($list, array('rank0' => 'DESC', 'rank1' => 'DESC', 'rank2' => 'DESC', 'rank3' => 'DESC'));
        $list = \VideoGamesRecords\CoreBundle\Tools\Ranking::addRank($list, 'rankMedal', array('rank0', 'rank1', 'rank2', 'rank3', 'rank4', 'rank5'));

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(array($normalizer));

        $serie = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Serie', $idSerie);

        foreach ($list as $row) {
            $userSerie = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\UserSerie'
            );
            $userSerie->setUser($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\User', $row['idUser']));
            $userSerie->setSerie($serie);

            $this->_em->persist($userSerie);
            $this->_em->flush($userSerie);
        }
    }
}
