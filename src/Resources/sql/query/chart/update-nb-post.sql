UPDATE vgr_chart c
SET c.nb_post = (SELECT COUNT(pc.id)
            FROM vgr_player_chart pc
            WHERE pc.chart_id = c.id)





