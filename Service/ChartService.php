<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\EntityManagerInterface;

class ChartService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager(): EntityManagerInterface
    {
        return $this->em;
    }
}
