UPDATE vgr_game g
SET g.nb_video = (SELECT COUNT(id) FROM vgr_video WHERE game_id = g.id);