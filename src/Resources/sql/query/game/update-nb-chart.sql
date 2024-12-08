UPDATE vgr_game ga
SET ga.nb_chart = (SELECT IFNULL(SUM(nb_chart),0) FROM vgr_group WHERE game_id = ga.id);
