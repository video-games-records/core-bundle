CREATE TABLE vgr_player_chart_status_translation (
    id INT AUTO_INCREMENT NOT NULL,
    translatable_id INT DEFAULT NULL,
    name VARCHAR(255) NOT NULL,
    locale VARCHAR(255) NOT NULL,
    INDEX (translatable_id),
    UNIQUE INDEX country_translation_unique_translation (translatable_id, locale),
    PRIMARY KEY(id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

ALTER TABLE vgr_player_chart_status_translation ADD CONSTRAINT FK_PLAYER_CHART_STATUS FOREIGN KEY (translatable_id) REFERENCES vgr_player_chart_status (id) ON DELETE CASCADE;


INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (1, 'Normal', 'en');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (1, 'Normal', 'fr');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (2, 'Proof request sent', 'en');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (2, 'Demande de preuve envoyé', 'fr');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (3, 'Under investigation', 'en');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (3, 'Sous investigation', 'fr');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (4, 'Proof sent', 'en');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (4, 'Preuve envoyé', 'fr');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (5, 'Proof sent', 'en');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (5, 'Preuve envoyé', 'fr');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (6, 'Proven', 'en');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (6, 'Prouvé', 'fr');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (7, 'Non Prouvé', 'en');
INSERT INTO vgr_player_chart_status_translation (translatable_id, name, locale) VALUES (7, 'Not proven', 'fr');

ALTER TABLE `vgr_player_chart_status` CHANGE `classEtat` `class` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

UPDATE vgr_player_chart_status SET class='proof--none' WHERE id=1;
UPDATE vgr_player_chart_status SET class='proof--request-pending' WHERE id=2;
UPDATE vgr_player_chart_status SET class='proof--request-validated' WHERE id=3;
UPDATE vgr_player_chart_status SET class='proof--request-sent' WHERE id=4;
UPDATE vgr_player_chart_status SET class='proof--sent' WHERE id=5;
UPDATE vgr_player_chart_status SET class='proof--proved' WHERE id=6;
UPDATE vgr_player_chart_status SET class='proof--unproved' WHERE id=7;

ALTER TABLE `vgr_player_chart_status` DROP `label`;




