INSERT INTO vgr_picture (id, path, metadata, idPlayer, idGame, hash)
SELECT vgr_proof.idPicture,
    concat('path', vgr_proof.idPicture),
    'metadata',
    vgr_player_chart.idPlayer,
    MIN(vgr_group.idGame),
    concat('hash', vgr_proof.idPicture)
FROM vgr_proof
INNER JOIN vgr_player_chart ON vgr_proof.id = vgr_player_chart.idProof
INNER JOIN vgr_chart ON vgr_chart.id = vgr_player_chart.idChart
INNER JOIN vgr_group ON vgr_group.id = vgr_chart.idGroup
WHERE vgr_proof.idPicture IS NOT NULL
GROUP BY vgr_proof.idPicture, vgr_player_chart.idPlayer;


SELECT DISTINCT
    vgr_proof.idPicture,
    concat('path', vgr_proof.idPicture),
    'metadata',
    vgr_player_chart.idPlayer,
    vgr_group.idGame
FROM vgr_proof
INNER JOIN vgr_player_chart ON vgr_proof.id = vgr_player_chart.idProof
INNER JOIN vgr_chart ON vgr_chart.id = vgr_player_chart.idChart
INNER JOIN vgr_group ON vgr_group.id = vgr_chart.idGroup
WHERE vgr_proof.idPicture IS NOT NULL
AND vgr_proof.idPicture = 521977;



SELECT MAX(idPreuve), idMembre, idRecord, COUNT(idPreuve)
FROM vgr_preuves
GROUP BY idMembre, idRecord
HAVING count(idPreuve) > 1;


-- 493973