UPDATE vgr_player p
SET p.averageChartRank = ROUND((SELECT AVG(pa.rank) FROM vgr_player_chart pa WHERE idPlayer = p.id),2)
WHERE p.nbChart > 0
