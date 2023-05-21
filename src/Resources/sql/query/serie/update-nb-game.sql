UPDATE vgr_serie s
SET s.nbGame = (SELECT COUNT(g.id) FROM vgr_game g WHERE idSerie = s.id);
