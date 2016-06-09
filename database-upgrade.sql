-- Migration script for video-games-records.com

RENAME TABLE vgr_jeu TO vgr_game;
RENAME TABLE vgr_groupe TO vgr_group;
RENAME TABLE vgr_record TO vgr_chart;
RENAME TABLE vgr_record_membre TO vgr_user_chart;
RENAME TABLE mv_membre_serie TO vgr_user_serie;
RENAME TABLE mv_membre_jeu TO vgr_user_game;
RENAME TABLE mv_membre_groupe TO vgr_user_group;
RENAME TABLE vgr_librecord TO vgr_chartlib;
RENAME TABLE vgr_librecord_type TO vgr_charttype;
RENAME TABLE vgr_librecord_membre TO vgr_user_chartlib;
RENAME TABLE vgr_perteposition TO vgr_lostposition;

ALTER TABLE `t_membre` CHANGE `idMembre` `idUser` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `vgr_game` CHANGE `idJeu` `idGame` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_game` CHANGE `libJeu_fr` `libGameFr` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_game` CHANGE `libJeu_en` `libGameEn` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_game` CHANGE `imageJeu` `picture` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_game` CHANGE `nbMembre` `nbUser` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `boolDLC` `boolDlc` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `statut` `status` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_game` DROP `imagePlateForme`;

ALTER TABLE `vgr_group` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_group` CHANGE `libGroupe_fr` `libGroupFr` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_group` CHANGE `libGroupe_en` `libGroupEn` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_group` CHANGE `idJeu` `idGame` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `boolDLC` `boolDlc` TINYINT(1) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `nbMembre` `nbUser` INT(11) NOT NULL;

ALTER TABLE `vgr_chart` CHANGE `idRecord` `idChart` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_chart` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `libRecord_fr` `libChartFr` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_chart` CHANGE `libRecord_en` `libChartEn` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `statut` `statusUser` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `statutTeam` `statusTeam` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

ALTER TABLE `vgr_chartlib` CHANGE `idLibRecord` `idLibChart` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_chartlib` CHANGE `idRecord` `idChart` INT(11) NOT NULL;

ALTER TABLE `vgr_charttype` CHANGE `lib_fr` `libFr` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_charttype` CHANGE `lib_en` `libEn` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_charttype` CHANGE `nomType` `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `vgr_user_chart` CHANGE `idMembre` `idUser` INT(11) NOT NULL;
ALTER TABLE `vgr_user_chart` CHANGE `idRecord` `idChart` INT(11) NOT NULL;
ALTER TABLE `vgr_user_chart` CHANGE `pointRecord` `pointChart` DOUBLE NOT NULL;

ALTER TABLE `vgr_user_chartlib` CHANGE `idMembre` `idUser` INT(11) NOT NULL;
ALTER TABLE `vgr_user_chartlib` CHANGE `idLibRecord` `idLibChart` INT(11) NOT NULL;

ALTER TABLE `vgr_user_game` CHANGE `idMembre` `idUser` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `idJeu` `idGame` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `rank` `rankPoint` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `pointRecordSansDLC` `pointChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `nbRecordProuve` `nbChartProven` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `nbRecordSansDLC` `nbChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `nbRecordProuveSansDLC` `nbChartProvenWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_user_game` CHANGE `pointJeu` `pointGame` INT(11) NOT NULL;

ALTER TABLE `vgr_user_group` CHANGE `idMembre` `idUser` INT(11) NOT NULL;
ALTER TABLE `vgr_user_group` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_user_group` CHANGE `rank` `rankPoint` INT(11) NOT NULL;
ALTER TABLE `vgr_user_group` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_user_group` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_user_group` CHANGE `nbRecordProuve` `nbChartProven` INT(11) NOT NULL;

ALTER TABLE `vgr_user_serie` CHANGE `idMembre` `idUser` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `pointRecordSansDLC` `pointChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `nbRecordProuve` `nbChartProven` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `nbRecordSansDLC` `nbChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `nbRecordProuveSansDLC` `nbChartProvenWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `pointJeu` `pointGame` INT(11) NOT NULL;
ALTER TABLE `vgr_user_serie` CHANGE `nbJeu` `nbGame` INT(11) NOT NULL;

ALTER TABLE `vgr_lostposition` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_lostposition` CHANGE `idRecord` `idChart` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_lostposition` CHANGE `oldPosition` `oldRank` INT(5) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_lostposition` CHANGE `newPosition` `newRank` INT(5) NOT NULL DEFAULT '0';

























