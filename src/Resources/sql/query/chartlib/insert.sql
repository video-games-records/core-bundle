-- Ajout des chartlib pour tout un group
INSERT INTO vgr_chartlib (idChart,idType,created_at,updated_at)
SELECT id,1,NOW(),NOW()
FROM vgr_chart
WHERE idGroup = :idGroup;


