UPDATE vgr_group gr
SET gr.nbPlayer = (SELECT COUNT(DISTINCT pc.idPlayer)
            FROM vgr_player_chart pc
            JOIN vgr_chart c ON pc.idChart = c.id
            WHERE c.idGroup = gr.id)





