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
     * @param array $params idMembre|idRecord|idGroupe
     * @return array
     */
    public function getFormValues($params = array())
    {
        $query = $this->createQueryBuilder('ucl')
            ->join('ucl.lib', 'lib')
            ->addSelect('lib')
            ->join('lib.type', 'type')
            ->addSelect('type')
            ->orderBy('lib.idRecord')
            ->addOrderBy('ucl.idLibRecord');

        $query->where('ucl.idMembre = :idMembre')
            ->setParameter('idMembre', $params['idMembre']);

        if (array_key_exists('idRecord', $params)) {
            $query->andWhere('lib.idRecord = :idRecord')
                ->setParameter('idRecord', $params['idRecord']);
        }

        $result = $query->getQuery()->getResult();
        $data = array();

        foreach($result as $row) {
            $data['membre_' . $row->getLib()->getIdRecord() . '_' . $row->getIdLibRecord()] = $row->getValue();
            $values = \VideoGamesRecords\CoreBundle\Tools\Score::getValues($row->getLib()->getType()->getMask(), $row->getValue());
            $i = 1;
            foreach ($values as $key => $value) {
                $data['value_' . $row->getLib()->getIdRecord() . '_' . $row->getIdLibRecord() . '_' . $i++] = $value['value'];
            }
        }

        return $data;

    }



}
