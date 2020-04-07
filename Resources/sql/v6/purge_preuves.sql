DROP PROCEDURE IF EXISTS purge_preuves;
DELIMITER &&
CREATE PROCEDURE purge_preuves()
BEGIN
	DECLARE preuve_id INT;
	DECLARE membre_id INT;
	DECLARE record_id INT;
	DECLARE nb INT;


        DECLARE cursor1 CURSOR FOR
	SELECT MAX(idPreuve) as id, idMembre, idRecord, COUNT(idPreuve) as nb
	FROM vgr_preuves
	GROUP BY idMembre, idRecord
	HAVING count(idPreuve) > 1;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;


	open cursor1;

	read_loop: LOOP
		FETCH cur1 INTO preuve_id, membre_id, record_id, nb;
		IF done THEN
			LEAVE read_loop;
		END IF;

		-- DELETE
		DELETE FROM vgr_preuves WHERE idMembre = membre_id AND idRecord = record_id AND idPreuve != preuve_id;

	END LOOP;
	CLOSE cur1;

END &&
DELIMITER ;
