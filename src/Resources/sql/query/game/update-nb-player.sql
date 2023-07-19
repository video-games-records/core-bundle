UPDATE vgr_game ga
SET ga.nbPlayer = (SELECT COUNT(DISTINCT pc.idPlayer)
            FROM vgr_player_chart pc
            INNER JOIN vgr_chart c ON pc.idChart = c.id
            INNER JOIN vgr_group gr on c.idGroup = gr.id
            WHERE gr.idGame = ga.id)





