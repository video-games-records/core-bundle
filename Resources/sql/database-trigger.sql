-- User
delimiter //
DROP TRIGGER IF EXISTS `userAfterInsert`//
CREATE TRIGGER `userAfterInsert` AFTER INSERT ON `user`
FOR EACH ROW
BEGIN
    -- ROLE PLAYER
    INSERT INTO user_group (userId, groupId) VALUE (NEW.id, 2);
    -- Player
    INSERT INTO vgr_player (id, pseudo, normandie_user_id, slug) values (NEW.id, NEW.username, NEW.id, NEW.slug);
END //
delimiter ;

delimiter //
DROP TRIGGER IF EXISTS `userAfterUpdate`//
CREATE TRIGGER `userAfterUpdate` AFTER UPDATE ON `user`
FOR EACH ROW
BEGIN
    -- Player
    UPDATE vgr_player
    SET
        pseudo = NEW.username,
        avatar = NEW.avatar
    WHERE normandie_user_id = NEW.id;
END //
delimiter ;


-- PlayerChart
delimiter //
DROP TRIGGER IF EXISTS `vgrPlayerChartAfterInsert`//
CREATE TRIGGER `vgrPlayerChartAfterInsert` AFTER INSERT ON `vgr_player_chart`
FOR EACH ROW
BEGIN
    UPDATE vgr_chart
	  SET nbPost = (SELECT COUNT(idPlayer) FROM vgr_player_chart WHERE idChart = NEW.idChart AND idStatus != 7),
		statusPlayer = 'MAJ',
		statusTeam = 'MAJ'
	WHERE id = NEW.idChart;
	UPDATE vgr_player
	SET nbChart = (SELECT COUNT(idChart) FROM vgr_player_chart WHERE idPlayer = NEW.idPlayer)
	WHERE id = NEW.idPlayer;
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

	IF OLD.lastUpdate != NEW.lastUpdate THEN
		UPDATE vgr_chart
	    SET statusPlayer = 'MAJ',
	        statusTeam = 'MAJ'
	    WHERE id = OLD.idChart;
	END IF;
	IF (OLD.idStatus != NEW.idStatus AND (NEW.idStatus != 2 OR NEW.idStatus != 5)) THEN
		UPDATE vgr_chart
	    SET statusPlayer = 'MAJ'
	    WHERE id = OLD.idChart;
	END IF;
	IF (OLD.idStatus != NEW.idStatus AND (OLD.idStatus = 7 OR NEW.idStatus = 7) ) THEN
		UPDATE vgr_chart
		SET nbPost = (SELECT COUNT(idPlayer) FROM vgr_player_chart WHERE idChart = OLD.idChart AND idStatus != 7)
		WHERE id = OLD.idChart;
	END IF;

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


delimiter //
DROP TRIGGER IF EXISTS `vgrChartPlayerAfterDelete`//
CREATE TRIGGER `vgrChartPlayerAfterDelete` AFTER DELETE ON `vgr_player_chart`
FOR EACH ROW
BEGIN
    UPDATE vgr_chart
	SET nbPost = (SELECT COUNT(idPlayer) FROM vgr_player_chart WHERE idChart = OLD.idChart AND idStatus != 7),
	    statusPlayer = 'MAJ',
	    statusTeam = 'MAJ'
	WHERE id = OLD.idChart;
	UPDATE vgr_player
	SET nbChart = (SELECT COUNT(idChart) FROM vgr_player_chart WHERE idPlayer = OLD.idPlayer)
	WHERE id = OLD.idPlayer;
END //
delimiter ;


-- Chart
delimiter //
DROP TRIGGER IF EXISTS `vgrChartAfterInsert`//
CREATE TRIGGER vgrChartAfterInsert AFTER INSERT ON vgr_chart
FOR EACH ROW
UPDATE vgr_group
SET nbChart = (SELECT COUNT(id) FROM vgr_chart WHERE idGroup = NEW.idGroup)
WHERE id = NEW.idGroup //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrChartAfterUpdate`//
CREATE TRIGGER vgrChartAfterUpdate AFTER UPDATE ON vgr_chart
FOR EACH ROW
BEGIN
	IF OLD.nbPost != NEW.nbPost	THEN
		UPDATE vgr_group
		SET nbPost = (SELECT SUM(nbPost) FROM vgr_chart WHERE idGroup = NEW.idGroup),
		    nbPlayer = (SELECT COUNT(DISTINCT(a.idPlayer))
		    			FROM vgr_player_chart a INNER JOIN vgr_chart b ON a.idChart = b.id
		    			WHERE b.idGroup = NEW.idGroup)
		WHERE id = NEW.idGroup;
	END IF;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrChartAfterDelete`//
CREATE TRIGGER vgrChartAfterDelete AFTER DELETE ON vgr_chart
FOR EACH ROW
BEGIN
  UPDATE vgr_group
  SET nbChart = (SELECT COUNT(id) FROM vgr_chart WHERE idGroup = OLD.idGroup)
  WHERE id = OLD.idGroup;
END //
delimiter ;


-- Group
delimiter //
DROP TRIGGER IF EXISTS `vgrGroupAfterInsert`//
CREATE TRIGGER vgrGroupAfterInsert AFTER INSERT ON vgr_group
FOR EACH ROW
BEGIN
	IF (SELECT COUNT(id) FROM vgr_group WHERE idGame = NEW.idGame AND boolDLC = 1) > 0 THEN
		UPDATE vgr_game SET boolDLC=1 WHERE id = NEW.idGame;
	ELSE
		UPDATE vgr_game SET boolDLC=0 WHERE id = NEW.idGame;
	END IF;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrGroupAfterUpdate`//
