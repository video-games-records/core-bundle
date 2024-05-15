<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Platform;

class PlatformRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Platform::class);
    }

    /**
     * Finds all entities in the repository.
     * @return array The entities.
     */
    public function findAll(): array
    {
        return $this->findBy([], ['libPlatform' => 'ASC']);
    }

    /**
     * @param string $q
     * @return mixed
     */
    public function autocomplete(string $q): mixed
    {
        $query = $this->createQueryBuilder('p');

        $query
            ->where("p.libPlatform LIKE :q")
            ->setParameter('q', '%' . $q . '%')
            ->orderBy("p.libPlatform", 'ASC');

        return $query->getQuery()->getResult();
    }
}
