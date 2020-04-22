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
        SET seq_string = LPAD(seq, 2, '0');
        SET label_fr = CONCAT(prefixe_fr, " ", seq_string);
        SET label_en = CONCAT(prefixe_en, " " , seq_string);

        -- ADD CHART
		INSERT INTO vgr_record (idGroupe, libRecord_fr, libRecord_en, dateCreation, dateModification)
		VALUES (group_id, label_fr, label_en, NOW(), NOW());
		INSERT INTO vgr_librecord (idRecord, idType, dateCreation)  VALUES (LAST_INSERT_ID(), type_id, NOW());

        SET seq = seq + 1;
    END WHILE;

END &&
DELIMITER ;

call insert_sequence_chart (1, 1, 1, 15, 2, 'test fr', 'test en');
