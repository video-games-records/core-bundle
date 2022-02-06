<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\ORMException;

abstract class DefaultRepository extends ServiceEntityRepository
{
    protected string $entityClass;

    public function __construct(Registry $registry, $entityClass)
    {
        $this->entityClass = $entityClass;
        parent::__construct($registry, $entityClass);
    }

    /**
     * @param $object
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function save($object)
    {
        $this->_em->persist($object);
        $this->_em->flush();
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function flush()
    {
        $this->_em->flush();
    }
}
