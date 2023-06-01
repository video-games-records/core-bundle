UPDATE vgr_serie s
SET s.nbChart = (SELECT IFNULL(SUM(nbChart),0) FROM vgr_game WHERE idSerie = s.id);
