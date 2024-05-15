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
        $this->_em->persist($object);
        $this->_em->flush();
    }


    public function flush(): void
    {
        $this->_em->flush();
    }

    /**
     * @throws ORMException
     */
    public function getReference($id)
    {
        return $this->_em->getReference($this->entityClass, $id);
    }
}
