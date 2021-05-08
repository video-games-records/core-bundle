UPDATE vgr_game ga, vgr_group gr
SET ga.nbChart = (SELECT SUM(nbChart) FROM vgr_group WHERE idGame = ga.id)
WHERE ga.id = gr.idGame;
