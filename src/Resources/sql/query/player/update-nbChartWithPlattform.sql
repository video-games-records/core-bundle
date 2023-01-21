UPDATE vgr_player p
SET p.nbChartWithPlatform = (SELECT COUNT(id) FROM vgr_player_chart WHERE idPlatform IS NOT NULL AND idPlayer = p.id);