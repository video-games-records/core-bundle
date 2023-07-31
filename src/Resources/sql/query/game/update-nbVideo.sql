UPDATE vgr_game g
SET g.nbVideo = (SELECT COUNT(id) FROM vgr_video WHERE idGame = g.id);