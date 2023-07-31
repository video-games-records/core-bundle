UPDATE vgr_player p
SET p.nbVideo = (SELECT COUNT(id) FROM vgr_video WHERE idPlayer = p.id);