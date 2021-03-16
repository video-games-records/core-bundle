SELECT MAX(id),COUNT(id) as nb,idPlayer,idChart,oldRank,newRank
FROM vgr_lostposition
GROUP BY idPlayer,idChart,oldRank,newRank
HAVING nb > 1




