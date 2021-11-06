<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Group;

class GroupRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    /**
     * @param       $id
     * @param false $boolCopyLibChart
     * @throws Exception
     */
    public function copy($id, bool $boolCopyLibChart = false)
    {
        $sql = sprintf("call copy_group (%d, %d);", $id, ($boolCopyLibChart) ? 1 : 0);
        $this->_em->getConnection()->executeUpdate($sql);
    }
}
