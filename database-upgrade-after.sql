ALTER TABLE `vgr_picture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `vgr_proof` ADD CONSTRAINT `FK_PROOF_PICTURE` FOREIGN KEY (`idPicture`) REFERENCES `vgr_picture`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `vgr_proof` ADD CONSTRAINT `FK_PROOF_VIDEO` FOREIGN KEY (`idVideo`) REFERENCES `video`(`idVideo`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- MAJ idMesageMax
UPDATE forum_forum
SET idMessageMax = null;
UPDATE forum_topic
SET idMessageMax = 0;

UPDATE forum_topic t
SET t.idMessageMax = (SELECT MAX(id) FROM forum_message WHERE idTopic = t.id);

