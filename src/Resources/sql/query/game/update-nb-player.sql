UPDATE vgr_game ga
SET ga.nb_player = (SELECT COUNT(DISTINCT pc.player_id)
            FROM vgr_player_chart pc
            INNER JOIN vgr_chart c ON pc.chart_id = c.id
            INNER JOIN vgr_group gr on c.group_id = gr.id
            WHERE gr.game_id = ga.id)





