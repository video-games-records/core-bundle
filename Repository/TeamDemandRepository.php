<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\TeamDemand;


class TeamDemandRepository extends EntityRepository
{

    /**
     * @param $idTeam
     * @return array
     */
    public function getFromTeam($idTeam)
    {
        $query = $this->createQueryBuilder('td');

        $query->where('td.idTeam = :idTeam')
            ->setParameter('idTeam', $idTeam);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }

    /**
     * @param $idPlayer
     * @return array
     */
    public function getFromPlayer($idPlayer)
    {
        $query = $this->createQueryBuilder('td');

        $query->where('td.idPlayer = :idPlayer')
            ->setParameter('idTeam', $idPlayer);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }

    /**
     * Requires only active games.
     *
     * @param \Doctrine\ORM\QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query
            ->andWhere('td.status = :status')
            ->setParameter('status', TeamDemand::STATUS_ACTIVE);
    }
}
