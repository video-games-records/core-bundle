delimiter //
DROP TRIGGER IF EXISTS `vgrRecordMembreAfterUpdate`//
CREATE TRIGGER vgrRecordMembreAfterUpdate AFTER UPDATE ON vgr_record_membre
FOR EACH ROW
BEGIN
	DECLARE done INT DEFAULT FALSE;
	DECLARE librecord_id_src INT;
	DECLARE librecord_id_dest INT;
	DECLARE cur1 CURSOR FOR SELECT idLibRecord FROM vgr_librecord WHERE idRecord = OLD.idRecord ORDER BY idLibrecord ASC;
	DECLARE cur2 CURSOR FOR SELECT idLibRecord FROM vgr_librecord WHERE idRecord = NEW.idRecord ORDER BY idLibrecord ASC;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;


	IF OLD.dateModif != NEW.dateModif THEN
		UPDATE vgr_record
	    SET statut='MAJ',
	        statutTeam = 'MAJ'
	    WHERE idRecord = OLD.idRecord;
	END IF;
	IF (OLD.idEtat != NEW.idEtat && (NEW.idEtat != 2 || NEW.idEtat != 5)) THEN
		UPDATE vgr_record
	    SET statut='MAJ'
	    WHERE idRecord = OLD.idRecord;
	END IF;
	IF (OLD.idEtat != NEW.idEtat && (OLD.idEtat = 7 || NEW.idEtat = 7) ) THEN
		UPDATE vgr_record
		SET nbPost = (SELECT COUNT(idMembre) FROM vgr_record_membre WHERE idRecord = OLD.idRecord AND idEtat != 7)
		WHERE idRecord = OLD.idRecord;
	END IF;

	-- move score
	IF OLD.idRecord != NEW.idRecord THEN

		open cur1;
		open cur2;

		read_loop: LOOP
		    FETCH cur1 INTO librecord_id_src;
		    IF done THEN
			    LEAVE read_loop;
		    END IF;
			FETCH cur2 INTO librecord_id_dest;

			UPDATE vgr_librecord_membre SET idLibRecord = librecord_id_dest WHERE idLibRecord = librecord_id_src AND idMembre = NEW.idMembre;
		END LOOP;

		CLOSE cur1;
		CLOSE cur2;

		UPDATE vgr_record
		SET nbPost = (SELECT COUNT(idMembre) FROM vgr_record_membre WHERE idRecord = NEW.idRecord AND idEtat != 7),
			statut = 'MAJ',
			statutTeam = 'MAJ'
		WHERE idRecord = NEW.idRecord;

		UPDATE vgr_record
		SET nbPost = (SELECT COUNT(idMembre) FROM vgr_record_membre WHERE idRecord = OLD.idRecord AND idEtat != 7),
			statut = 'MAJ',
			statutTeam = 'MAJ'
		WHERE idRecord = OLD.idRecord;

	END IF;

END //
delimiter ;