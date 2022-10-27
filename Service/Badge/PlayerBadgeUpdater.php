<?php

namespace VideoGamesRecords\CoreBundle\Service\Badge;

use Doctrine\ORM\EntityManagerInterface;

class PlayerBadgeUpdater
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function process(): void
    {
        $sql = "INSERT INTO vgr_player_badge (idPlayer, idBadge)
        SELECT vgr_player.id,vgr_badge.id
        FROM vgr_player,user,vgr_badge
        WHERE type = '%s'
        AND value <= user.%s
        AND vgr_player.normandie_user_id = user.id
        AND vgr_badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

        $this->em->getConnection()->executeUpdate(sprintf($sql, 'Connexion', 'nbConnexion'));
        $this->em->getConnection()->executeUpdate(sprintf($sql, 'Forum', 'nbForumMessage'));

        $sql = " INSERT INTO vgr_player_badge (idPlayer, idBadge)
        SELECT vgr_player.id,vgr_badge.id
        FROM vgr_player,vgr_badge
        WHERE type = '%s'
        AND value <= vgr_player.%s
        AND vgr_badge.id NOT IN (SELECT idBadge FROM vgr_player_badge WHERE idPlayer = vgr_player.id)";

        $this->em->getConnection()->executeUpdate(sprintf($sql, 'VgrChart', 'nbChart'));
        $this->em->getConnection()->executeUpdate(sprintf($sql, 'VgrProof', 'nbChartProven'));
    }
}
