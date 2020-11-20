<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use VideoGamesRecords\CoreBundle\Entity\TeamRequest;

class TeamRequestRepository extends EntityRepository
{
    /**
     * @param $idTeam
     * @return array
     */
    public function getFromTeam($idTeam)
    {
        $query = $this->createQueryBuilder('tr');

        $query->where('tr.idTeam = :idTeam')
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
        $query = $this->createQueryBuilder('tr');

        $query->where('tr.idPlayer = :idPlayer')
            ->setParameter('idPlayer', $idPlayer);

        $this->onlyActive($query);

        return $query->getQuery()->getResult();
    }


    /**
     * @param $idPlayer
     * @param $idTeam
     * @return mixed
     * @throws NonUniqueResultException
     */

    public function getFromPlayerAndTeam($idPlayer, $idTeam)
    {
        $query = $this->createQueryBuilder('tr');

        $query->where('tr.idPlayer = :idPlayer')
            ->setParameter('idPlayer', $idPlayer);

        $query->andWhere('tr.idTeam = :idTeam')
            ->setParameter('idTeam', $idTeam);

        $this->onlyActive($query);

        return $query->getQuery()->getOneOrNullResult();
    }

    /**
     * Requires only active requests.
     *
     * @param QueryBuilder $query
     */
    private function onlyActive(QueryBuilder $query)
    {
        $query
            ->andWhere('tr.status = :status')
            ->setParameter('status', TeamRequest::STATUS_ACTIVE);
    }
}
