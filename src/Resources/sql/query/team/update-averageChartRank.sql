UPDATE vgr_team t
SET t.averageChartRank = ROUND((SELECT AVG(tc.rankPointChart) FROM vgr_team_chart tc WHERE tc.idTeam = t.id),2)
