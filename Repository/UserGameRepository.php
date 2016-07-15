<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/**
 * UserGameRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserGameRepository extends EntityRepository
{
    /**
     * @param array $params idJeu|idLogin|limit|maxRank
     * @return array
     */
    public function getRankingPoints($params = array())
    {
        $query = $this->createQueryBuilder('ug')
            ->join('ug.user', 'u')
            ->addSelect('u')//----- for using ->getUser() on each result
            ->orderBy('ug.rankPoint');

        $query->where('ug.idGame= :idGame')
            ->setParameter('idGame', $params['idGame']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idLogin', $params))) {
            $query->andWhere('(ug.rankPoint <= :maxRank OR ug.idUser = :idLogin)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('idLogin', $params['idLogin']);
        } else if (array_key_exists('maxRank', $params)) {
            $query->andWhere('ug.rankPoint <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @param array $params idJeu|idLogin|limit|maxRank
     * @return array
     */
    public function getRankingMedals($params = array())
    {
        $query = $this->createQueryBuilder('ug')
            ->join('ug.user', 'u')
            ->addSelect('u')//----- for using ->getUser() on each result
            ->orderBy('ug.rankMedal');

        $query->where('ug.idGame = :idGame')
            ->setParameter('idGame', $params['idGame']);

        if (array_key_exists('limit', $params)) {
            $query->setMaxResults($params['limit']);
        }

        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idLogin', $params))) {
            $query->andWhere('(ug.rankMedal <= :maxRank OR ug.idUser = :idLogin)')
                ->setParameter('maxRank', $params['maxRank'])
                ->setParameter('idLogin', $params['idLogin']);
        } else if (array_key_exists('maxRank', $params)) {
            $query->andWhere('ug.rankMedal <= :maxRank')
                ->setParameter('maxRank', $params['maxRank']);
        }
        return $query->getQuery()->getResult();
    }


    /**
     * @param $idGame
     */
    public function maj($idGame)
    {
        //----- delete
        $query = $this->_em->createQuery('DELETE VideoGamesRecords\CoreBundle\Entity\UserGame ug WHERE ug.idGame = :idGame');
        $query->setParameter('idGame', $idGame);
        $query->execute();

        //----- data without DLC
        $query = $this->_em->createQuery("
            SELECT
                 ug.idUser,
                 SUM(ug.pointChart) as pointChartWithoutDlc,
                 SUM(ug.nbChart) as nbChartWithoutDlc,
                 SUM(ug.nbChartProven) as nbChartProvenWithoutDlc
            FROM VideoGamesRecords\CoreBundle\Entity\UserGroup ug
            JOIN ug.group g
            WHERE g.idGame = :idGame
            AND g.boolDlc = 0
            GROUP BY ug.idUser");

        $dataWithoutDlc = array();

        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();
        foreach ($result as $row) {
            $dataWithoutDlc[$row['idUser']] = $row;
        }

        //----- select ans save result in array
        $query = $this->_em->createQuery("
            SELECT
                ug.idUser,
                (g.idGame) as idGame,
                '' as rankPoint,
                '' as rankMedal,
                SUM(ug.rank0) as rank0,
                SUM(ug.rank1) as rank1,
                SUM(ug.rank2) as rank2,
                SUM(ug.rank3) as rank3,
                SUM(ug.rank4) as rank4,
                SUM(ug.rank5) as rank5,
                SUM(ug.pointChart) as pointChart,
                SUM(ug.nbChart) as nbChart,
                SUM(ug.nbChartProven) as nbChartProven
            FROM VideoGamesRecords\CoreBundle\Entity\UserGroup ug
            JOIN ug.group g
            WHERE g.idGame = :idGame
            GROUP BY ug.idUser
            ORDER BY pointChart DESC");


        $query->setParameter('idGame', $idGame);
        $result = $query->getResult();

        $list = array();
        foreach ($result as $row) {
            $row = array_merge($row, $dataWithoutDlc[$row['idUser']]);
            $list[] = $row;
        }

        //----- add some data
        $list = \VideoGamesRecords\CoreBundle\Tools\Ranking::addRank($list, 'rankPoint', array('pointChart'), true);
        $list = \VideoGamesRecords\CoreBundle\Tools\Ranking::calculateGamePoints($list, array('rankPoint', 'nbEqual'), 'pointGame', 'pointChart');
        $list = \VideoGamesRecords\CoreBundle\Tools\Ranking::order($list, array('rank0' => 'DESC', 'rank1' => 'DESC', 'rank2' => 'DESC', 'rank3' => 'DESC'));
        $list = \VideoGamesRecords\CoreBundle\Tools\Ranking::addRank($list, 'rankMedal', array('rank0', 'rank1', 'rank2', 'rank3', 'rank4', 'rank5'));

        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer(array($normalizer));

        $game = $this->_em->find('VideoGamesRecords\CoreBundle\Entity\Game', $idGame);

        foreach ($list as $row) {
            $userGame = $serializer->denormalize(
                $row,
                'VideoGamesRecords\CoreBundle\Entity\UserGame'
            );
            $userGame->setUser($this->_em->getReference('VideoGamesRecords\CoreBundle\Entity\User', $row['idUser']));
            $userGame->setGame($game);

            $this->_em->persist($userGame);
            $this->_em->flush($userGame);
        }
    }
}
