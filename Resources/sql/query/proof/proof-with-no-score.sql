SELECT *
FROM vgr_proof LEFT JOIN vgr_player_chart ON vgr_proof.id  = vgr_player_chart.idProof
WHERE status = 'IN PROGRESS'
AND vgr_player_chart.idProof IS NULL



