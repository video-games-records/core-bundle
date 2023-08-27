SELECT
    vgr_game.libGameEn,
    vgr_group.libGroupEn,
    vgr_chart.libChartEn
FROM `vgr_player_chart`
    INNER JOIN vgr_chart ON vgr_player_chart.idChart = vgr_chart.id
    INNER JOIN vgr_group ON vgr_chart.idGroup = vgr_group.id
    INNER JOIN vgr_game ON vgr_group.idGame = vgr_game.id
WHERE `idPlayer` = 6094
  AND `idPlatform` IS NULL
ORDER BY vgr_game.libGameEn ASC, vgr_group.libGroupEn ASC, vgr_chart.libChartEn