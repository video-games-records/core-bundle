<?php

namespace VideoGamesRecords\CoreBundle\Service\Stats\Write;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GroupStatsHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function majNbPlayer(Group $group): void
    {
        $query = $this->em->createQuery(
            "
            SELECT COUNT(DISTINCT pc.player)
            FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc
            JOIN pc.chart c
            WHERE c.group = :group"
        );
        $query->setParameter('group', $group);

        $nb = $query->getSingleScalarResult();
        $group->setNbPlayer($nb);
        $this->em->flush();
    }
}
