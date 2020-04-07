DROP PROCEDURE IF EXISTS copy_game;
DELIMITER &&
CREATE PROCEDURE copy_game(IN game_id_src int)
BEGIN
 	DECLARE game_id_dest INT;

	DECLARE group_id_src INT;
	DECLARE group_id_dest INT;
	DECLARE group_lib_fr VARCHAR(100);
	DECLARE group_lib_en VARCHAR(100);
	DECLARE group_bool_dlc INT;
	DECLARE group_nb_record INT;

	DECLARE chart_id_src INT;
	DECLARE chart_id_dest INT;
	DECLARE chart_lib_fr VARCHAR(100);
	DECLARE chart_lib_en VARCHAR(100);

	DECLARE group_id_local INT;
	DECLARE done INT DEFAULT FALSE;

	DECLARE cur1 CURSOR FOR
	SELECT vgr_groupe.idGroupe, libGroupe_fr, libGroupe_en, boolDlc, nbRecord, idRecord, libRecord_fr, libRecord_en
	FROM vgr_groupe INNER JOIN vgr_record ON vgr_groupe.idGroupe = vgr_record.idGroupe
	WHERE vgr_groupe.idJeu = game_id_src;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

	INSERT INTO vgr_jeu (libJeu_fr, libJeu_en, imageJeu, statut, etat, boolDlc, dateCreation, dateModification, idSerie, imagePlateForme)
	SELECT concat(libJeu_fr, ' [COPY]'), concat(libJeu_en, ' [COPY]'), imageJeu, 'INACTIF', etat, boolDlc, NOW(), NOW(), idSerie, imagePlateForme FROM vgr_jeu WHERE idJeu = game_id_src;

	SET game_id_dest = LAST_INSERT_ID();
	SET group_id_local = 0;

	-- ADD BADGE
	INSERT INTO t_badge (type, image, value, idJeu, nbMembre) SELECT type, image, 0, game_id_dest, 0 FROM t_badge WHERE idJeu = game_id_src AND type = 'Master';

	open cur1;

	read_loop: LOOP
		FETCH cur1 INTO group_id_src, group_lib_fr, group_lib_en, group_bool_dlc, group_nb_record, chart_id_src, chart_lib_fr, chart_lib_en;
		IF done THEN
			LEAVE read_loop;
		END IF;

		-- ADD GROUP
		IF (group_id_local != group_id_src) THEN
			INSERT INTO vgr_groupe (idJeu, libGroupe_fr, libGroupe_en, boolDlc, nbRecord, dateCreation, dateModification)
			VALUES (game_id_dest, group_lib_fr, group_lib_en, group_bool_dlc, group_nb_record, NOW(), NOW());

			SET group_id_local = group_id_src;
			SET group_id_dest = LAST_INSERT_ID();
		END IF;

		-- ADD CHART
		INSERT INTO vgr_record (idGroupe, libRecord_fr, libRecord_en, dateCreation, dateModification)
		VALUES (group_id_dest, chart_lib_fr, chart_lib_en, NOW(), NOW());

		SET chart_id_dest = LAST_INSERT_ID();

		INSERT INTO vgr_librecord (idRecord, idType, lib, dateCreation) SELECT chart_id_dest, idType, lib, NOW() FROM vgr_librecord WHERE idRecord = chart_id_src;

	END LOOP;
	CLOSE cur1;

END &&
DELIMITER ;
