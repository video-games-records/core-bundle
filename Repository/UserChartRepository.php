<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use VideoGamesRecords\CoreBundle\Tools\Ranking;

/**
 * UserChartRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserChartRepository extends EntityRepository
{
    /**
     * @param array $params idChart|idLogin|limit|maxRank
     * @todo
     * => Join etat to keep only boolRanking = 1
     * => If idLogin, search for the rank and display a range of -5 and +5
     * @return array
     */
    public function getRanking($params = array())
    {
        /** @var \VideoGamesRecords\CoreBundle\Entity\Chart $chart */
        $chart = $params['chart'];

        $rsm = new ResultSetMapping;
        $rsm->addEntityResult('VideoGamesRecords\CoreBundle\Entity\UserChart', 'uc', 'uc');
        $rsm->addFieldResult('uc', 'idChart', 'idChart');
        $rsm->addFieldResult('uc', 'idUser', 'idUser');
        $rsm->addFieldResult('uc', 'rank', 'rank');
        $rsm->addFieldResult('uc', 'nbEqual', 'nbEqual');
        $rsm->addFieldResult('uc', 'pointChart', 'pointChart');
        $rsm->addFieldResult('uc', 'idEtat', 'idEtat');
        $rsm->addFieldResult('uc', 'dateModif', 'dateModif');
        //$rsm->addJoinedEntityResult('VideoGamesRecords\CoreBundle\Entity\User' , 'u', 'uc', 'user');
        //$rsm->addFieldResult('u','pseudo','pseudo');
        //$rsm->addFieldResult('u','idMembre','idMembre');

        $fields = array();
        $orders = array();
        $where = array();
        $parameters = array();
        $columns = array();

        $fields[] = 'uc.*';
        $fields[] = 'u.*';

        $where[] = 'uc.idChart = :idChart';
        $parameters['idChart'] = $params['idChart'];

        foreach ($chart->getLibs() as $lib) {
            $columnName = "value_" . $lib->getIdLibChart();
            $fields[] = "(SELECT value FROM vgr_user_chartlib WHERE idLibChart=" . $lib->getIdLibChart() . " AND idUser = uc.idUser) AS $columnName";
            $orders[] = $columnName . " " . $lib->getType()->getOrderBy();
            $columns[] = $columnName;
            $rsm->addScalarResult($columnName, $columnName);
        }


        if ((array_key_exists('maxRank', $params)) && (array_key_exists('idLogin', $params))) {
            $where[] = '(uc.rank <= :maxRank OR uc.idMembre = :idLogin)';
            $parameters['maxRank'] = $params['maxRank'];
            $parameters['idLogin'] = $params['idLogin'];
        } else if (array_key_exists('maxRank', $params)) {
            $where[] = 'uc.rank <= :maxRank';
            $parameters['maxRank'] = $params['maxRank'];
        }

        $where[] = 'uc.rank IS NOT NULL'; //----- Disabeld post


        $sql = sprintf(
            "SELECT %s
            FROM vgr_user_chart uc INNER JOIN vgr_member u ON uc.idUser = u.idUser
            WHERE %s ORDER BY %s",
            implode(',', $fields),
            implode(' AND ', $where),
            implode(',', $orders)
        );

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsm);
        foreach ($parameters as $key => $value) {
            $query->setParameter($key, $value);
        }

        //var_dump($query->getResult()); exit;
        $result = $query->getResult();

        $list = array();
        foreach ($result as $row) {
            $list[] = $row;
        }
        $list = Ranking::addChartRank($list, $columns);

        return $list;
    }

    /**
     * @param int $idChart
     * @todo disabled post (Rank is null)
     */
    public function maj($idChart)
    {
        $chart = $this->_em->getRepository('VideoGamesRecordsCoreBundle:Chart')->getWithChartType($idChart);
        $ranking = $this->getRanking(
            array(
                'idChart' => $idChart,
                'chart' => $chart,
            )
        );

        //----- Array of pointChart
        $pointsChart = Ranking::arrayPointRecord(count($ranking));

        foreach ($ranking as $k => $row) {
            /** @var \VideoGamesRecords\CoreBundle\Entity\UserChart $userChart */
            $userChart = $row['uc'];
            //----- If equal
            if ($userChart->getNbEqual() == 1) {
                $pointChart = $pointsChart[$userChart->getRank()];
            } else {
                $pointChart = (int)(
                    array_sum(
                        array_slice(array_values($pointsChart), $userChart->getRank() - 1, $userChart->getNbEqual())
                    ) / $userChart->getNbEqual()
                );
            }
            $userChart->setPointChart($pointChart);

            $this->_em->persist($userChart);
            $this->_em->flush($userChart);
        }
    }
}
