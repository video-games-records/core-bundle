SELECT vgr_badge.*
FROM vgr_badge
    INNER JOIN vgr_serie ON vgr_badge.id = vgr_serie.idBadge


SELECT vgr_badge.*
FROM vgr_badge
         INNER JOIN vgr_game ON vgr_badge.id = vgr_game.idBadge


SELECT count(*) as nb,type, picture
FROM vgr_badge
WHERE type = 'Serie'
GROUP BY type,picture
HAVING count(*) > 1


    SELECT * FROM vgr_badge
WHERE type='Master'
  AND id NOT IN (SELECT idBadge FROM vgr_game)


SELECT * FROM vgr_badge
WHERE type='Serie'
  AND id NOT IN (SELECT idBadge FROM vgr_serie)
