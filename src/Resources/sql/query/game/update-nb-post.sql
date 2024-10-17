UPDATE vgr_game ga
SET ga.nb_post = (SELECT IFNULL(SUM(gr.nb_post), 0)
                  FROM vgr_group gr
                  WHERE gr.game_id = ga.id)