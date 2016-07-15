<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * UserChartLibRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserChartLibRepository extends EntityRepository
{
    /**
     * @param array $params idMembre|idChart|idGroupe
     * @return array
     */
    public function getFormValues($params = array())
    {
        $query = $this->createQueryBuilder('ucl')
            ->join('ucl.lib', 'lib')
            ->addSelect('lib')
            ->join('lib.type', 'type')
            ->addSelect('type')
            ->orderBy('lib.idChart')
            ->addOrderBy('ucl.idLibChart');

        $query->where('ucl.idUser = :idUser')
            ->setParameter('idUser', $params['idUser']);

        if (array_key_exists('idChart', $params)) {
            $query->andWhere('lib.idChart = :idChart')
                ->setParameter('idChart', $params['idChart']);
        }

        $result = $query->getQuery()->getResult();
        $data = array();

        foreach ($result as $row) {
            $data['user_' . $row->getLib()->getIdChart() . '_' . $row->getIdLibChart()] = $row->getValue();
            $values = \VideoGamesRecords\CoreBundle\Tools\Score::getValues($row->getLib()->getType()->getMask(), $row->getValue());
            $i = 1;
            foreach ($values as $key => $value) {
                $data['value_' . $row->getLib()->getIdChart() . '_' . $row->getIdLibChart() . '_' . $i++] = $value['value'];
            }
        }

        return $data;
    }
}
