DELETE vgr_lostposition
FROM vgr_lostposition
    INNER JOIN vgr_player_chart ON vgr_lostposition.idPlayer = vgr_player_chart.idPlayer AND vgr_lostposition.idChart = vgr_player_chart.idChart
WHERE (vgr_player_chart.rank <= vgr_lostposition.oldRank)
OR (vgr_player_chart.rank =1 AND vgr_player_chart.nbEqual = 1 AND vgr_lostposition.oldRank = 0)