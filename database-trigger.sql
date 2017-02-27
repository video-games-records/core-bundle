-- PlayerChart
delimiter //
DROP TRIGGER IF EXISTS `vgrPlayerChartAfterInsert`//
CREATE TRIGGER `vgrPlayerChartAfterInsert` AFTER INSERT ON `vgr_player_chart`
FOR EACH ROW
BEGIN
    UPDATE vgr_chart
	SET nbPost = (SELECT COUNT(idPlayer) FROM vgr_player_chart WHERE idChart = NEW.idChart AND idEtat != 7),
		statusUser = 'MAJ',
		statutTeam = 'MAJ'
	WHERE idChart = NEW.idChart;
	UPDATE vgr_player
	SET vgr_nbChart = (SELECT COUNT(idChart) FROM vgr_player_chart WHERE idPlayer = NEW.idPlayer)
	WHERE idPlayer = NEW.idPlayer;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrChartPlayerBeforeUpdate`//
CREATE TRIGGER vgrChartPlayerBeforeUpdate BEFORE UPDATE ON vgr_player_chart
FOR EACH ROW
BEGIN
	IF NEW.idStatus = 7 THEN
		SET NEW.pointChart = 0;
		SET NEW.rank = 0;
		SET NEW.isTopScore = 0;
	END IF;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrChartPlayerAfterUpdate`//
CREATE TRIGGER vgrChartPlayerAfterUpdate AFTER UPDATE ON vgr_player_chart
FOR EACH ROW
BEGIN
	IF OLD.dateModif != NEW.dateModif THEN
		UPDATE vgr_chart
	    SET statusUser = 'MAJ',
	        statusTeam = 'MAJ'
	    WHERE idChart = OLD.idChart;
	END IF;
	IF (OLD.idStatus != NEW.idStatus && (NEW.idStatus != 2 || NEW.idStatus != 5)) THEN
		UPDATE vgr_chart
	    SET statusUSer = 'MAJ'
	    WHERE idChart = OLD.idChart;
	END IF;
	IF (OLD.idStatus != NEW.idStatus && (OLD.idStatus = 7 || NEW.idStatus = 7) ) THEN
		UPDATE vgr_chart
		SET nbPost = (SELECT COUNT(idPlayer) FROM vgr_player_chart WHERE idChart = OLD.idChart AND idEtat != 7)
		WHERE idChart = OLD.idChart;
	END IF;

END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrChartPlayerAfterDelete`//
CREATE TRIGGER `vgrChartPlayerAfterDelete` AFTER DELETE ON `vgr_player_chart`
FOR EACH ROW
BEGIN
    UPDATE vgr_chart
	SET nbPost = (SELECT COUNT(idPlayer) FROM vgr_player_chart WHERE idChart = OLD.idChart AND idEtat != 7),
	    statusUser = 'MAJ',
	    statusTeam = 'MAJ'
	WHERE idChart = OLD.idChart;
	UPDATE vgr_player
	SET vgr_nbChart = (SELECT COUNT(idChart) FROM vgr_player_chart WHERE idPlayer = OLD.idPlayer)
	WHERE idPlayer = OLD.idPlayer;
END //
delimiter ;


-- Chart
delimiter //
DROP TRIGGER IF EXISTS `vgrChartAfterInsert`//
CREATE TRIGGER vgrChartAfterInsert AFTER INSERT ON vgr_chart
FOR EACH ROW
UPDATE vgr_groupe
SET nbChart = (SELECT COUNT(idChart) FROM vgr_chart WHERE idGroup = NEW.idGroup)
WHERE idGroup = NEW.idGroup //
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
		    			FROM vgr_player_chart a INNER JOIN vgr_chart b ON a.idChart = b.idChart
		    			WHERE b.idGroup = NEW.idGroup)
		WHERE idGroup = NEW.idGroup;
	END IF;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrChartAfterDelete`//
CREATE TRIGGER vgrChartAfterDelete AFTER DELETE ON vgr_chart
FOR EACH ROW
UPDATE vgr_groupe
SET nbChart = (SELECT COUNT(idChart) FROM vgr_chart WHERE idGroup = OLD.idGroup)
WHERE idGroup = OLD.idGroup //
delimiter ;


-- Group
delimiter //
DROP TRIGGER IF EXISTS `vgrGroupAfterInsert`//
CREATE TRIGGER vgrGroupAfterInsert AFTER INSERT ON vgr_group
FOR EACH ROW
BEGIN
	IF (SELECT COUNT(idGroup) FROM vgr_group WHERE idGame = NEW.idGame AND boolDLC = 1) > 0 THEN
		UPDATE vgr_game SET boolDLC=1 WHERE idGame = NEW.idGame;
	ELSE
		UPDATE vgr_game SET boolDLC=0 WHERE idGame = NEW.idGame;
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
		SET nbChart = (SELECT SUM(nbChart) FROM vgr_groupe WHERE idGame = NEW.idGame)
		WHERE idGame = NEW.idGame;
	END IF;
	IF OLD.nbPost != NEW.nbPost	THEN
		UPDATE vgr_game
		SET nbPost = (SELECT SUM(nbPost) FROM vgr_groupe WHERE idGame = NEW.idGame)
		WHERE idGame = NEW.idGame;
	END IF;
	IF OLD.nbPlayer != NEW.nbPlayer THEN
		UPDATE vgr_game
		SET nbPlayer = (SELECT COUNT(DISTINCT(a.idPlayer))
		    			FROM vgr_player_chart a INNER JOIN vgr_chart b ON a.idChart = b.idChart
		    			INNER JOIN vgr_group c ON b.idGroup = c.idGroup
		    			WHERE c.idGame = NEW.idGame)
		WHERE idGame = NEW.idGame;
	END IF;
	IF (SELECT COUNT(idGroup) FROM vgr_groue WHERE idGame = NEW.idGame AND boolDLC = 1) > 0 THEN
		UPDATE vgr_game SET boolDLC=1 WHERE idGame = NEW.idGame;
	ELSE
		UPDATE vgr_game SET boolDLC=0 WHERE idGame = NEW.idGame;
	END IF;
END //
delimiter ;


delimiter //
DROP TRIGGER IF EXISTS `vgrGroupAfterDelete`//
CREATE TRIGGER vgrGroupAfterDelete AFTER DELETE ON vgr_group
FOR EACH ROW
BEGIN
	IF (SELECT COUNT(idGroup) FROM vgr_group WHERE idGame = OLD.idGame AND boolDLC = 1) > 0 THEN
		UPDATE vgr_game SET boolDLC=1 WHERE idGame = OLD.idGame;
	ELSE
		UPDATE vgr_game SET boolDLC=0 WHERE idGame = OLD.idGame;
	END IF;
END //
delimiter ;
