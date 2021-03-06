<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    /**
     * @param $id
     * @throws DBALException
     */
    public function copy($id)
    {
        $sql = sprintf("call copy_group (%d);", $id);
        $this->_em->getConnection()->executeUpdate($sql);
    }
}
