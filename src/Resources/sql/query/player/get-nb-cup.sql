SELECT
    COUNT(g.id) as nb
FROM vgr_player_game pg
JOIN vgr_game g ON g.id = pg.game_id
WHERE pg.rank_point_chart = 1
  AND pg.player_id = 11377
  AND g.nb_player > 1
  AND g.is_rank = 1
  AND pg.nb_equal = 1
