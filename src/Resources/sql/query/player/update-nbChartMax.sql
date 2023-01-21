UPDATE vgr_player p
SET p.nbChartMax =
    (SELECT IFNULL(SUM(vgr_game.nbChart),0)
    FROM vgr_player_game
    INNER JOIN vgr_game ON vgr_player_game.idGame = vgr_game.id
    WHERE vgr_player_game.idPlayer = p.id)

