DROP PROCEDURE IF EXISTS copy_group;
DELIMITER &&
CREATE PROCEDURE copy_group(IN group_id_src int)
BEGIN
 	DECLARE group_id_dest INT;


	DECLARE chart_id_src INT;
	DECLARE chart_id_dest INT;
	DECLARE chart_slug VARCHAR(255);

	DECLARE done INT DEFAULT FALSE;

	DECLARE cur1 CURSOR FOR
	SELECT vgr_chart.id as idChart, vgr_chart.slug
	FROM vgr_chart
	WHERE vgr_chart.idGroup = group_id_src;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;


    -- GROUP
    INSERT INTO vgr_group (idGame, boolDlc, nbChart, slug, created_at, updated_at)
    SELECT idGame, boolDlc, nbChart, slug, NOW(), NOW()
    FROM vgr_group
    WHERE id = group_id_src;

    SET group_id_dest = LAST_INSERT_ID();

    -- GROUP TRANSLATION
    INSERT INTO vgr_group_translation (translatable_id, name, locale) SELECT group_id_dest, name, locale FROM vgr_group_translation WHERE translatable_id = group_id_src;
    UPDATE vgr_group_translation SET name = CONCAT(name, ' [COPY]') WHERE translatable_id = group_id_dest;

	open cur1;

	read_loop: LOOP
		FETCH cur1 INTO chart_id_src, chart_slug;
		IF done THEN
			LEAVE read_loop;
		END IF;

        -- ADD CHART
		INSERT INTO vgr_chart (idGroup, slug, created_at, updated_at)
		VALUES (group_id_dest, chart_slug, NOW(), NOW());
		SET chart_id_dest = LAST_INSERT_ID();

		-- CHART TRANSLATION
		INSERT INTO vgr_chart_translation (translatable_id, name, locale) SELECT chart_id_dest, name, locale FROM vgr_chart_translation WHERE translatable_id = chart_id_src;

        -- LIBRECORD
        INSERT INTO vgr_chartlib (idChart, idType, name, created_at, updated_at) SELECT chart_id_dest, idType, name, NOW(), NOW() FROM vgr_chartlib WHERE idChart = chart_id_src;

    END LOOP;
	CLOSE cur1;

END &&
DELIMITER ;

