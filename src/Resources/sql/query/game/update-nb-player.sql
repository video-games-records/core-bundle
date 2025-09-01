UPDATE vgr_game ga
SET ga.nb_player = (SELECT COUNT(pg.player_id) FROM vgr_player_game pg WHERE game_id = ga.id);




