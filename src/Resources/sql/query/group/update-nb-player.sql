UPDATE vgr_group gr
SET gr.nb_player = (SELECT COUNT(DISTINCT pc.player_id)
            FROM vgr_player_chart pc
            JOIN vgr_chart c ON pc.chart_id = c.id
            WHERE c.group_id = gr.id)





