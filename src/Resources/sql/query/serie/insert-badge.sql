DROP PROCEDURE IF EXISTS insert_serie_badge;
DELIMITER &&
CREATE PROCEDURE insert_serie_badge()
BEGIN
 	DECLARE badge_id INT;
 	DECLARE serie_id INT;

	DECLARE done INT DEFAULT FALSE;

	DECLARE cur1 CURSOR FOR
	SELECT vgr_serie.id as idSerie
	FROM vgr_serie
	WHERE idBadge IS NULL;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	open cur1;

	read_loop: LOOP
		FETCH cur1 INTO serie_id;
		IF done THEN
			LEAVE read_loop;
		END IF;

		-- Serie Badge
        INSERT INTO vgr_badge (type, picture, value, nbPlayer) VALUES ('Serie', 'default.gif', 0, 0);
        SET badge_id = LAST_INSERT_ID();
		UPDATE vgr_serie SET idBadge = badge_id WHERE id = serie_id;

    END LOOP;
	CLOSE cur1;

END &&
DELIMITER ;

