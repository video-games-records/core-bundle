<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\Persistence\ManagerRegistry;

abstract class DefaultRepository extends ServiceEntityRepository
{
    protected string $entityClass;

    public function __construct(ManagerRegistry $registry, $entityClass)
    {
        $this->entityClass = $entityClass;
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param $object
     */
    public function save($object): void
    {
        $this->getEntityManager()->persist($object);
        $this->getEntityManager()->flush();
    }


    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    /**
     * @throws ORMException
     */
    public function getReference($id)
    {
        return $this->getEntityManager()->getReference($this->entityClass, $id);
    }
}
