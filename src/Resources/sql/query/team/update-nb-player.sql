UPDATE vgr_team t
SET t.nbPlayer = (SELECT COUNT(DISTINCT p.id)
            FROM vgr_player p
            WHERE p.idTeam = t.id)





