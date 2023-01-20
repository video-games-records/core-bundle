UPDATE vgr_team t
SET t.averageGameRank = ROUND((SELECT AVG(tg.rankPointChart) FROM vgr_team_game tg WHERE idTeam = t.id),2)
WHERE t.nbGame > 0
