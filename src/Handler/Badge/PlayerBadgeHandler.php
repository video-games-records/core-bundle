<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Handler\Badge;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

class PlayerBadgeHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     */
    public function process(): void
    {
        $sql = "INSERT INTO vgr_player_badge (idPlayer, idBadge, created_at, updated_at)
        SELECT vgr_player.id,vgr_badge.id, NOW(), NOW()
        FROM vgr_player,user,vgr_badge
        WHERE type = '%s'
        AND value <= user.%s
        AND vgr_player.user_id = user.id
        AND vgr_badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

        $this->em->getConnection()->executeQuery(sprintf($sql, 'Forum', 'nbForumMessage'));

        $sql = " INSERT INTO vgr_player_badge (idPlayer, idBadge, created_at, updated_at)
        SELECT vgr_player.id,vgr_badge.id, NOW(), NOW()
        FROM vgr_player,vgr_badge
        WHERE type = '%s'
        AND value <= vgr_player.%s
        AND vgr_badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

        $this->em->getConnection()->executeQuery(sprintf($sql, 'Connexion', 'nbConnexion'));
        $this->em->getConnection()->executeQuery(sprintf($sql, 'VgrChart', 'nbChart'));
        $this->em->getConnection()->executeQuery(sprintf($sql, 'VgrProof', 'nbChartProven'));
    }
}
