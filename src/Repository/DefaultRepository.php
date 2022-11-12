<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;

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
     */
    public function save($object)
    {
        $this->_em->persist($object);
        $this->_em->flush();
    }


    public function flush()
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
