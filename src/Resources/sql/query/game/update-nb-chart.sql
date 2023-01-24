UPDATE vgr_game ga
SET ga.nbChart = (SELECT IFNULL(SUM(nbChart),0) FROM vgr_group WHERE idGame = ga.id);
