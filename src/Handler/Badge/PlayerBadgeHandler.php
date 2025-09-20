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
    public function handle(): void
    {
        /*$sql = "INSERT INTO vgr_player_badge (player_id, badge_id, created_at, updated_at)
        SELECT vgr_player.id,vgr_badge.id, NOW(), NOW()
        FROM vgr_player,pnu_user,vgr_badge
        WHERE type = '%s'
        AND value <= pnu_user.%s
        AND vgr_player.user_id = pnu_user.id
        AND vgr_badge.id NOT IN (SELECT badge_id FROM vgr_player_badge WHERE player_id = vgr_player.id)";

        $this->em->getConnection()->executeQuery(sprintf($sql, 'Forum', 'nb_forum_message'));*/

        $sql = "INSERT INTO vgr_player_badge (player_id, badge_id, created_at, updated_at)
        SELECT vgr_player.id,vgr_badge.id, NOW(), NOW()
        FROM vgr_player,vgr_badge
        WHERE type = '%s'
        AND value <= vgr_player.%s
        AND vgr_badge.id NOT IN (SELECT badge_id FROM vgr_player_badge WHERE player_id = vgr_player.id)";

        $this->em->getConnection()->executeQuery(sprintf($sql, 'Connexion', 'nb_connexion'));
        $this->em->getConnection()->executeQuery(sprintf($sql, 'VgrChart', 'nb_chart'));
        $this->em->getConnection()->executeQuery(sprintf($sql, 'VgrProof', 'nb_chart_proven'));
    }
}
