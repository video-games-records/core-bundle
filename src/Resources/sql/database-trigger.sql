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


