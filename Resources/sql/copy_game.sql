DROP PROCEDURE IF EXISTS copy_game;
DELIMITER &&
CREATE PROCEDURE copy_game(IN game_id_src int)
BEGIN
 	DECLARE game_id_dest INT;
 	DECLARE badge_id INT;

	DECLARE group_id_src INT;
	DECLARE group_id_dest INT;
	DECLARE group_bool_dlc INT;
	DECLARE group_nb_chart INT;
	DECLARE group_slug VARCHAR(255);

	DECLARE chart_id_src INT;
	DECLARE chart_id_dest INT;
	DECLARE chart_slug VARCHAR(255);

	DECLARE group_id_local INT;
	DECLARE done INT DEFAULT FALSE;

	DECLARE cur1 CURSOR FOR
	SELECT vgr_group.id as idGroup, boolDlc, vgr_group.nbChart, vgr_group.slug, vgr_chart.id as idChart, vgr_chart.slug
	FROM vgr_group INNER JOIN vgr_chart ON vgr_group.id = vgr_chart.idGroup
	WHERE vgr_group.idGame = game_id_src;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	-- Master Badge
	INSERT INTO badge (type, picture, value, nbUser) SELECT b.type, b.picture, 0, 0 FROM badge b INNER JOIN vgr_game g ON b.id = g.idBadge WHERE g.id = game_id_src;
    SET badge_id = LAST_INSERT_ID();

    -- GAME
	INSERT INTO vgr_game (picture, status, etat, boolDlc, boolRanking, created_at, updated_at, idSerie, slug, idBadge)
	SELECT picture, 'INACTIF', etat, boolDlc, boolRanking, NOW(), NOW(), idSerie, slug, badge_id FROM vgr_game
	WHERE id = game_id_src;
	SET game_id_dest = LAST_INSERT_ID();
	SET group_id_local = 0;

	-- GAME TRANSLATION
	INSERT INTO vgr_game_translation (translatable_id, name, rules, locale) SELECT game_id_dest, name, rules, locale FROM vgr_game_translation WHERE translatable_id = game_id_src;
	UPDATE vgr_game_translation SET name = CONCAT(name, ' [COPY]') WHERE translatable_id = game_id_dest;

	-- PLATFORM
	INSERT INTO vgr_game_platform(idGame, idPlatform)  SELECT game_id_dest,idPlatform FROM vgr_game_platform WHERE idGame = game_id_src;

	open cur1;

	read_loop: LOOP
		FETCH cur1 INTO group_id_src, group_bool_dlc, group_nb_chart, group_slug, chart_id_src, chart_slug;
		IF done THEN
			LEAVE read_loop;
		END IF;

		-- ADD GROUP
		IF (group_id_local != group_id_src) THEN
			INSERT INTO vgr_group (idGame, boolDlc, nbChart, slug, created_at, updated_at)
			VALUES (game_id_dest, group_bool_dlc, group_nb_chart, group_slug, NOW(), NOW());

			SET group_id_local = group_id_src;
			SET group_id_dest = LAST_INSERT_ID();

			-- GROUP TRANSLATION
			INSERT INTO vgr_group_translation (translatable_id, name, locale) SELECT group_id_dest, name, locale FROM vgr_group_translation WHERE translatable_id = group_id_src;
		END IF;

        -- ADD CHART
		INSERT INTO vgr_chart (idGroup, slug, created_at, updated_at)
		VALUES (group_id_dest, chart_slug, NOW(), NOW());
		SET chart_id_dest = LAST_INSERT_ID();

		-- CHART TRANSLATION
		INSERT INTO vgr_chart_translation (translatable_id, name, locale) SELECT chart_id_dest, name, locale FROM vgr_chart_translation WHERE translatable_id = chart_id_src;

        -- LIbRECORD
        INSERT INTO vgr_chartlib (idChart, idType, name, created_at, updated_at) SELECT chart_id_dest, idType, name, NOW(), NOW() FROM vgr_chartlib WHERE idChart = chart_id_src;

    END LOOP;
	CLOSE cur1;

END &&
DELIMITER ;

