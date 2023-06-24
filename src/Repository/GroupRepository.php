<?php

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;

class GroupRepository extends EntityRepository
{
    /**
     * @param       $id
     * @param false $boolCopyLibChart
     * @throws Exception
     */
    public function copy($id, bool $boolCopyLibChart = false): void
    {
        $sql = sprintf("call copy_group (%d, %d);", $id, ($boolCopyLibChart) ? 1 : 0);
        $this->_em->getConnection()->executeStatement($sql);
    }

    /**
     * @param int $idGroup
     * @param int $idType
     * @return int|string
     * @throws Exception
     */
    public function insertLibChart(int $idGroup, int $idType): int|string
    {
        $sql = "INSERT INTO vgr_chartlib (idChart,idType,created_at)
            SELECT id,:idType,NOW()
            FROM vgr_chart
            WHERE idGroup = :idGroup";
        return $this->_em->getConnection()->executeStatement(
            $sql,
            [
                'idGroup' => $idGroup,
                'idType' => $idType,
            ]
        );
    }
}
