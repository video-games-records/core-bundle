UPDATE vgr_player p
SET p.averageGameRank = ROUND((SELECT AVG(pg.rankPointChart) FROM vgr_player_game pg WHERE idPlayer = p.id),2)
WHERE p.nbChart > 0
