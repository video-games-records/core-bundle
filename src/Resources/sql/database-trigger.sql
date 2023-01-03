delimiter //
DROP TRIGGER IF EXISTS `userAfterUpdate`//
CREATE TRIGGER `userAfterUpdate` AFTER UPDATE ON `user`
FOR EACH ROW
BEGIN
    -- Player
    UPDATE vgr_player
    SET
        pseudo = NEW.username,
        avatar = NEW.avatar,
        slug = NEW.slug
    WHERE normandie_user_id = NEW.id;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrChartPlayerAfterUpdate`//
CREATE TRIGGER vgrChartPlayerAfterUpdate AFTER UPDATE ON vgr_player_chart
FOR EACH ROW
BEGIN
	DECLARE done INT DEFAULT FALSE;
	DECLARE chartlib_id_src INT;
	DECLARE chartlib_id_dest INT;
	DECLARE cur1 CURSOR FOR SELECT idLibChart FROM vgr_chartlib WHERE idChart = OLD.idChart ORDER BY idLibChart ASC;
	DECLARE cur2 CURSOR FOR SELECT idLibChart FROM vgr_chartlib WHERE idChart = NEW.idChart ORDER BY idLibChart ASC;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	-- MOVE SCORE
	IF OLD.idChart != NEW.idChart THEN

		open cur1;
		open cur2;

		read_loop: LOOP
			FETCH cur1 INTO chartlib_id_src;
			IF done THEN
				LEAVE read_loop;
			END IF;
			FETCH cur2 INTO chartlib_id_dest;

			UPDATE vgr_player_chartlib SET idLibChart = chartlib_id_dest WHERE idLibChart = chartlib_id_src AND idPlayerChart = NEW.id;
		END LOOP;

		CLOSE cur1;
		CLOSE cur2;

		UPDATE vgr_chart
		SET nbPost = (SELECT COUNT(id) FROM vgr_player_chart WHERE idChart = NEW.idChart AND idStatus != 7),
			statusPlayer = 'MAJ',
			statusTeam = 'MAJ'
		WHERE id = NEW.idChart;

		UPDATE vgr_chart
		SET nbPost = (SELECT COUNT(id) FROM vgr_player_chart WHERE idChart = OLD.idChart AND idStatus != 7),
			statusPlayer = 'MAJ',
			statusTeam = 'MAJ'
		WHERE id = OLD.idChart;

	END IF;

END //
delimiter ;
