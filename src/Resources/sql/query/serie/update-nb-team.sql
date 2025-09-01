UPDATE vgr_serie s
SET s.nb_team = (SELECT COUNT(ts.team_id) FROM vgr_team_serie ts WHERE serie_id = s.id);
