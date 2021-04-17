DROP PROCEDURE IF EXISTS copy_group;
DELIMITER &&
CREATE PROCEDURE copy_group(IN group_id_src int, IN copy_libchart boolean)
BEGIN
 	DECLARE group_id_dest INT;


	DECLARE chart_id_src INT;
	DECLARE chart_id_dest INT;
	DECLARE chart_slug VARCHAR(255);
    DECLARE chart_lib_en VARCHAR(255);
    DECLARE chart_lib_fr VARCHAR(255);

	DECLARE done INT DEFAULT FALSE;

	DECLARE cur1 CURSOR FOR
	SELECT vgr_chart.id as idChart, libChartEn, libChartFr, vgr_chart.slug
	FROM vgr_chart
	WHERE vgr_chart.idGroup = group_id_src;

	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;


    -- GROUP
    INSERT INTO vgr_group (idGame, libGroupEn, libGroupFr, boolDlc, nbChart, slug, created_at, updated_at)
    SELECT idGame, CONCAT(libGroupEn, ' [COPY]'), CONCAT(libGroupFr, ' [COPY]'), boolDlc, nbChart, slug, NOW(), NOW()
    FROM vgr_group
    WHERE id = group_id_src;

    SET group_id_dest = LAST_INSERT_ID();

	open cur1;

	read_loop: LOOP
		FETCH cur1 INTO chart_id_src, chart_lib_en, chart_lib_fr, chart_slug;
		IF done THEN
			LEAVE read_loop;
		END IF;

        -- ADD CHART
		INSERT INTO vgr_chart (idGroup, libChartEn, libChartFr, slug, created_at, updated_at)
		VALUES (group_id_dest, chart_id_src, chart_lib_en, chart_slug, NOW(), NOW());
		SET chart_id_dest = LAST_INSERT_ID();

        -- LIBRECORD
        IF (copy_libchart = 1) THEN
            INSERT INTO vgr_chartlib (idChart, idType, name, created_at, updated_at) SELECT chart_id_dest, idType, name, NOW(), NOW() FROM vgr_chartlib WHERE idChart = chart_id_src;
        END IF;
    END LOOP;
	CLOSE cur1;

END &&
DELIMITER ;

