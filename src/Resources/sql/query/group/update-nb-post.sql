UPDATE vgr_group g
SET g.nb_post = (SELECT IFNULL(SUM(c.nb_post), 0)
                 FROM vgr_chart c
                 WHERE c.group_id = g.id)