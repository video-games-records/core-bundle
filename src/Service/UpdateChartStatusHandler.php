<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\ValueObject\ChartStatus;

class UpdateChartStatusHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function playerMajStatusTeam(Player $player): void
    {
        $conn = $this->em->getConnection();
        $sql = 'UPDATE vgr_chart
            SET statusTeam = :status
            WHERE id IN (SELECT idChart FROM vgr_player_chart WHERE idPlayer = :idPlayer)';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['status' => ChartStatus::STATUS_MAJ, 'idPlayer' => $player->getId()]);

        /* DONT WORK
        $query = $this->em->createQueryBuilder()
            ->update('VideoGamesRecords\CoreBundle\Entity\Chart', 'c')
            ->set('c.statusTeam', ':status')
            ->setParameter('status', ChartStatus::STATUS_MAJ)
            ->where('c IN (SELECT pc.id FROM VideoGamesRecords\CoreBundle\Entity\PlayerChart pc WHERE pc.player = :player)')
            ->setParameter('player', $player);
        $query->getQuery()->execute();*/
    }
}
