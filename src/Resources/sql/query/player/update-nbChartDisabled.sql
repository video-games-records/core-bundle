UPDATE vgr_player p
SET p.nbChartDisabled = (SELECT COUNT(id) FROM vgr_player_chart WHERE idStatus = 7 AND idPlayer = p.id);