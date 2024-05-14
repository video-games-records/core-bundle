DROP PROCEDURE IF EXISTS insert_sequence_chart;
DELIMITER &&
CREATE PROCEDURE insert_sequence_chart(IN group_id INT, IN type_id INT, IN seq_begin INT, IN seq_end INT,IN nbChar INT, IN prefixe_fr VARCHAR(100), IN prefixe_en VARCHAR(100))
BEGIN
    DECLARE seq INT;
    DECLARE label_fr VARCHAR(100);
    DECLARE label_en VARCHAR(100);
    DECLARE seq_string VARCHAR(10);

    SET seq = seq_begin;

    WHILE seq <= seq_end DO
        SET seq_string = LPAD(seq, nbChar, '0');
        SET label_fr = TRIM(CONCAT(prefixe_fr, " ", seq_string));
        SET label_en = TRIM(CONCAT(prefixe_en, " ", seq_string));

        -- ADD CHART
		INSERT INTO vgr_chart (idGroup, libChartEn, libChartFr, created_at, updated_at, slug)
		VALUES (group_id, label_en, label_fr, NOW(), NOW(), get_slug(label_en));

		INSERT INTO vgr_chartlib (idChart, idType, created_at, updated_at)  VALUES (LAST_INSERT_ID(), type_id, NOW(), NOW());

        SET seq = seq + 1;
    END WHILE;

END &&
DELIMITER ;

    /*
group_id : ID du groupe
type_id : ID des type de record
seq_begin : Début de la séquence
seq_end : Fin de la séquence
nbChar : Nombre de caractère sur nombre (exemple 0001, 0002 donc 4)
prefixe_fr : label FR
prefixe_en : label EN
    */

-- Exemple
call insert_sequence_chart (18136, 51, 301, 999, 3, 'Level', 'Level');

