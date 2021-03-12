<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    /**
     * @param       $id
     * @param false $boolCopyLibChart
     */
    public function copy($id, $boolCopyLibChart = false)
    {
        $sql = sprintf("call copy_group (%d, %d);", $id, ($boolCopyLibChart) ? 1 : 0);
        $this->_em->getConnection()->executeUpdate($sql);
    }
}