CREATE TRIGGER vgrGroupAfterUpdate AFTER UPDATE ON vgr_group
FOR EACH ROW
BEGIN
	IF OLD.nbChart != NEW.nbChart	THEN
		UPDATE vgr_game
		SET nbChart = (SELECT SUM(nbChart) FROM vgr_group WHERE idGame = NEW.idGame)
		WHERE id = NEW.idGame;
	END IF;
	IF OLD.nbPost != NEW.nbPost	THEN
		UPDATE vgr_game
		SET nbPost = (SELECT SUM(nbPost) FROM vgr_group WHERE idGame = NEW.idGame)
		WHERE id = NEW.idGame;
	END IF;
	IF OLD.nbPlayer != NEW.nbPlayer THEN
		UPDATE vgr_game
		SET nbPlayer = (SELECT COUNT(DISTINCT(a.idPlayer))
		    			FROM vgr_player_chart a
		    			INNER JOIN vgr_chart b ON a.idChart = b.id
		    			INNER JOIN vgr_group c ON b.idGroup = c.id
		    			WHERE c.idGame = NEW.idGame)
		WHERE id = NEW.idGame;
	END IF;
	IF (SELECT COUNT(id) FROM vgr_group WHERE idGame = NEW.idGame AND boolDLC = 1) > 0 THEN
		UPDATE vgr_game SET boolDLC=1 WHERE id = NEW.idGame;
	ELSE
		UPDATE vgr_game SET boolDLC=0 WHERE id = NEW.idGame;
	END IF;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrGroupAfterDelete`//
CREATE TRIGGER vgrGroupAfterDelete AFTER DELETE ON vgr_group
FOR EACH ROW
BEGIN
	IF (SELECT COUNT(id) FROM vgr_group WHERE idGame = OLD.idGame AND boolDLC = 1) > 0 THEN
		UPDATE vgr_game SET boolDLC=1 WHERE id = OLD.idGame;
	ELSE
		UPDATE vgr_game SET boolDLC=0 WHERE id = OLD.idGame;
	END IF;
END //
delimiter ;


-- Player
delimiter //
DROP TRIGGER IF EXISTS `vgrPlayerAfterInsert`//
CREATE TRIGGER `vgrPlayerAfterInsert` AFTER INSERT ON `vgr_player`
    FOR EACH ROW
BEGIN
    -- BADGE INSCRIPTION
    INSERT INTO vgr_player_badge (idPlayer, idBadge) VALUES (NEW.id, 1);

END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrPlayerAfterUpdate`//
CREATE TRIGGER vgrPlayerAfterUpdate AFTER UPDATE ON vgr_player
FOR EACH ROW
BEGIN
  IF OLD.idTeam IS NULL AND NEW.idTeam IS NOT NULL THEN
    UPDATE vgr_chart
    SET statusTeam = 'MAJ'
    WHERE id IN (SELECT idChart FROM vgr_player_chart WHERE idPlayer = OLD.id);
  END IF;

  IF NEW.idTeam IS NULL AND OLD.idTeam	IS NOT NULL THEN
    UPDATE vgr_chart
    SET statusTeam = 'MAJ'
    WHERE id IN (SELECT idChart FROM vgr_player_chart WHERE idPlayer = OLD.id);
  END IF;
END //
delimiter ;


-- GamePlatform
delimiter //
DROP TRIGGER IF EXISTS `vgrGamePlatformAfterInsert`//
CREATE TRIGGER vgrGamePlatformAfterInsert AFTER INSERT ON vgr_game_platform
	FOR EACH ROW
	UPDATE vgr_game
	SET nbPlatform = (SELECT COUNT(idPlatform) FROM vgr_game_platform WHERE idGame = NEW.idGame)
	WHERE id = NEW.idGame //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrGamePlatformAfterDelete`//
CREATE TRIGGER vgrGamePlatformAfterDelete AFTER DELETE ON vgr_game_platform
	FOR EACH ROW
	UPDATE vgr_game
	SET nbPlatform = (SELECT COUNT(idPlatform) FROM vgr_game_platform WHERE idGame = OLD.idGame)
	WHERE id = OLD.idGame //
delimiter ;


-- VideoComment
delimiter //
DROP TRIGGER IF EXISTS `vgrVideoCommentAfterInsert`//
CREATE TRIGGER vgrVideoCommentAfterInsert AFTER INSERT ON vgr_video_comment
    FOR EACH ROW
BEGIN
    UPDATE vgr_video
    SET nbComment = (SELECT COUNT(id) FROM vgr_video_comment WHERE idVideo = NEW.idVideo)
    WHERE id = NEW.idVideo;
END //
delimiter ;

delimiter //
DROP TRIGGER IF EXISTS `vgrVideoCommentAfterDelete`//
CREATE TRIGGER vgrVideoCommentAfterDelete AFTER DELETE ON vgr_video_comment
    FOR EACH ROW
BEGIN
    UPDATE vgr_video
    SET nbComment = (SELECT COUNT(id) FROM vgr_video_comment WHERE idVideo = OLD.idVideo)
    WHERE id = OLD.idVideo;
END //
delimiter ;


