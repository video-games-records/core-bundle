UPDATE vgr_game ga
SET ga.nbPost = (SELECT COUNT(pc.idPlayer)
            FROM vgr_player_chart pc
            JOIN vgr_chart c ON pc.idChart = c.id
            INNER JOIN vgr_group gr on c.idGroup = gr.id
            WHERE gr.idGame = ga.id)





