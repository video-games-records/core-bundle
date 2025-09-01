UPDATE vgr_serie s
SET s.nb_player = (SELECT COUNT(ps.player_id) FROM vgr_player_serie ps WHERE serie_id = s.id);
