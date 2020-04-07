DROP PROCEDURE IF EXISTS move_score;
DELIMITER &&
CREATE PROCEDURE move_score(IN src_record_id int, IN dest_record_id INT)
BEGIN

  DECLARE done INT DEFAULT FALSE;
  DECLARE membre_id INT;
  DECLARE cur1 CURSOR FOR SELECT idMembre FROM vgr_record_membre WHERE idRecord = src_record_id;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  SELECT COUNT(idLibRecord) INTO @nb1 FROM vgr_librecord WHERE idRecord = src_record_id;
  SELECT COUNT(idLibRecord) INTO @nb2 FROM vgr_librecord WHERE idRecord = dest_record_id;

  IF ((@nb1 = 1) AND (@nb2 = 1)) THEN
	SELECT idLibRecord INTO @srcIdLibRecord FROM vgr_librecord WHERE idRecord = src_record_id;
	SELECT idLibRecord,idType INTO @destIdLibRecord,@idType FROM vgr_librecord WHERE idRecord = dest_record_id;
	SELECT orderBy INTO @orderBy FROM vgr_librecord_type WHERE idType = @idType;

	open cur1;

	read_loop: LOOP
	  FETCH cur1 INTO membre_id;
	  IF done THEN
	    LEAVE read_loop;
	  END IF;

	  SELECT COUNT(idRecord) INTO @nb3 FROM vgr_record_membre WHERE idRecord = dest_record_id AND idMembre = membre_id;

	  IF (@nb3 = 0) THEN
	    	-- move librecord_membre
		UPDATE vgr_librecord_membre SET idLibRecord = @destIdLibRecord WHERE idLibRecord = @srcIdLibRecord AND idMembre = membre_id;
		-- move record_membre
		UPDATE vgr_record_membre SET idRecord = dest_record_id WHERE idRecord = src_record_id AND idMembre = membre_id;
	  ELSE
		SELECT `value` into @oldValue FROM vgr_librecord_membre WHERE idMembre = membre_id AND idLibRecord = @destIdLibRecord;
		SELECT `value` into @newValue FROM vgr_librecord_membre WHERE idMembre = membre_id AND idLibRecord = @srcIdLibRecord;

		-- change value
		IF (((@orderBy  = 'DESC') AND (@oldValue < @newValue)) OR ((@orderBy = 'ASC') AND (@oldValue > @newValue))) THEN
		   UPDATE vgr_librecord_membre SET `value` = @newValue WHERE idLibRecord = @destIdLibRecord AND idMembre = membre_id;
		END IF;
	  END IF;

	END LOOP;
	CLOSE cur1;

  END IF;

END&&
DELIMITER ;