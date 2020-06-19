-- Migration script for video-games-records.com
SET NAMES 'utf8';
SET CHARACTER SET utf8;

DROP TRIGGER IF EXISTS `vgrGroupeAfterDelete`;
DROP TRIGGER IF EXISTS `vgrGroupeAfterInsert`;
DROP TRIGGER IF EXISTS `vgrGroupeAfterUpdate`;
DROP TRIGGER IF EXISTS `vgrRecordAfterDelete`;
DROP TRIGGER IF EXISTS `vgrRecordAfterInsert`;
DROP TRIGGER IF EXISTS `vgrRecordAfterUpdate`;
DROP TRIGGER IF EXISTS `vgrRecordMembreAfterDelete`;
DROP TRIGGER IF EXISTS `vgrRecordMembreAfterInsert`;
DROP TRIGGER IF EXISTS `vgrRecordMembreAfterUpdate`;
DROP TRIGGER IF EXISTS `vgrRecordMembreBeforeUpdate`;
DROP TRIGGER IF EXISTS `tVgrJeuBeforeUpdate`;
DROP TRIGGER IF EXISTS `tForumTopicAfterDelete`;
DROP TRIGGER IF EXISTS `tForumTopicAfterInsert`;
DROP TRIGGER IF EXISTS `tForumTopicAfterUpdate`;
DROP TRIGGER IF EXISTS `tForumMessageAfterDelete`;
DROP TRIGGER IF EXISTS `tForumMessageAfterInsert`;
DROP TRIGGER IF EXISTS `tDonAfterDelete`;
DROP TRIGGER IF EXISTS `tDonAfterInsert`;
DROP TRIGGER IF EXISTS `tDonAfterUpdate`;

--
DROP TRIGGER IF EXISTS `vgrDemandepreuveAfterInsert`;
DROP TRIGGER IF EXISTS `tTeamDemandeAfterUpdate`;
DROP TRIGGER IF EXISTS `tMembreBeforeUpdate`;
DROP TRIGGER IF EXISTS `tMembreAfterUpdate`;
DROP TRIGGER IF EXISTS `tCommentaireAfterInsert`;
DROP TRIGGER IF EXISTS `tCommentaireAfterDelete`;
DROP TRIGGER IF EXISTS `mvTeamRecordAfterDelete`;


DROP TABLE IF EXISTS copy_vgr_groupe;
DROP TABLE IF EXISTS copy_vgr_record;
DROP TABLE IF EXISTS t_team_demande_old;
DROP TABLE IF EXISTS t_email_cron;
DROP TABLE IF EXISTS t_banniere;
DROP TABLE IF EXISTS t_fan;
DROP TABLE IF EXISTS t_newsletter;
DROP TABLE IF EXISTS t_variable;
DROP TABLE IF EXISTS t_poll_question;
DROP TABLE IF EXISTS t_poll_reponse;
DROP TABLE IF EXISTS t_poll_vote;
DROP TABLE IF EXISTS t_acl_deny;
DROP TABLE IF EXISTS vgr_editeur;
DROP TABLE IF EXISTS vgr_sanction_membre;
DROP TABLE IF EXISTS vgr_sanction;

DROP TABLE IF EXISTS t_concours_participant_reponse;
DROP TABLE IF EXISTS t_concours_participant;
DROP TABLE IF EXISTS t_concours_reponse;
DROP TABLE IF EXISTS t_concours_participant;
DROP TABLE IF EXISTS t_concours_question;
DROP TABLE IF EXISTS t_concours;
DROP TABLE IF EXISTS t_theme;
DROP TABLE IF EXISTS t_session2;

DROP TABLE IF EXISTS t_groupeutilisateur_membre;
DROP TABLE IF EXISTS t_forum_groupeutilisateur;
DROP TABLE IF EXISTS t_groupeutilisateur;



-- DROP VIEW
DROP VIEW IF EXISTS view_commentaire;
DROP VIEW IF EXISTS view_forum;
DROP VIEW IF EXISTS view_forum_message;
DROP VIEW IF EXISTS view_forum_home;
DROP VIEW IF EXISTS view_forum_topic;
DROP VIEW IF EXISTS view_groupe;
DROP VIEW IF EXISTS view_jeu;
DROP VIEW IF EXISTS view_librecord;
DROP VIEW IF EXISTS view_librecord_membre;
DROP VIEW IF EXISTS view_membre;
DROP VIEW IF EXISTS view_membre2;
DROP VIEW IF EXISTS view_membre3;
DROP VIEW IF EXISTS view_membre_cup;
DROP VIEW IF EXISTS view_pays;
DROP VIEW IF EXISTS view_record;
DROP VIEW IF EXISTS view_record_membre_last;
DROP VIEW IF EXISTS view_team_cup;
DROP VIEW IF EXISTS view_team_demande;
DROP VIEW IF EXISTS view_topscore;
DROP VIEW IF EXISTS view_topScore;
DROP VIEW IF EXISTS view_video;

-- TRUNCATE t_session;

RENAME TABLE vgr_jeu TO vgr_game;
RENAME TABLE vgr_groupe TO vgr_group;
RENAME TABLE vgr_record TO vgr_chart;
RENAME TABLE vgr_record_membre TO vgr_player_chart;
RENAME TABLE mv_membre_serie TO vgr_player_serie;
RENAME TABLE mv_membre_jeu TO vgr_player_game;
RENAME TABLE mv_membre_groupe TO vgr_player_group;
RENAME TABLE vgr_librecord TO vgr_chartlib;
RENAME TABLE vgr_librecord_type TO vgr_charttype;
RENAME TABLE vgr_librecord_membre TO vgr_player_chartlib;
RENAME TABLE vgr_perteposition TO vgr_lostposition;
RENAME TABLE vgr_plateforme TO vgr_platform;
RENAME TABLE vgr_jeu_plateforme TO vgr_game_platform;

RENAME TABLE vgr_etatrecord TO vgr_player_chart_status;
RENAME TABLE t_pays TO country;
RENAME TABLE t_email TO email;
RENAME TABLE t_article TO article;
RENAME TABLE t_membre TO vgr_player;
RENAME TABLE t_team TO vgr_team;
RENAME TABLE t_team_demande TO vgr_team_request;
RENAME TABLE mv_team_record TO vgr_team_chart;
RENAME TABLE mv_team_groupe TO vgr_team_group;
RENAME TABLE mv_team_jeu TO vgr_team_game;
RENAME TABLE t_badge TO badge;
RENAME TABLE t_badge_membre TO vgr_player_badge;
RENAME TABLE t_badge_team TO vgr_team_badge;
RENAME TABLE t_video TO vgr_video;
RENAME TABLE t_partenaire TO partner;
RENAME TABLE t_messageprive TO message;

RENAME TABLE vgr_demandepreuve TO vgr_proof_request;
RENAME TABLE vgr_preuves TO vgr_proof;

RENAME TABLE `t_ip` TO `ip`;
RENAME TABLE `t_membre_ip` TO `user_ip`;

ALTER TABLE `vgr_player` CHANGE `idMembre` `id` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `idPays` `idPays` INT(11) NULL DEFAULT NULL;
ALTER TABLE `email` CHANGE `idEmail` `emailId` INT(11) NOT NULL AUTO_INCREMENT;

--
-- Countries
--

-- New structure
ALTER TABLE `country` CHANGE `idPays` `id` int(11) NOT NULL AUTO_INCREMENT, ADD code_iso2 VARCHAR(2) NOT NULL, ADD code_iso3 VARCHAR(3) NOT NULL, ADD code_iso_numeric INT NOT NULL;
ALTER TABLE country CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;
CREATE TABLE country_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_A1FE6FA42C2AC5D3 (translatable_id), UNIQUE INDEX country_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE country_translation ADD CONSTRAINT FK_A1FE6FA42C2AC5D3 FOREIGN KEY (translatable_id) REFERENCES country (id) ON DELETE CASCADE;

-- Temporary base - official data
CREATE TABLE country_code (id INT AUTO_INCREMENT NOT NULL, liben VARCHAR(255) NOT NULL, iso2 VARCHAR(2) NOT NULL, iso3 VARCHAR(3) NOT NULL, isoN int NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
INSERT INTO country_code (liben, iso2, iso3, isoN) VALUES ("Afghanistan","AF","AFG","4"), ("Albania","AL","ALB","8"), ("Antarctica","AQ","ATA","10"), ("Algeria","DZ","DZA","12"), ("American Samoa","AS","ASM","16"), ("Andorra","AD","AND","20"), ("Angola","AO","AGO","24"), ("Antigua and Barbuda","AG","ATG","28"), ("Azerbaijan","AZ","AZE","31"), ("Argentina","AR","ARG","32"), ("Australia","AU","AUS","36"), ("Austria","AT","AUT","40"), ("Bahamas","BS","BHS","44"), ("Bahrain","BH","BHR","48"), ("Bangladesh","BD","BGD","50"), ("Armenia","AM","ARM","51"), ("Barbados","BB","BRB","52"), ("Belgium","BE","BEL","56"), ("Bermuda","BM","BMU","60"), ("Bhutan","BT","BTN","64"), ("Bolivia","BO","BOL","68"), ("Bosnia and Herzegovina","BA","BIH","70"), ("Botswana","BW","BWA","72"), ("Bouvet Island","BV","BVT","74"), ("Brazil","BR","BRA","76"), ("Belize","BZ","BLZ","84"), ("British Indian Ocean Territory","IO","IOT","86"), ("Solomon Islands","SB","SLB","90"), ("British Virgin Islands","VG","VGB","92"), ("Brunei Darussalam","BN","BRN","96"), ("Bulgaria","BG","BGR","100"), ("Myanmar","MM","MMR","104"), ("Burundi","BI","BDI","108"), ("Belarus","BY","BLR","112"), ("Cambodia","KH","KHM","116"), ("Cameroon","CM","CMR","120"), ("Canada","CA","CAN","124"), ("Cape Verde","CV","CPV","132"), ("Cayman Islands","KY","CYM","136"), ("Central African Republic","CF","CAF","140"), ("Sri Lanka","LK","LKA","144"), ("Chad","TD","TCD","148"), ("Chile","CL","CHL","152"), ("China","CN","CHN","156"), ("Taiwan, Republic of China","TW","TWN","158"), ("Christmas Island","CX","CXR","162"), ("Cocos (Keeling) Islands","CC","CCK","166"), ("Colombia","CO","COL","170"), ("Comoros","KM","COM","174"), ("Mayotte","YT","MYT","175"), ("Congo (Brazzaville)","CG","COG","178"), ("Congo, Democratic Republic of the","CD","COD","180"), ("Cook Islands","CK","COK","184"), ("Costa Rica","CR","CRI","188"), ("Croatia","HR","HRV","191"), ("Cuba","CU","CUB","192"), ("Cyprus","CY","CYP","196"), ("Czech Republic","CZ","CZE","203"), ("Benin","BJ","BEN","204"), ("Denmark","DK","DNK","208"), ("Dominica","DM","DMA","212"), ("Dominican Republic","DO","DOM","214"), ("Ecuador","EC","ECU","218"), ("El Salvador","SV","SLV","222"), ("Equatorial Guinea","GQ","GNQ","226"), ("Ethiopia","ET","ETH","231"), ("Eritrea","ER","ERI","232"), ("Estonia","EE","EST","233"), ("Faroe Islands","FO","FRO","234"), ("Falkland Islands (Malvinas)","FK","FLK","238"), ("South Georgia and the South Sandwich Islands","GS","SGS","239"), ("Fiji","FJ","FJI","242"), ("Finland","FI","FIN","246"), ("Aland Islands","AX","ALA","248"), ("France","FR","FRA","250"), ("French Guiana","GF","GUF","254"), ("French Polynesia","PF","PYF","258"), ("French Southern Territories","TF","ATF","260"), ("Djibouti","DJ","DJI","262"), ("Gabon","GA","GAB","266"), ("Georgia","GE","GEO","268"), ("Gambia","GM","GMB","270"), ("Palestinian Territory","PS","PSE","275"), ("Germany","DE","DEU","276"), ("Ghana","GH","GHA","288"), ("Gibraltar","GI","GIB","292"), ("Kiribati","KI","KIR","296"), ("Greece","GR","GRC","300"), ("Greenland","GL","GRL","304"), ("Grenada","GD","GRD","308"), ("Guadeloupe","GP","GLP","312"), ("Guam","GU","GUM","316"), ("Guatemala","GT","GTM","320"), ("Guinea","GN","GIN","324"), ("Guyana","GY","GUY","328"), ("Haiti","HT","HTI","332"), ("Heard Island and Mcdonald Islands","HM","HMD","334"), ("Holy See (Vatican City State)","VA","VAT","336"), ("Honduras","HN","HND","340"), ("Hong Kong","HK","HKG","344"), ("Hungary","HU","HUN","348"), ("Iceland","IS","ISL","352"), ("India","IN","IND","356"), ("Indonesia","ID","IDN","360"), ("Iran, Islamic Republic of","IR","IRN","364"), ("Iraq","IQ","IRQ","368"), ("Ireland","IE","IRL","372"), ("Israel","IL","ISR","376"), ("Italy","IT","ITA","380"), ("Côte d'Ivoire","CI","CIV","384"), ("Jamaica","JM","JAM","388"), ("Japan","JP","JPN","392"), ("Kazakhstan","KZ","KAZ","398"), ("Jordan","JO","JOR","400"), ("Kenya","KE","KEN","404"), ("Korea, Democratic People's Republic of","KP","PRK","408"), ("Korea, Republic of","KR","KOR","410"), ("Kuwait","KW","KWT","414"), ("Kyrgyzstan","KG","KGZ","417"), ("Lao PDR","LA","LAO","418"), ("Lebanon","LB","LBN","422"), ("Lesotho","LS","LSO","426"), ("Latvia","LV","LVA","428"), ("Liberia","LR","LBR","430"), ("Libya","LY","LBY","434"), ("Liechtenstein","LI","LIE","438"), ("Lithuania","LT","LTU","440"), ("Luxembourg","LU","LUX","442"), ("Macao","MO","MAC","446"), ("Madagascar","MG","MDG","450"), ("Malawi","MW","MWI","454"), ("Malaysia","MY","MYS","458"), ("Maldives","MV","MDV","462"), ("Mali","ML","MLI","466"), ("Malta","MT","MLT","470"), ("Martinique","MQ","MTQ","474"), ("Mauritania","MR","MRT","478"), ("Mauritius","MU","MUS","480"), ("Mexico","MX","MEX","484"), ("Monaco","MC","MCO","492"), ("Mongolia","MN","MNG","496"), ("Moldova","MD","MDA","498"), ("Montenegro","ME","MNE","499"), ("Montserrat","MS","MSR","500"), ("Morocco","MA","MAR","504"), ("Mozambique","MZ","MOZ","508"), ("Oman","OM","OMN","512"), ("Namibia","NA","NAM","516"), ("Nauru","NR","NRU","520"), ("Nepal","NP","NPL","524"), ("Netherlands","NL","NLD","528"), ("Netherlands Antilles","AN","ANT","530"), ("Aruba","AW","ABW","533"), ("New Caledonia","NC","NCL","540"), ("Vanuatu","VU","VUT","548"), ("New Zealand","NZ","NZL","554"), ("Nicaragua","NI","NIC","558"), ("Niger","NE","NER","562"), ("Nigeria","NG","NGA","566"), ("Niue","NU","NIU","570"), ("Norfolk Island","NF","NFK","574"), ("Norway","NO","NOR","578"), ("Northern Mariana Islands","MP","MNP","580"), ("United States Minor Outlying Islands","UM","UMI","581"), ("Micronesia, Federated States of","FM","FSM","583"), ("Marshall Islands","MH","MHL","584"), ("Palau","PW","PLW","585"), ("Pakistan","PK","PAK","586"), ("Panama","PA","PAN","591"), ("Papua New Guinea","PG","PNG","598"), ("Paraguay","PY","PRY","600"), ("Peru","PE","PER","604"), ("Philippines","PH","PHL","608"), ("Pitcairn","PN","PCN","612"), ("Poland","PL","POL","616"), ("Portugal","PT","PRT","620"), ("Guinea-Bissau","GW","GNB","624"), ("Timor-Leste","TL","TLS","626"), ("Puerto Rico","PR","PRI","630"), ("Qatar","QA","QAT","634"), ("Réunion","RE","REU","638"), ("Romania","RO","ROU","642"), ("Russian Federation","RU","RUS","643"), ("Rwanda","RW","RWA","646"), ("Saint-Barthélemy","BL","BLM","652"), ("Saint Helena","SH","SHN","654"), ("Saint Kitts and Nevis","KN","KNA","659"), ("Anguilla","AI","AIA","660"), ("Saint Lucia","LC","LCA","662"), ("Saint-Martin","MF","MAF","663"), ("Saint Pierre and Miquelon","PM","SPM","666"), ("Saint Vincent and Grenadines","VC","VCT","670"), ("San Marino","SM","SMR","674"), ("Sao Tome and Principe","ST","STP","678"), ("Saudi Arabia","SA","SAU","682"), ("Senegal","SN","SEN","686"), ("Serbia","RS","SRB","688"), ("Seychelles","SC","SYC","690"), ("Sierra Leone","SL","SLE","694"), ("Singapore","SG","SGP","702"), ("Slovakia","SK","SVK","703"), ("Viet Nam","VN","VNM","704"), ("Slovenia","SI","SVN","705"), ("Somalia","SO","SOM","706"), ("South Africa","ZA","ZAF","710"), ("Zimbabwe","ZW","ZWE","716"), ("Spain","ES","ESP","724"), ("South Sudan","SS","SSD","728"), ("Western Sahara","EH","ESH","732"), ("Sudan","SD","SDN","736"), ("Suriname","SR","SUR","740"), ("Svalbard and Jan Mayen Islands","SJ","SJM","744"), ("Swaziland","SZ","SWZ","748"), ("Sweden","SE","SWE","752"), ("Switzerland","CH","CHE","756"), ("Syrian Arab Republic","SY","SYR","760"), ("Tajikistan","TJ","TJK","762"), ("Thailand","TH","THA","764"), ("Togo","TG","TGO","768"), ("Tokelau","TK","TKL","772"), ("Tonga","TO","TON","776"), ("Trinidad and Tobago","TT","TTO","780"), ("United Arab Emirates","AE","ARE","784"), ("Tunisia","TN","TUN","788"), ("Turkey","TR","TUR","792"), ("Turkmenistan","TM","TKM","795"), ("Turks and Caicos Islands","TC","TCA","796"), ("Tuvalu","TV","TUV","798"), ("Uganda","UG","UGA","800"), ("Ukraine","UA","UKR","804"), ("Macedonia, Republic of","MK","MKD","807"), ("Egypt","EG","EGY","818"), ("United Kingdom","GB","GBR","826"), ("Guernsey","GG","GGY","831"), ("Jersey","JE","JEY","832"), ("Isle of Man","IM","IMN","833"), ("Tanzania, United Republic of","TZ","TZA","834"), ("United States of America","US","USA","840"), ("Virgin Islands, US","VI","VIR","850"), ("Burkina Faso","BF","BFA","854"), ("Uruguay","UY","URY","858"), ("Uzbekistan","UZ","UZB","860"), ("Venezuela","VE","VEN","862"), ("Wallis and Futuna Islands","WF","WLF","876"), ("Samoa","WS","WSM","882"), ("Yemen","YE","YEM","887"), ("Zambia","ZM","ZMB","894");

-- Mise à jour des données actuelles
UPDATE country SET code_iso2 = UCASE(LEFT(codeIso, 2));
UPDATE country c
JOIN country_code cc ON c.code_iso2 = cc.iso2
SET c.code_iso3 = cc.iso3, c.code_iso_numeric = cc.isoN, c.libPays_en = cc.liben;

-- Suppression des pays n'existant pas
UPDATE vgr_player SET idPays = 191 WHERE idPays IN (2, 92);
UPDATE vgr_player SET idPays = NULL WHERE idPays IN (56, 182, 186, 246, 239);
DELETE FROM country WHERE id IN (2, 56, 182, 186, 246, 239);

-- Transfert des données
INSERT INTO country_translation (translatable_id, name, locale) SELECT id, libPays_fr, 'fr' FROM country;
INSERT INTO country_translation (translatable_id, name, locale) SELECT id, libPays_en, 'en' FROM country;
ALTER TABLE country DROP libPays_fr, DROP libPays_en, DROP classPays, DROP codeIso;

-- Suppression Temporary base
DROP TABLE country_code;

ALTER TABLE `country` ADD `idBadge` INT NULL AFTER `code_iso_numeric`;
ALTER TABLE `country` ADD UNIQUE `idxBadge` (`idBadge`);

--
-- VGR Part
--

-- Series
ALTER TABLE vgr_serie CHANGE idSerie id INT AUTO_INCREMENT NOT NULL;
CREATE TABLE vgr_serie_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_B355773C2C2AC5D3 (translatable_id), UNIQUE INDEX serie_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE vgr_serie ADD slug VARCHAR(255) DEFAULT NULL;
UPDATE `vgr_serie` SET
    slug = lower(libSerie),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');
-- Transfert des données
INSERT INTO vgr_serie_translation (translatable_id, name, locale) SELECT id, libSerie, 'fr' FROM vgr_serie;
INSERT INTO vgr_serie_translation (translatable_id, name, locale) SELECT id, libSerie, 'en' FROM vgr_serie;
ALTER TABLE vgr_serie DROP libSerie;

-- Games
CREATE TABLE vgr_game_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL,rules text NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_6A3C076D2C2AC5D3 (translatable_id), UNIQUE INDEX game_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE `vgr_game` CHANGE `idJeu` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_game` CHANGE `imageJeu` `picture` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_game` CHANGE `nbMembre` `nbPlayer` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `boolDLC` `boolDlc` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `statut` `status` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_game` ADD `boolRanking` TINYINT(1) NOT NULL DEFAULT '1' AFTER `boolDlc`;
ALTER TABLE `vgr_game` ADD `boolMaj` TINYINT(1) NOT NULL DEFAULT '0' AFTER `boolRanking`;
ALTER TABLE `vgr_game` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_game` CHANGE dateModification updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_game` CHANGE `dateActivation` `published_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `vgr_game` DROP `imagePlateForme`;
ALTER TABLE `vgr_game` ADD `nbTeam` INT NOT NULL DEFAULT '0' AFTER `nbPlayer`;
ALTER TABLE `vgr_game` ADD slug VARCHAR(255) DEFAULT NULL;
ALTER TABLE `vgr_game` add `nbPlatform` INT(11) NOT NULL DEFAULT '0';
UPDATE `vgr_game` SET
    slug = lower(libJeu_en),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');
-- Transfert des données
INSERT INTO vgr_game_translation (translatable_id, name, locale) SELECT id, libJeu_fr, 'fr' FROM vgr_game;
INSERT INTO vgr_game_translation (translatable_id, name, locale) SELECT id, libJeu_en, 'en' FROM vgr_game;
ALTER TABLE vgr_game DROP libJeu_fr, DROP libJeu_en;


-- Groups
CREATE TABLE vgr_group_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_6A3C076D2C2AC5D3 (translatable_id), UNIQUE INDEX game_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE `vgr_group` CHANGE `idGroupe` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_group` CHANGE `idJeu` `idGame` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `boolDLC` `boolDlc` TINYINT(1) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `nbMembre` `nbPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE nbPost nbPost INT NOT NULL;
ALTER TABLE `vgr_group` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_group` CHANGE dateModification updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_group` ADD slug VARCHAR(255) DEFAULT NULL;
UPDATE `vgr_group` SET
    slug = lower(libGroupe_en),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');
INSERT INTO vgr_group_translation (translatable_id, name, locale) SELECT id, libGroupe_fr, 'fr' FROM vgr_group;
INSERT INTO vgr_group_translation (translatable_id, name, locale) SELECT id, libGroupe_en, 'en' FROM vgr_group;
ALTER TABLE vgr_group DROP libGroupe_fr, DROP libGroupe_en;

-- Charts
CREATE TABLE vgr_chart_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_6A3C076D2C2AC5D3 (translatable_id), UNIQUE INDEX game_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE `vgr_chart` CHANGE `idRecord` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_chart` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `statut` `statusPlayer` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `statutTeam` `statusTeam` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_chart` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_chart` CHANGE dateModification updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_chart` ADD slug VARCHAR(255) DEFAULT NULL;
UPDATE `vgr_chart` SET
    slug = lower(libRecord_en),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');
ALTER TABLE `vgr_chart` CHANGE statusPlayer statusPlayer VARCHAR(255) NOT NULL, CHANGE statusTeam statusTeam VARCHAR(255) NOT NULL, CHANGE nbPost nbPost INT NOT NULL;
INSERT INTO vgr_chart_translation (translatable_id, name, locale) SELECT id, libRecord_fr, 'fr' FROM vgr_chart;
INSERT INTO vgr_chart_translation (translatable_id, name, locale) SELECT id, libRecord_en, 'en' FROM vgr_chart;
ALTER TABLE vgr_chart DROP libRecord_fr, DROP libRecord_en;

-- Chart lib
ALTER TABLE `vgr_chartlib` CHANGE `idLibRecord` `idLibChart` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_chartlib` CHANGE `idRecord` `idChart` INT(11) NOT NULL;
ALTER TABLE `vgr_chartlib` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_chartlib` ADD updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_chartlib` CHANGE lib name VARCHAR(100) DEFAULT NULL;
ALTER TABLE `vgr_chartlib` CHANGE idChart idChart INT DEFAULT NULL, CHANGE idType idType INT DEFAULT NULL;

-- Chart type
ALTER TABLE `vgr_charttype` CHANGE `lib_fr` `libFr` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_charttype` CHANGE `lib_en` `libEn` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_charttype` CHANGE `nomType` `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

ALTER TABLE `vgr_player_chart` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_player_chart` CHANGE `idRecord` `idChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_chart` CHANGE `pointRecord` `pointChart` DOUBLE NOT NULL;
ALTER TABLE `vgr_player_chart` CHANGE `idEtat` `idStatus` INT(11) NOT NULL;
ALTER TABLE `vgr_player_chart` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_player_chart` CHANGE dateModification updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_player_chart` CHANGE rank rank INT NULL, CHANGE nbEqual nbEqual INT NOT NULL, CHANGE isTopScore isTopScore TINYINT(1) NOT NULL;
ALTER TABLE `vgr_player_chart` DROP PRIMARY KEY;
ALTER TABLE `vgr_player_chart` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE `vgr_player_chart` ADD UNIQUE( `idChart`, `idPlayer`);
ALTER TABLE `vgr_player_chart` ADD `dateInvestigation` DATE NULL AFTER `isTopScore`;
ALTER TABLE `vgr_player_chart` ADD `idPlatform` INT NULL;
ALTER TABLE `vgr_player_chart` CHANGE `dateModif` `lastUpdate` DATETIME NOT NULL;
ALTER TABLE `vgr_player_chart` ADD INDEX `idxPlayerLastUpdate` (`idPlayer`, `lastUpdate`);


ALTER TABLE `vgr_player_chartlib` ADD `idPlayerChart` INT NULL;
ALTER TABLE `vgr_player_chartlib` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_player_chartlib` CHANGE `idLibRecord` `idLibChart` INT(11) NOT NULL;

-- UPDATE vgr_player_chartlib.idPlayerChart
UPDATE  vgr_player_chart, vgr_chartlib,vgr_player_chartlib
SET vgr_player_chartlib.idPlayerChart = vgr_player_chart.id
WHERE vgr_player_chart.idChart = vgr_chartlib.idChart
AND vgr_chartlib.idLibChart = vgr_player_chartlib.idLibChart
AND vgr_player_chartlib.idPlayer = vgr_player_chart.idPlayer;
DELETE FROM vgr_player_chartlib WHERE idPlayerChart IS NULL;
ALTER TABLE `vgr_player_chartlib` CHANGE `idPlayerChart` `idPlayerChart` INT(11) NOT NULL;

ALTER TABLE `vgr_player_chartlib` DROP PRIMARY KEY;
ALTER TABLE `vgr_player_chartlib` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE vgr_player_chartlib DROP FOREIGN KEY vgr_player_chartlib_ibfk_1;
ALTER TABLE `vgr_player_chartlib` DROP `idPlayer`;
ALTER TABLE  `vgr_player_chartlib` ADD UNIQUE `idxUniq` (`idLibChart`, `idPlayerChart`);




ALTER TABLE `vgr_player_chartlib` ADD INDEX(`idPlayerChart`);
ALTER TABLE `vgr_player_chartlib` ADD FOREIGN KEY (`idPlayerChart`) REFERENCES `vgr_player_chart`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;


ALTER TABLE `vgr_player_game` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `idJeu` `idGame` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `rank` `rankPoint` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `pointRecordSansDLC` `pointChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `nbRecordProuve` `nbChartProven` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `nbRecordSansDLC` `nbChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `nbRecordProuveSansDLC` `nbChartProvenWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `pointJeu` `pointGame` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` ADD `nbEqual` INT NOT NULL DEFAULT '1';
ALTER TABLE `vgr_player_game` CHANGE `rank0` `chartRank0` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `rank1` `chartRank1` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `rank2` `chartRank2` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `rank3` `chartRank3` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `rank4` `chartRank4` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `rank5` `chartRank5` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` CHANGE `rankPoint` `rankPointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_game` ADD `lastUpdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `nbEqual`;

ALTER TABLE `vgr_player_group` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank` `rankPoint` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `nbRecordProuve` `nbChartProven` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank0` `chartRank0` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank1` `chartRank1` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank2` `chartRank2` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank3` `chartRank3` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank4` `chartRank4` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank5` `chartRank5` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rankPoint` `rankPointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` ADD `lastUpdate` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `nbChartProven`;

ALTER TABLE `vgr_player_serie` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `pointRecordSansDLC` `pointChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `nbRecordProuve` `nbChartProven` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `nbRecordSansDLC` `nbChartWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `nbRecordProuveSansDLC` `nbChartProvenWithoutDlc` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `pointJeu` `pointGame` INT(11) NOT NULL;
ALTER TABLE `vgr_player_serie` CHANGE `nbJeu` `nbGame` INT(11) NOT NULL;

ALTER TABLE `vgr_lostposition` CHANGE `idMembre` `idPlayer` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_lostposition` CHANGE `idRecord` `idChart` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_lostposition` CHANGE `oldPosition` `oldRank` INT(5) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_lostposition` CHANGE `newPosition` `newRank` INT(5) NOT NULL DEFAULT '0';

-- Platforms
ALTER TABLE `vgr_platform` CHANGE `idPlateforme` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_platform` CHANGE `libPlateforme` `libPlatform` VARCHAR(50) NOT NULL;
ALTER TABLE `vgr_platform` CHANGE `statut` `status` ENUM('ACTIF','INACTIF') NOT NULL DEFAULT 'INACTIF';
ALTER TABLE `vgr_platform` CHANGE `image` `picture` VARCHAR(30) NOT NULL DEFAULT '';
ALTER TABLE `vgr_platform` CHANGE `classPlateforme` `class` VARCHAR(30) NOT NULL;

ALTER TABLE `vgr_game_platform` CHANGE `idJeu` `idGame` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game_platform` CHANGE `idPlateForme` `idPlatform` INT(11) NOT NULL DEFAULT '0';

UPDATE vgr_game a
SET nbPlatform = (SELECT COUNT(idGame) FROM vgr_game_platform WHERE idGame = a.id);


ALTER TABLE `vgr_player_chart_status` CHANGE `idEtat` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_player_chart_status` CHANGE `libEtat` `label` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- Team
ALTER TABLE `vgr_team` CHANGE `idTeam` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_team` CHANGE `statut` `status` ENUM('OPEN','CLOSED','OPENED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'OPENED';
UPDATE vgr_team SET status = 'OPENED' WHERE status = 'OPEN';
ALTER TABLE `vgr_team` CHANGE `status` `status` ENUM('CLOSED','OPENED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'OPENED';
ALTER TABLE `vgr_team` CHANGE `nbMembre` `nbPlayer` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_team` CHANGE `vgr_pointRecord` `pointChart` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_team` CHANGE `vgr_pointBadge` `pointBadge` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_team` CHANGE `vgr_rank0` `chartRank0` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank1` `chartRank1` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank2` `chartRank2` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank3` `chartRank3` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_point` `rankPointChart` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_medal` `rankMedal` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_badge` `rankBadge` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_cup` `rankCup` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank0` `gameRank0` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank1` `gameRank1` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank2` `gameRank2` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank3` `gameRank3` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_nbMasterBadge` `nbMasterBadge` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_team` CHANGE `vgr_pointJeu` `pointGame` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_team` CHANGE `vgr_rank_pointJeu` `rankPointGame` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `vgr_team` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;
ALTER TABLE `vgr_team` ADD slug VARCHAR(255) DEFAULT NULL;
UPDATE `vgr_team` SET
    slug = lower(libTeam),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');

ALTER TABLE `vgr_team_chart` CHANGE `idRecord` `idChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_chart` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_chart` CHANGE `rank` `rankPointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_chart` CHANGE `rank0` `chartRank0` INT(11) NOT NULL;
ALTER TABLE `vgr_team_chart` CHANGE `rank1` `chartRank1` INT(11) NOT NULL;
ALTER TABLE `vgr_team_chart` CHANGE `rank2` `chartRank2` INT(11) NOT NULL;
ALTER TABLE `vgr_team_chart` CHANGE `rank3` `chartRank3` INT(11) NOT NULL;


ALTER TABLE `vgr_team_group` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` CHANGE `rank` `rankPointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` ADD `rankMedal` INT NOT NULL AFTER `rankPointChart`;
ALTER TABLE `vgr_team_group` CHANGE `rank0` `chartRank0` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` CHANGE `rank1` `chartRank1` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` CHANGE `rank2` `chartRank2` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` CHANGE `rank3` `chartRank3` INT(11) NOT NULL;


ALTER TABLE `vgr_team_game` CHANGE `idJeu` `idGame` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `pointJeu` `pointGame` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `rank` `rankPointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` ADD `rankMedal` INT NOT NULL AFTER `rankPointChart`;
ALTER TABLE `vgr_team_game` ADD `nbEqual` INT NOT NULL DEFAULT '1';
ALTER TABLE `vgr_team_game` CHANGE `rank0` `chartRank0` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `rank1` `chartRank1` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `rank2` `chartRank2` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `rank3` `chartRank3` INT(11) NOT NULL;

ALTER TABLE `vgr_team_request` CHANGE `idDemande` `idRequest` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_team_request` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_team_request` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `vgr_team_request` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;
ALTER TABLE `vgr_team_request` CHANGE `statut` `status` ENUM('ACTIF','ACCEPT','CANCEL','REFUSE','ACTIVE','ACCEPTED','CANCELED','REFUSED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'ACTIF';
UPDATE `vgr_team_request` SET status = 'ACTIVE' WHERE status = 'ACTIF';
UPDATE `vgr_team_request` SET status = 'ACCEPTED' WHERE status = 'ACCEPT';
UPDATE `vgr_team_request` SET status = 'CANCELED' WHERE status = 'CANCEL';
UPDATE `vgr_team_request` SET status = 'REFUSED' WHERE status = 'REFUSE';
ALTER TABLE `vgr_team_request` CHANGE `status` `status` ENUM('ACTIVE','ACCEPTED','CANCELED','REFUSED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'ACTIVE';
--
-- User
--
CREATE TABLE user_group (userId INT NOT NULL, groupId INT NOT NULL, INDEX IDX_FE1D13664B64DCC (userId), INDEX IDX_FE1D136ED8188B0 (groupId), PRIMARY KEY(userId, groupId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE groupRole (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', UNIQUE INDEX UNIQ_39A2D4D75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NULL, password VARCHAR(255) NOT NULL,comment TEXT, avatar VARCHAR(100) NOT NULL DEFAULT 'default.png', last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, nbConnexion INT NOT NULL DEFAULT 0, nbForumMessage INT NOT NULL DEFAULT 0,locale VARCHAR(2) DEFAULT NULL, firstName VARCHAR(255) DEFAULT NULL, lastName VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, birthDate DATE DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, timeZone INT DEFAULT NULL, personalWebsite VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, googleplus VARCHAR(255) DEFAULT NULL, youtube VARCHAR(255) DEFAULT NULL, dailymotion VARCHAR(255) DEFAULT NULL, twitch VARCHAR(255) DEFAULT NULL, skype VARCHAR(255) DEFAULT NULL, snapchat VARCHAR(255) DEFAULT NULL, pinterest VARCHAR(255) DEFAULT NULL, trumblr VARCHAR(255) DEFAULT NULL, blogger VARCHAR(255) DEFAULT NULL, reddit VARCHAR(255) DEFAULT NULL, deviantart VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, idCountry INT DEFAULT NULL, UNIQUE INDEX UNIQ_70E4FA7892FC23A8 (username_canonical), UNIQUE INDEX UNIQ_70E4FA78A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_70E4FA78C05FB297 (confirmation_token), INDEX IDX_70E4FA7847626230 (idCountry), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB;
ALTER TABLE user ADD CONSTRAINT FK_70E4FA7847626230 FOREIGN KEY (idCountry) REFERENCES country (id);
ALTER TABLE user_group ADD CONSTRAINT FK_FE1D13664B64DCC FOREIGN KEY (userId) REFERENCES user (id);
ALTER TABLE user_group ADD CONSTRAINT FK_FE1D136ED8188B0 FOREIGN KEY (groupId) REFERENCES groupRole (id);

-- New id for link between normandie & vgr
ALTER TABLE vgr_player ADD normandie_user_id INT DEFAULT NULL;

ALTER TABLE `vgr_player` CHANGE `derniereConnexion` `derniereConnexion` DATETIME NULL;
UPDATE vgr_player SET derniereConnexion = NULL  WHERE CAST(derniereConnexion AS CHAR(20)) = '0000-00-00 00:00:00';
UPDATE vgr_player SET dateNaissance = NULL  WHERE CAST(dateNaissance AS CHAR(11)) LIKE '0%';
UPDATE vgr_player SET dateCreation = '2004-10-30 00:00:00', dateModification = '2004-10-30 00:00:00'  WHERE id = 0;

-- Procedure to migrate user
DELIMITER &&
CREATE PROCEDURE user_migrate()
BEGIN
  DECLARE done, locked INT DEFAULT FALSE;
  DECLARE duplicateIncrement INT DEFAULT 100;
  DECLARE user_id, vgr_user_id, pays, nb_connection, nb_forum_message INT;
  DECLARE userName varchar(180) CHARSET utf8;
  DECLARE userAvatar varchar(100) CHARSET utf8;
  DECLARE gender varchar(1);
  DECLARE birthdate date;
  DECLARE userDateCreation, userDateModification, userDerniereConnexion datetime;
  DECLARE v_email, nom, prenom, siteWeb, statutCompte, sexe varchar(255);
  DECLARE cur1 CURSOR FOR SELECT id, pseudo, email, nom, prenom, dateNaissance, nbConnexion, nbForumMessage, siteWeb, statutCompte,
                            dateCreation, dateModification, derniereConnexion, sexe, idPays, avatar
                          FROM vgr_player WHERE id != 0;
  -- Handler for duplicate email
  DECLARE CONTINUE HANDLER FOR 1062
    BEGIN
      -- Log for duplicate email
      SELECT CONCAT('Duplicate email for: ', v_email);
      SET duplicateIncrement = duplicateIncrement + 1;
      SET v_email = CONCAT(v_email, '#', duplicateIncrement);
      SET locked = TRUE;
      -- Retry with new mail
      INSERT INTO user (username, username_canonical, password, email, email_canonical, firstName, lastName, birthDate,
                          enabled, locked, expired, credentials_expired, salt, roles, nbConnexion, nbForumMessage, personalWebsite, gender, avatar,
                          created_at, updated_at, last_login, idCountry, confirmation_token, password_requested_at)
      VALUES
        (userName, userName, "", v_email, v_email, prenom, nom, birthdate, false, locked, false, true,
         MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), 'a:0:{}', nb_connection, nb_forum_message, siteWeb, gender, userAvatar,
         userDateCreation, userDateModification, userDerniereConnexion, pays,
         MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), NOW()
        );
    END;

  -- Handler for finishing the loop
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur1;
  read_loop: LOOP
    FETCH cur1 INTO vgr_user_id, userName, v_email, nom, prenom, birthdate, nb_connection, nb_forum_message, siteWeb, statutCompte,
      userDateCreation, userDateModification, userDerniereConnexion, sexe, pays, userAvatar;
    IF done THEN
      LEAVE read_loop;
    END IF;
    IF statutCompte IN ('SUPPRIME', 'BANNI') THEN SET locked = TRUE; ELSE SET locked = FALSE; END IF;
    IF sexe = 'homme' THEN
      SET gender = 'H';
    ELSEIF sexe = 'femme' THEN
      SET gender = 'F';
    ELSE
      SET gender = 'I';
    END IF;

    INSERT INTO user (username, username_canonical, password, email, email_canonical, firstName, lastName, birthDate,
              enabled, locked, expired, credentials_expired, salt, roles, nbConnexion, nbForumMessage, personalWebsite, gender, avatar,
              created_at, updated_at, last_login, idCountry, confirmation_token, password_requested_at)
    VALUES
      (userName, userName, "", v_email, v_email, prenom, nom, birthdate, false, locked, false, true,
       MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), 'a:0:{}', nb_connection, nb_forum_message, siteWeb, gender, userAvatar,
       userDateCreation, userDateModification, userDerniereConnexion, pays,
       MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), NOW()
      );
    SET user_id = LAST_INSERT_ID();
    UPDATE vgr_player SET normandie_user_id = user_id WHERE id = vgr_user_id;
  END LOOP;
  CLOSE cur1;
END&&

DELIMITER ;

CALL user_migrate();
DROP PROCEDURE user_migrate;

-- INSERT VGR USER
INSERT INTO user (id, username, username_canonical, email, email_canonical, enabled, idCountry, created_at, updated_at,salt,password, locked, expired, roles, credentials_expired, nbConnexion)
VALUES (0, 'VGR', 'VGR', 'videogamesrecords@gmail.com', 'videogamesrecords@gmail.com', 0, 1, NOW(), NOW(), '', '', 1, 1, 'a:0:{}',1,0);
UPDATE user SET id=0 WHERE email = 'videogamesrecords@gmail.com';


ALTER TABLE vgr_player DROP FOREIGN KEY vgr_player_ibfk_3;
ALTER TABLE vgr_player DROP FOREIGN KEY vgr_player_ibfk_4;
ALTER TABLE vgr_player DROP FOREIGN KEY vgr_player_ibfk_5;

ALTER TABLE vgr_player DROP password, DROP email, DROP confirm_email, DROP nom, DROP prenom, DROP dateNaissance,
DROP statutCompte, DROP siteWeb, DROP nbConnexion, DROP derniereConnexion, DROP sexe, DROP dateCreation, DROP dateModification,DROP nbForumMessage,
DROP MSN, DROP nbCommentaire, DROP boolTeam, DROP boolContact, DROP boolNewsletter, DROP boolAssoc, DROP boolShowFbLikeBox,
DROP boolNotifCommentaire, DROP mailSending, DROP don,DROP idLangueForum, DROP idStatut, DROP idRang, DROP presentation;
/* ,,  DROP signature, DROP dateFormat, DROP utcFormat,  DROP idLangue, DROP idPays, */
ALTER TABLE `vgr_player` CHANGE `idPays` `idCountry` INT(11) NULL DEFAULT NULL;
ALTER TABLE vgr_player CHANGE avatar avatar VARCHAR(100) NOT NULL,
CHANGE vgr_gamerCard gamerCard VARCHAR(50) DEFAULT NULL,
CHANGE vgr_displayGamerCard displayGamerCard TINYINT(1) NOT NULL,
CHANGE vgr_displayGoalBar displayGoalBar TINYINT(1) NOT NULL,
CHANGE vgr_rank0 chartRank0 INT DEFAULT NULL,
CHANGE vgr_rank1 chartRank1 INT DEFAULT NULL,
CHANGE vgr_rank2 chartRank2 INT DEFAULT NULL,
CHANGE vgr_rank3 chartRank3 INT DEFAULT NULL,
CHANGE vgr_pointRecord pointChart INT NOT NULL,
CHANGE vgr_pointVGR pointVGR INT NOT NULL,
CHANGE vgr_pointBadge pointBadge INT NOT NULL,
CHANGE vgr_cup_rank0 gameRank0 INT DEFAULT NULL,
CHANGE vgr_cup_rank1 gameRank1 INT DEFAULT NULL,
CHANGE vgr_cup_rank2 gameRank2 INT DEFAULT NULL,
CHANGE vgr_cup_rank3 gameRank3 INT DEFAULT NULL,
CHANGE vgr_pointJeu pointGame INT DEFAULT NULL,
CHANGE vgr_nbRecord nbChart INT DEFAULT NULL,
CHANGE vgr_nbRecordProuve nbChartProven  INT DEFAULT NULL,
CHANGE vgr_rank_point rankPointChart  INT DEFAULT NULL,
CHANGE vgr_rank_medal rankMedal  INT DEFAULT NULL,
CHANGE vgr_rank_proof rankProof  INT DEFAULT NULL,
CHANGE vgr_rank_badge rankBadge  INT DEFAULT NULL,
CHANGE vgr_rank_cup rankCup  INT DEFAULT NULL,
CHANGE vgr_nbMasterBadge nbMasterBadge  INT DEFAULT NULL,
CHANGE vgr_rank_pointJeu rankPointGame INT DEFAULT NULL,
CHANGE vgr_collection collection text DEFAULT NULL;
ALTER TABLE `vgr_player` ADD `nbGame` INT NOT NULL DEFAULT '0' AFTER `gameRank3`;
ALTER TABLE `vgr_player` ADD `rankCountry` INT NOT NULL AFTER `rankCup`;
ALTER TABLE `vgr_player` ADD slug VARCHAR(255) DEFAULT NULL;
ALTER TABLE `vgr_player` ADD `nbChartDisabled` INT NULL DEFAULT '0' AFTER `nbChartProven`;
UPDATE `vgr_player`
SET nbChartDisabled = (SELECT (COUNT(id)) FROM vgr_player_chart WHERE idStatus = 7 AND idPlayer = vgr_player.id);
UPDATE `vgr_player` SET
    slug = lower(pseudo),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');

--
UPDATE vgr_game g
SET nbTeam = (SELECT COUNT(idGame) FROM vgr_team_game tg WHERE tg.idGame = g.id);

UPDATE vgr_player p
SET nbGame = (SELECT COUNT(idGame) FROM vgr_player_game pg WHERE pg.idPlayer = p.id);

-- Badge
ALTER TABLE `badge` CHANGE `idBadge` `id` INT(13) NOT NULL AUTO_INCREMENT;
ALTER TABLE `badge` CHANGE `image` `picture` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'defaut.gif';
ALTER TABLE `vgr_game` ADD `idBadge` INT NULL;
ALTER TABLE vgr_game ADD CONSTRAINT vgr_game_ibfk_3 FOREIGN KEY (idBadge) REFERENCES badge (id);
UPDATE vgr_game a, badge b
SET a.idBadge = b.id
WHERE a.id = b.idJeu;
ALTER TABLE badge DROP FOREIGN KEY badge_ibfk_1;
ALTER TABLE `badge` DROP `idJeu`;
ALTER TABLE `badge` CHANGE `nbMembre` `nbUser` INT(11) NOT NULL;
ALTER TABLE `vgr_player_badge` CHANGE `idMembre` `idPlayer` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_player_badge` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `vgr_player_badge` ADD updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_player_badge` CHANGE `dateFin` `ended_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `vgr_team_badge` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `vgr_team_badge` ADD updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_team_badge` CHANGE `dateFin` `ended_at` DATETIME NULL DEFAULT NULL;
ALTER TABLE `vgr_team_badge` DROP PRIMARY KEY;
ALTER TABLE `vgr_team_badge` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE `badge` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
UPDATE `vgr_player_badge` SET updated_at = NOW();
UPDATE badge SET type='VgrChart' WHERE type='Record';
UPDATE badge SET type='VgrProof' WHERE type='Preuve';
UPDATE badge SET type='VgrSpecialCountry' WHERE type='Special' AND  `value`IS NOT NULL;
UPDATE badge SET type='VgrSpecialCup' WHERE type='SpecialCoupes';
UPDATE badge SET type='VgrSpecialLegend' WHERE type='SpecialLegende';
UPDATE badge SET type='VgrSpecialMedals' WHERE type='SpecialMedailles';
UPDATE badge SET type='VgrSpecialPoints' WHERE type='SpecialPoints';

DELETE FROM badge WHERE type='Special';

ALTER TABLE `vgr_game` ADD UNIQUE(`idBadge`);

UPDATE country SET idBadge = null;
UPDATE country,badge
SET idBadge = badge.id
WHERE country.id = badge.value
AND type = 'VgrSpecialCountry';
-- ALTER TABLE `country` ADD CONSTRAINT `FK_COUNTRY_BADGE` FOREIGN KEY (`idBadge`) REFERENCES `badge`(`id`) ON DELETE SET NULL ON UPDATE SET NULL;

ALTER TABLE `vgr_player_badge` DROP PRIMARY KEY;
ALTER TABLE `vgr_player_badge` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);

-- PlayerChartStatus
ALTER TABLE `vgr_player_chart_status` ADD `boolSendProof` TINYINT NOT NULL DEFAULT '0' AFTER `boolRanking`;
UPDATE `vgr_player_chart_status` SET boolSendProof = 1 WHERE id IN (1,3,7);



--
-- VIDEO Part
--
ALTER TABLE `vgr_video` CHANGE `idVideo` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE vgr_video CHANGE statut status ENUM('UPLOAD','WORK','OK','ERROR','UPLOADED','IN PROGRESS') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'UPLOADED';
UPDATE vgr_video SET status = 'UPLOADED' WHERE status = 'UPLOAD';
UPDATE vgr_video SET status = 'IN PROGRESS' WHERE status = 'WORK';
ALTER TABLE vgr_video CHANGE status status ENUM('OK','ERROR','UPLOADED','IN PROGRESS') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'UPLOADED';
ALTER TABLE vgr_video CHANGE dateCreation created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE vgr_video CHANGE dateModification updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE vgr_video CHANGE idMembre idPlayer INT(11) NOT NULL;
ALTER TABLE vgr_video CHANGE vgr_idJeu idGame INT(11) NULL DEFAULT NULL;
ALTER TABLE vgr_video CHANGE nbCommentaire nbComment INT(10) UNSIGNED NOT NULL DEFAULT '0';
ALTER TABLE `vgr_video` ADD `url` VARCHAR(255) NULL AFTER `status`;
ALTER TABLE `vgr_video` DROP `thumb`;
DELETE FROM `vgr_video` WHERE status IN ('IN PROGRESS', 'ERROR', 'UPLOADED');
ALTER TABLE `vgr_video` CHANGE `fileIn` `fileIn` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';

--
-- VGR GAME MESSAGE Part
--
CREATE TABLE vgr_game_topic (idTopic int(11) NOT NULL, idGame int(11) NOT NULL, idPlayer int(11) NOT NULL, libTopic varchar(255) NOT NULL, created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, oldIdTopic int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE vgr_game_topic ADD PRIMARY KEY (idTopic), ADD KEY idxPlayer (idPlayer), ADD KEY idxGame (idGame);

ALTER TABLE vgr_game_topic MODIFY idTopic int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE vgr_game_topic
  ADD CONSTRAINT fk_game_topic_game FOREIGN KEY (idGame) REFERENCES vgr_game (id),
  ADD CONSTRAINT fk_game_topic_player FOREIGN KEY (idPlayer) REFERENCES vgr_player (id);

CREATE TABLE vgr_game_message (
  idMessage int(11) NOT NULL,
  idTopic int(11) NOT NULL,
  idPlayer int(11) NOT NULL,
  text text NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE vgr_game_message
  ADD PRIMARY KEY (idMessage),
  ADD KEY idxTopic (idTopic),
  ADD KEY idxPlayer (idPlayer);

ALTER TABLE vgr_game_message
  MODIFY idMessage int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE vgr_game_message
  ADD CONSTRAINT fk_game_message_player FOREIGN KEY (idPlayer) REFERENCES vgr_player (id),
  ADD CONSTRAINT fk_game_message_topic FOREIGN KEY (idTopic) REFERENCES vgr_game_topic (idTopic);


INSERT INTO vgr_game_topic (idPlayer, idGame, libTopic, oldIdTopic)
SELECT idMembre,vgr_game.id, libTopic, idTopic
FROM t_forum
  INNER JOIN vgr_game ON t_forum.idForum = vgr_game.idForum
  INNER JOIN t_forum_topic ON t_forum_topic.idForum = t_forum.idForum;

INSERT INTO vgr_game_message (idTopic, idPlayer, text, created_at, updated_at)
SELECT vgr_game_topic.idTopic, t_forum_message.idMembre, t_forum_message.texte, t_forum_message.dateCreation, t_forum_message.dateModification
FROM t_forum_message INNER JOIN vgr_game_topic ON vgr_game_topic.oldIdTopic = t_forum_message.idTopic
ORDER BY t_forum_message.idMessage ASC;

UPDATE vgr_game_topic a, vgr_game_message b
SET a.created_at = b.created_at
WHERE a.idTopic = b.idTopic;

UPDATE vgr_game_topic a, vgr_game_message b
SET a.updated_at = b.created_at
WHERE a.idTopic = b.idTopic;

ALTER TABLE t_forum_topic DROP FOREIGN KEY t_forum_topic_ibfk_2;
ALTER TABLE t_forum_topic ADD CONSTRAINT t_forum_topic_ibfk_2 FOREIGN KEY (idForum) REFERENCES t_forum(idForum) ON DELETE CASCADE ON UPDATE CASCADE;

DELETE FROM t_forum WHERE idForumPere = 42;
DELETE FROM t_forum WHERE idForum = 42;

--
-- VGR TEAM MESSAGE Part
--
CREATE TABLE vgr_team_topic (idTopic int(11) NOT NULL, idTeam int(11) NOT NULL, idPlayer int(11) NOT NULL, libTopic varchar(255) NOT NULL, created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP, oldIdTopic int(11) NOT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE vgr_team_topic ADD PRIMARY KEY (idTopic), ADD KEY idxPlayer (idPlayer), ADD KEY idxTeam (idTeam);

ALTER TABLE vgr_team_topic MODIFY idTopic int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE vgr_team_topic
  ADD CONSTRAINT fk_team_topic_team FOREIGN KEY (idTeam) REFERENCES vgr_team (id),
  ADD CONSTRAINT fk_team_topic_player FOREIGN KEY (idPlayer) REFERENCES vgr_player (id);

CREATE TABLE vgr_team_message (
  idMessage int(11) NOT NULL,
  idTopic int(11) NOT NULL,
  idPlayer int(11) NOT NULL,
  text text NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE vgr_team_message
  ADD PRIMARY KEY (idMessage),
  ADD KEY idxTopic (idTopic),
  ADD KEY idxPlayer (idPlayer);

ALTER TABLE vgr_team_message
  MODIFY idMessage int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE vgr_team_message
  ADD CONSTRAINT fk_team_message_player FOREIGN KEY (idPlayer) REFERENCES vgr_player (id),
  ADD CONSTRAINT fk_team_message_topic FOREIGN KEY (idTopic) REFERENCES vgr_team_topic (idTopic);


INSERT INTO vgr_team_topic (idPlayer, idTeam, libTopic, oldIdTopic)
SELECT idMembre,vgr_team.id, libTopic, idTopic
FROM t_forum
  INNER JOIN vgr_team ON t_forum.idForum = vgr_team.idForum
  INNER JOIN t_forum_topic ON t_forum_topic.idForum = t_forum.idForum;

INSERT INTO vgr_team_message (idTopic, idPlayer, text, created_at, updated_at)
SELECT vgr_team_topic.idTopic, t_forum_message.idMembre, t_forum_message.texte, t_forum_message.dateCreation, t_forum_message.dateModification
FROM t_forum_message INNER JOIN vgr_team_topic ON vgr_team_topic.oldIdTopic = t_forum_message.idTopic
ORDER BY t_forum_message.idMessage ASC;


UPDATE vgr_team_topic a, vgr_team_message b
SET a.created_at = b.created_at
WHERE a.idTopic = b.idTopic;

UPDATE vgr_team_topic a, vgr_team_message b
SET a.updated_at = b.created_at
WHERE a.idTopic = b.idTopic;

ALTER TABLE vgr_team DROP FOREIGN KEY vgr_team_ibfk_2;
ALTER TABLE `vgr_team` DROP `idForum`;
DELETE FROM t_forum WHERE idForumPere = 793;
DELETE FROM t_forum WHERE idForum = 793;

--
-- PARTNER Part
--
ALTER TABLE `partner` CHANGE `idPartenaire` `idPartner` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `partner` CHANGE `libPartenaire` `libPartner` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `partner` CHANGE `commentaire` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `partner` CHANGE `statut` `status` ENUM('ACTIF','INACTIF','BANNI','ACTIVE','INACTIVE','CANCELED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'INACTIF';
UPDATE `partner` SET status = 'ACTIVE' WHERE status = 'ACTIF';
UPDATE `partner` SET status = 'INACTIVE' WHERE status = 'INACTIF';
UPDATE `partner` SET status = 'CANCELED' WHERE status = 'BANNI';
ALTER TABLE `partner` CHANGE `status` `status` ENUM('ACTIVE','INACTIVE','CANCELED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'INACTIVE';
ALTER TABLE `partner` DROP `image`;
ALTER TABLE `partner` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `order`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
ALTER TABLE `partner` ADD `contact` VARCHAR(255) NULL AFTER `url`;



--
-- MESSAGE Part
--
ALTER TABLE `message` CHANGE `idMessagePrive` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `message` CHANGE `idAuteur` `idSender` INT(11) NULL;
ALTER TABLE `message` CHANGE `idDestinataire` `idRecipient` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `message` CHANGE `objet` `object` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `message` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `message` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;
ALTER TABLE `message` CHANGE `boolOpen` `isOpened` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `message` CHANGE `boolDeleteAuteur` `isDeletedSender` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `message` CHANGE `boolDeleteDestinataire` `isDeletedRecipient` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `message` ADD `type` VARCHAR(50) NOT NULL DEFAULT 'DEFAULT' AFTER `object`;

UPDATE `message` SET type = 'VGR_PROOF_ACCEPTED' WHERE vgr_proof = 1 AND vgr_accepted = 1;
UPDATE `message` SET type = 'VGR_PROOF_REFUSED' WHERE vgr_proof = 1 AND vgr_accepted = 0;
UPDATE `message` SET type = 'VGR_REQUEST_ACCEPTED' WHERE vgr_request = 1 AND vgr_accepted = 1;
UPDATE `message` SET type = 'VGR_REQUEST_REFUSED' WHERE vgr_request = 1 AND vgr_accepted = 0;
UPDATE `message` SET type = 'VIDEO_COMMENT' WHERE  commentaireVideo = 1;
UPDATE `message` SET type = 'FORUM_NOTIF' WHERE  notification = 1;

ALTER TABLE `message` DROP `boolRepondre`;
ALTER TABLE `message` DROP `notification`;
ALTER TABLE `message` DROP `commentaireVideo`;
ALTER TABLE `message` DROP `vgr_request`;
ALTER TABLE `message` DROP `vgr_proof`;
ALTER TABLE `message` DROP `vgr_accepted`;

ALTER TABLE `message` ADD INDEX `idxType` (`type`);
ALTER TABLE `message` ADD INDEX `idxIsDeletedSender` (`isDeletedSender`);
ALTER TABLE `message` ADD INDEX `idxIsDeletedRecipient` (`isDeletedRecipient`);


ALTER TABLE message DROP FOREIGN KEY message_ibfk_1;
ALTER TABLE message DROP FOREIGN KEY message_ibfk_2;

UPDATE message SET idSender = null WHERE idSender = 0;

UPDATE message m, vgr_player p
SET m.idSender = p.normandie_user_id
WHERE m.idSender = p.id;

DELETE FROM message WHERE idRecipient = 0;

UPDATE message m, vgr_player p
SET m.idRecipient = p.normandie_user_id
WHERE m.idRecipient = p.id;


ALTER TABLE `message` ADD CONSTRAINT `fk_sender` FOREIGN KEY (`idSender`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `message` ADD CONSTRAINT `fk_recipient` FOREIGN KEY (`idRecipient`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

UPDATE message SET idSender = 0 WHERE idSender IS NULL;

--
-- VGR PROOF PART
--

-- request
ALTER TABLE `vgr_proof_request` CHANGE `idDemande` `idRequest` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_proof_request` CHANGE `idMembre` `idPlayer` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_proof_request` CHANGE `idRecord` `idChart` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_proof_request` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `vgr_proof_request` CHANGE `dateModification` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
UPDATE `vgr_proof_request` SET updated_at = NOW() WHERE CAST(updated_at AS CHAR(20)) LIKE '0%';
ALTER TABLE `vgr_proof_request` CHANGE `statut` `status` ENUM('EN COURS','FINI','IN PROGRESS','REFUSED','ACCEPTED','CLOSED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'IN PROGRESS';
ALTER TABLE `vgr_proof_request` CHANGE `idDemandeur` `idPlayerRequesting` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_proof_request` CHANGE `texte` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_proof_request` CHANGE `dateAcceptation` `dateAcceptance` DATETIME NULL DEFAULT NULL;
ALTER TABLE `vgr_proof_request` CHANGE `idAdmin` `idPlayerResponding` INT(11) NULL DEFAULT NULL;
UPDATE `vgr_proof_request` SET status = 'REFUSED' WHERE status = 'FINI' AND dateAcceptance IS NULL;
UPDATE `vgr_proof_request` SET status = 'ACCEPTED' WHERE status = 'FINI' AND dateAcceptance IS NOT NULL;
UPDATE `vgr_proof_request` SET status = 'IN PROGRESS' WHERE status = 'EN COURS';
ALTER TABLE `vgr_proof_request` CHANGE `status` `status` ENUM('IN PROGRESS','REFUSED','ACCEPTED','CLOSED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'IN PROGRESS';
ALTER TABLE `vgr_proof_request` ADD `idPlayerChart` INT NOT NULL AFTER `idRequest`;
UPDATE `vgr_proof_request` r, `vgr_player_chart` c
SET r.idPlayerChart = c.id
WHERE r.idPlayer = c.idPlayer AND r.idChart = c.idChart;
DELETE FROM `vgr_proof_request` WHERE idPlayerChart = 0;
ALTER TABLE `vgr_proof_request` ADD INDEX(`idPlayerChart`);
ALTER TABLE `vgr_proof_request` ADD FOREIGN KEY (`idPlayerChart`) REFERENCES `vgr_player_chart`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;


-- proof
ALTER TABLE `vgr_proof` CHANGE `idPreuve` `idProof` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_proof` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_proof` CHANGE `idRecord` `idChart` INT(11) NOT NULL;
ALTER TABLE `vgr_proof` CHANGE `dateAjout` `created_at` DATETIME NOT NULL;
ALTER TABLE `vgr_proof` ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `bAccept`;
ALTER TABLE `vgr_proof` CHANGE `idAdmin` `idPlayerResponding` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_proof` CHANGE `bAccept` `boolAccepted` TINYINT(1) NULL DEFAULT NULL;
UPDATE `vgr_proof` set updated_at = dateTraitement WHERE boolAccepted IS NOT NULL;
UPDATE `vgr_proof` set updated_at = created_at WHERE boolAccepted IS NULL;
ALTER TABLE `vgr_proof` DROP `dateTraitement`;
ALTER TABLE `vgr_proof` ADD `idPicture` INT NULL AFTER `idChart`;
ALTER TABLE `vgr_proof` CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `vgr_proof` ADD INDEX(`idPicture`);
ALTER TABLE `vgr_proof` ADD `status` ENUM('IN PROGRESS','REFUSED','ACCEPTED') NOT NULL DEFAULT 'IN PROGRESS' AFTER `idVideo`, ADD INDEX (`status`);
UPDATE `vgr_proof` SET status = 'ACCEPTED' WHERE boolAccepted = 1;
UPDATE `vgr_proof` SET status = 'REFUSED' WHERE boolAccepted = 0;
ALTER TABLE `vgr_proof` DROP `boolAccepted`;


-- playerChart
ALTER TABLE `vgr_player_chart` ADD `idProof` INT NULL AFTER `idStatus`;


-- update
UPDATE vgr_proof p, vgr_player_chart pc
SET p.idPicture = pc.idPicture
WHERE p.idPlayer = pc.idPlayer
AND p.idChart = pc.idChart
AND pc.idPicture IS NOT NULL;

UPDATE vgr_proof p, vgr_player_chart pc
SET p.idVideo = pc.idVideo
WHERE p.idPlayer = pc.idPlayer
      AND p.idChart = pc.idChart;

UPDATE vgr_player_chart pc, vgr_proof p
SET pc.idProof = p.idProof
WHERE pc.idPicture = p.idPicture
AND pc.idPicture IS NOT NULL;

UPDATE vgr_player_chart pc, vgr_proof p
SET pc.idProof = p.idProof
WHERE pc.idVideo = p.idVideo
AND pc.idVideo IS NOT NULL
AND pc.idVideo != 0
AND p.idVideo IS NOT NULL
AND p.idVideo != 0;

-- delete
DELETE FROM vgr_proof WHERE idPlayer NOT IN (SELECT id FROM vgr_player);
UPDATE vgr_proof SET idVideo = NULL WHERE idVideo = 0;
DELETE FROM vgr_proof WHERE idPicture IS NULL AND idVideo IS NULL;


-- Attention pas de video + picture en même temps
UPDATE vgr_player_chart SET idVideo = 0 WHERE idPicture IS NOT NULL and idVideo != 0;


-- maj idPlatform
CREATE VIEW view_chart_platform
  AS
    SELECT a.idGame, d.id as idChart, b.idPlatform
  FROM
    (SELECT idGame, count(idPlatform) as nb
     FROM vgr_game_platform
     GROUP BY idGame
     HAVING nb = 1
    ) a
  INNER JOIN vgr_game_platform b ON a.idGame = b.idGame
  INNER JOIN vgr_group c ON b.idGame = c.idGame
  INNER JOIN vgr_chart d ON c.id = d.idGroup;

UPDATE vgr_player_chart a, view_chart_platform b
SET a.idPlatform = b.idPlatform
WHERE a.idChart = b.idChart;
DROP view view_chart_platform;


-- drop
ALTER TABLE `vgr_player_chart` DROP `idVideo`;
ALTER TABLE `vgr_player_chart` DROP `preuveImage`;
ALTER TABLE `vgr_player_chart` DROP `idPicture`;
ALTER TABLE vgr_proof_request DROP FOREIGN KEY vgr_proof_request_ibfk_1;
ALTER TABLE vgr_proof_request DROP FOREIGN KEY vgr_proof_request_ibfk_3;
ALTER TABLE vgr_proof_request DROP INDEX idxUnique;
ALTER TABLE vgr_proof_request DROP COLUMN idPlayer;
ALTER TABLE vgr_proof_request DROP COLUMN idChart;
-- ALTER TABLE vgr_proof DROP COLUMN idPlayer;
-- ALTER TABLE vgr_proof DROP COLUMN idChart;
ALTER TABLE `vgr_proof` CHANGE `idPlayer` `idPlayer` INT(11) NULL;
ALTER TABLE `vgr_proof` CHANGE `idChart` `idChart` INT(11) NULL;

-- ROLE
INSERT INTO `groupRole` (`id`, `name`, `roles`) VALUES
(1, 'SuperAdmin', 'a:1:{i:0;s:10:\"ROLE_SUPER_ADMIN\";}'),
(2, 'Player', 'a:1:{i:0;s:11:\"ROLE_PLAYER\";}'),
(3, 'AdminUser', 'a:1:{i:0;s:15:\"ROLE_USER_ADMIN\";}'),
(4, 'AdminGames', 'a:1:{i:0;s:15:\"ROLE_GAME_ADMIN\";}'),
(5, 'AdminProof', 'a:1:{i:0;s:16:\"ROLE_PROOF_ADMIN\";}'),
(6, 'AdminForum', 'a:1:{i:0;s:16:\"ROLE_FORUM_ADMIN\";}'),
(7, 'AdminMessage', 'a:1:{i:0;s:18:\"ROLE_MESSAGE_ADMIN\";}'),
(8, 'AdminArticle', 'a:1:{i:0;s:18:\"ROLE_ARTICLE_ADMIN\";}');

-- all users have player role
INSERT INTO user_group (userId, groupId) SELECT id,2 FROM user WHERE id != 0;


-- VGR PICTURE
CREATE TABLE `vgr_picture` (
  `id` int(11) NOT NULL,
  `path` varchar(255) NOT NULL,
  `metadata` text,
  `idPlayer` int(11) NOT NULL,
  `idGame` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `vgr_picture`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idxPath` (`path`) USING BTREE,
  ADD KEY `idxPlayer` (`idPlayer`) USING BTREE,
  ADD KEY `idxGame` (`idGame`) USING BTREE;




ALTER TABLE `vgr_picture` ADD CONSTRAINT `FK_PICTURE_PLAYER` FOREIGN KEY (`idPlayer`) REFERENCES `vgr_player`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `vgr_picture` ADD CONSTRAINT `FK_PICTURE_GAME` FOREIGN KEY (`idGame`) REFERENCES `vgr_game`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- ARTICLE
ALTER TABLE `article` CHANGE `idArticle` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `article` CHANGE `idMembre` `idAuthor` INT(13) NULL;

DELETE FROM article WHERE id in (1540,6486,6243,6148,4058,4059,3466,107,138);
ALTER TABLE `article` CHANGE `statut` `status` ENUM('EN_CONSTRUCTION','PUBLIE','ANNULE','UNDER CONSTRUCTION','PUBLISHED','CANCELED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'EN_CONSTRUCTION';
UPDATE `article` SET status='UNDER CONSTRUCTION' WHERE status = 'EN_CONSTRUCTION';
UPDATE `article` SET status='PUBLISHED' WHERE status = 'PUBLIE';
UPDATE `article` SET status='CANCELED' WHERE status = 'ANNULE';
ALTER TABLE `article` CHANGE `status` `status` ENUM('UNDER CONSTRUCTION','PUBLISHED','CANCELED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'UNDER CONSTRUCTION';
ALTER TABLE `article`DROP FOREIGN KEY article_ibfk_2;
ALTER TABLE `article` DROP `idTraducteur`;
ALTER TABLE `article` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `article` CHANGE dateModification updated_at DATETIME DEFAULT NULL;
ALTER TABLE `article` CHANGE datePublication published_at DATETIME DEFAULT NULL;
ALTER TABLE `article` DROP `image`;


CREATE TABLE article_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, text text  NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_ARTICLE_TRANS_ID (translatable_id), UNIQUE INDEX country_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE article_translation ADD CONSTRAINT FK_ARTICLE_TRANS_ID FOREIGN KEY (translatable_id) REFERENCES article (id) ON DELETE CASCADE;
TRUNCATE article_translation;
INSERT INTO article_translation (translatable_id, title, text, locale) SELECT id, libArticle_fr, texte_fr, 'fr' FROM article;
INSERT INTO article_translation (translatable_id, title, text, locale) SELECT id, libArticle_en, texte_en, 'en' FROM article;

ALTER TABLE `article` ADD `link` VARCHAR(255) NULL AFTER `status`;
ALTER TABLE `article` ADD `slug` VARCHAR(255) NULL AFTER `status`;

UPDATE `article` SET
    slug = lower(libArticle_en),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');

ALTER TABLE `article` DROP `libArticle_fr`;
ALTER TABLE `article` DROP `libArticle_en`;
ALTER TABLE `article` DROP `texte_fr`;
ALTER TABLE `article` DROP `texte_en`;

DELETE FROM `article` WHERE flag = 'NEWS_VGRJ';
ALTER TABLE `article` DROP `flag`;
ALTER TABLE `article` DROP `idJeu`;
ALTER TABLE `article` DROP `idPlateforme`;

--

UPDATE user SET enabled = 1;

-- IP
ALTER TABLE user_ip DROP FOREIGN KEY user_ip_ibfk_1;
ALTER TABLE user_ip DROP FOREIGN KEY user_ip_ibfk_2;

ALTER TABLE `ip` CHANGE `idIp` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `ip` CHANGE `IP` `label` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `ip` ADD `status` ENUM('NORMAL','SUSPICIOUS','BANNED') NOT NULL DEFAULT 'NORMAL' AFTER `banni`;
UPDATE `ip` SET status = 'BANNED' WHERE banni = 1;
ALTER TABLE `ip` DROP `banni`;
ALTER TABLE `ip` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;

ALTER TABLE `user_ip` CHANGE `idMembre` `idUser` INT(13) NOT NULL;
ALTER TABLE `user_ip` CHANGE `nbUtilisation` `nbConnexion` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `user_ip` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `nbConnexion`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;

UPDATE `user_ip` SET created_at = dateDernierLogin,updated_at = dateDernierLogin;
ALTER TABLE `user_ip` DROP `dateDernierLogin`;
ALTER TABLE `user_ip` DROP PRIMARY KEY;
ALTER TABLE `user_ip` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);

UPDATE user_ip up, vgr_player p
SET up.idUser = p.normandie_user_id
WHERE up.idUser = p.id
AND p.normandie_user_id IS NOT NULL;

ALTER TABLE `user_ip` ADD CONSTRAINT `FK_USERIP_USER` FOREIGN KEY (`idUser`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `user_ip` ADD CONSTRAINT `FK_USERIP_IP` FOREIGN KEY (`idIp`) REFERENCES `ip`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;


-- maj lastUpdate
UPDATE vgr_player_group pg
SET pg.lastUpdate = (
    SELECT IFNULL(MAX(pc.lastUpdate), NOW())
    FROM vgr_player_chart pc INNER JOIN vgr_chart c ON pc.idChart = c.id
    WHERE pg.idPlayer = pc.idPlayer
    AND pg.idGroup = c.idGroup);

-- maj lastUpdate
UPDATE vgr_player_game pga
SET pga.lastUpdate = (
    SELECT IFNULL(MAX(pgr.lastUpdate), NOW())
    FROM vgr_player_group pgr INNER JOIN vgr_group g ON pgr.idGroup = g.id
    WHERE pgr.idPlayer = pga.idPlayer
    AND g.idGame = pga.idGame);

-- FK_PLAYERCHART_PLATFORM
ALTER TABLE `vgr_player_chart` ADD INDEX(`idPlatform`);
ALTER TABLE `vgr_player_chart` ADD CONSTRAINT `FK_PLAYERCHART_PLATFORM` FOREIGN KEY (`idPlatform`) REFERENCES `vgr_platform`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `vgr_player_chart` ADD INDEX(`idProof`);
ALTER TABLE `vgr_player_chart` ADD CONSTRAINT `FK_PLAYERCHART_PROOF` FOREIGN KEY (`idProof`) REFERENCES `vgr_proof`(`idProof`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- rename id
ALTER TABLE `vgr_proof` CHANGE `idProof` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_proof_request` CHANGE `idRequest` `id` INT(11) NOT NULL AUTO_INCREMENT;

--
ALTER TABLE `user` CHANGE `locked` `locked` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `user` CHANGE `expired` `expired` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `user` CHANGE `credentials_expired` `credentials_expired` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `vgr_player` CHANGE `pointChart` `pointChart` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_player` CHANGE `pointVGR` `pointVGR` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_player` CHANGE `pointBadge` `pointBadge` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_player` DROP `displayGamerCard`;
ALTER TABLE `vgr_player` DROP `displayGoalBar`;
ALTER TABLE `vgr_player` DROP `dateFormat`;
ALTER TABLE `vgr_player` DROP `utcFormat`;
ALTER TABLE `vgr_player` CHANGE `signature` `signature` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `vgr_player` CHANGE `avatar` `avatar` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'default.png';
ALTER TABLE `vgr_player` CHANGE `rankCountry` `rankCountry` INT(11) NULL;

RENAME TABLE `t_langue` TO `language`;
ALTER TABLE `language` CHANGE `idLangue` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `language` CHANGE `libLangue` `label` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `language` CHANGE `fichier` `file` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `vgr_player` CHANGE `idLangue` `idLanguage` INT(11) NOT NULL DEFAULT '2';


-- FORUM
RENAME TABLE t_forum_categorie TO forum_category;
RENAME TABLE t_forum TO forum_forum;
RENAME TABLE t_forum_message TO forum_message;
RENAME TABLE t_forum_topic TO forum_topic;
RENAME TABLE t_forum_typetopic TO forum_topic_type;
RENAME TABLE t_forum_topic_membre TO forum_topic_user;

DROP TABLE IF EXISTS t_forum_rang;

ALTER TABLE `forum_category` CHANGE `idCategorie` `id` INT(13) NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_category` DROP `libCategorie_fr`;
ALTER TABLE `forum_category` CHANGE `libCategorie_en` `libCategory` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `forum_category` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `position`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `forum_forum` CHANGE `idForum` `id` INT(13) NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_forum` CHANGE `libForum_en` `libForum` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `forum_forum` CHANGE `idCategorie` `idCategory` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_forum` CHANGE `statut` `status` ENUM('public','private') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'public';
ALTER TABLE `forum_forum` CHANGE `idForumPere` `idForumFather` INT(13) NULL DEFAULT NULL;
ALTER TABLE `forum_forum` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `idMessageMax`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `forum_topic` CHANGE `idTopic` `id` INT(13) NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_topic` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_topic` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `idLangue`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
ALTER TABLE `forum_topic` DROP `idLangue`;

ALTER TABLE forum_topic DROP FOREIGN KEY forum_topic_ibfk_1;
UPDATE forum_topic t, vgr_player p
SET t.idUser = p.normandie_user_id
WHERE t.idUser = p.id;
ALTER TABLE `forum_topic` ADD CONSTRAINT `forum_topic_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `forum_message` CHANGE `idMessage` `id` INT(13) NOT NULL AUTO_INCREMENT;
ALTER TABLE `forum_message` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_message` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `forum_message` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;
ALTER TABLE `forum_message` CHANGE `texte` `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE forum_message DROP FOREIGN KEY forum_message_ibfk_2;
UPDATE forum_message m, vgr_player p
SET m.idUser = p.normandie_user_id
WHERE m.idUser = p.id;
ALTER TABLE `forum_message` ADD CONSTRAINT `forum_message_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;


ALTER TABLE `forum_topic_user` CHANGE `idMembre` `idUser` INT(13) NOT NULL DEFAULT '0';
ALTER TABLE `forum_topic_user` CHANGE `estLu` `boolRead` TINYINT(4) NOT NULL DEFAULT '0';
ALTER TABLE `forum_topic_user` CHANGE `estNotif` `boolNotif` TINYINT(4) NOT NULL DEFAULT '0';


ALTER TABLE forum_topic_user DROP FOREIGN KEY forum_topic_user_ibfk_1;
TRUNCATE table forum_topic_user;
ALTER TABLE `forum_topic_user` ADD CONSTRAINT `forum_topic_user_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE forum_topic_user DROP PRIMARY KEY;
ALTER TABLE `forum_topic_user` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);
ALTER TABLE `forum_topic_user` ADD UNIQUE( `idUser`, `idTopic`);


CREATE TABLE `forum_forum_user` (
  `id` int(11) NOT NULL,
  `idForum` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `boolRead` tinyint(4)	NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER  TABLE `forum_forum_user` ADD PRIMARY KEY (`id`);
ALTER  TABLE `forum_forum_user` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `forum_forum_user` ADD CONSTRAINT `forum_forum_user_ibfk_1` FOREIGN KEY (`idForum`) REFERENCES `forum_forum`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
ALTER TABLE `forum_forum_user` ADD CONSTRAINT `forum_forum_user_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `user`(`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

-- MAJ nbMessage
UPDATE forum_topic t
SET t.nbMessage = (SELECT COUNT(id) FROM forum_message WHERE idTopic = t.id);


ALTER TABLE `forum_forum` ADD `role` VARCHAR(50) NULL AFTER `status`;
UPDATE `forum_forum` SET role = 'ROLE_FORUM_VGR_TEAM' WHERE id = 38;
UPDATE `forum_forum` SET role = 'ROLE_FORUM_ADMINISTRATION' WHERE id = 16;


ALTER TABLE `forum_topic_type` DROP `image1`;
ALTER TABLE `forum_topic_type` DROP `image2`;


UPDATE `user` set locale='en';

ALTER TABLE `vgr_chart` CHANGE `nbPost` `nbPost` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_chart` CHANGE `statusPlayer` `statusPlayer` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'NORMAL';
ALTER TABLE `vgr_chart` CHANGE `statusTeam` `statusTeam` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'NORMAL';
ALTER TABLE `vgr_group` CHANGE `nbPost` `nbPost` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_group` CHANGE `nbPlayer` `nbPlayer` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_group` CHANGE `nbChart` `nbChart` INT(11) NOT NULL DEFAULT '0';

-- Email
ALTER TABLE `email` CHANGE `emailId` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `email` CHANGE `idMembre` `idUser` INT(11) NULL DEFAULT NULL;
ALTER TABLE `email` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `email` ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
ALTER TABLE `email` CHANGE `email` `emailTo` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `email` ADD `emailFrom` VARCHAR(255) NULL AFTER `emailTo`;


-- PageBundle
CREATE TABLE `page` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  created_at datetime NOT NULL,
  updated_at datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`);
ALTER TABLE `page`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `page_translation` (
  `id` int(11) NOT NULL,
  `translatable_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `locale` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `page_translation`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `page_translation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `page_translation` ADD INDEX(`translatable_id`);

ALTER TABLE `page_translation` ADD FOREIGN KEY (`translatable_id`) REFERENCES `page`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `forum_message` CHANGE `message` `message` MEDIUMTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `vgr_game_topic` CHANGE `idTopic` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_game_message` CHANGE `idMessage` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_game_topic` DROP `oldIdTopic`;

ALTER TABLE `vgr_picture` ADD `hash` VARCHAR(255) NOT NULL AFTER `path`;
ALTER TABLE `vgr_picture` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `idGame`, ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


ALTER TABLE `vgr_video` DROP `nbView`;
ALTER TABLE `vgr_video` DROP `nbComment`;
ALTER TABLE `vgr_video` ADD `type` ENUM('Youtube','Twitch','Unknown') NULL DEFAULT NULL AFTER `status`;

ALTER TABLE `vgr_platform` CHANGE `class` `slug` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE forum_forum ADD slug VARCHAR(255) DEFAULT NULL;
UPDATE `forum_forum` SET
    slug = lower(libForum),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');


ALTER TABLE forum_topic ADD slug VARCHAR(255) DEFAULT NULL;
UPDATE `forum_topic` SET
    slug = lower(libTopic),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');


ALTER TABLE vgr_video ADD slug VARCHAR(255) DEFAULT NULL;
UPDATE `vgr_video` SET
    slug = lower(libVideo),
    slug = replace(slug, '.', ' '),
    slug = replace(slug, ',', ' '),
    slug = replace(slug, ';', ' '),
    slug = replace(slug, ':', ' '),
    slug = replace(slug, '?', ' '),
    slug = replace(slug, '%', ' '),
    slug = replace(slug, '&', ' '),
    slug = replace(slug, '#', ' '),
    slug = replace(slug, '*', ' '),
    slug = replace(slug, '!', ' '),
    slug = replace(slug, '_', ' '),
    slug = replace(slug, '@', ' '),
    slug = replace(slug, '+', ' '),
    slug = replace(slug, '(', ' '),
    slug = replace(slug, ')', ' '),
    slug = replace(slug, '[', ' '),
    slug = replace(slug, ']', ' '),
    slug = replace(slug, '/', ' '),
    slug = replace(slug, '-', ' '),
    slug = replace(slug, '\'', ''),
    slug = trim(slug),
    slug = replace(slug, ' ', '-'),
    slug = replace(slug, '--', '-'),
    slug = replace(slug, '--', '-');

ALTER TABLE `vgr_video` ADD `boolActive` BOOLEAN NOT NULL DEFAULT TRUE AFTER `type`;
UPDATE `vgr_video` SET boolActive = false;
ALTER TABLE `vgr_video` DROP `status`;

ALTER TABLE `vgr_lostposition` CHANGE `dateCreation` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `vgr_lostposition` ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;


UPDATE message SET type='VGR_PROOF' WHERE type IN ('VGR_PROOF_ACCEPTED', 'VGR_PROOF_REFUSED');
UPDATE message SET type='VGR_PROOF_REQUEST' WHERE type IN ('VGR_REQUEST_ACCEPTED', 'VGR_REQUEST_REFUSED');


RENAME TABLE `t_don` TO `cpt_donation`;
ALTER TABLE `cpt_donation` CHANGE `idDon` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cpt_donation` CHANGE `dateCreation` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `cpt_donation` CHANGE `dateModification` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `cpt_donation` CHANGE `dateDon` `dateDonation` DATE NOT NULL;
ALTER TABLE `cpt_donation` CHANGE `somme` `value` DOUBLE NOT NULL;
ALTER TABLE `cpt_donation` CHANGE `idMembre` `idPlayer` INT(11) NULL DEFAULT NULL;
ALTER TABLE `cpt_donation` ADD `idUser` INT NULL DEFAULT NULL AFTER `idPlayer`;
RENAME TABLE `t_gain` TO `cpt_compta`;
ALTER TABLE `cpt_compta` CHANGE `idGain` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `cpt_compta` CHANGE `mois` `month` VARCHAR(7) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0000-00';
ALTER TABLE `cpt_compta` CHANGE `type` `source` ENUM('DON','ADSENSE','AMAZON.FR','OVH') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `cpt_compta` ADD `type` ENUM('GAIN','BILL') NOT NULL DEFAULT 'GAIN' AFTER `month`;
ALTER TABLE `cpt_compta` ADD `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `value`;
ALTER TABLE `cpt_compta` ADD `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `created_at`;
CREATE TABLE `cpt_compta_source` (
    `id` int(11) NOT NULL,
    `label` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `cpt_compta_source`  ADD PRIMARY KEY (`id`);
ALTER TABLE `cpt_compta_source`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
CREATE TABLE `cpt_compta_type` (
    `id` int(11) NOT NULL,
    `label` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
ALTER TABLE `cpt_compta_type`  ADD PRIMARY KEY (`id`);
ALTER TABLE `cpt_compta_type`  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
INSERT INTO `cpt_compta_source` (`id`, `label`) VALUES (NULL, 'Don'), (NULL, 'Adsense');
INSERT INTO `cpt_compta_type` (`id`, `label`) VALUES (NULL, 'Gain'), (NULL, 'Bill');
ALTER TABLE `cpt_compta` DROP `type`;
ALTER TABLE `cpt_compta` ADD `idType` INT NOT NULL DEFAULT '1' AFTER `month`;
ALTER TABLE `cpt_compta` ADD INDEX idxType (`idType`);
ALTER TABLE `cpt_compta` ADD CONSTRAINT `FK_COMPTA_TYPE` FOREIGN KEY (`idType`) REFERENCES `cpt_compta_type`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `cpt_compta` ADD `idSource` INT NOT NULL DEFAULT '1' AFTER `month`;
ALTER TABLE `cpt_compta` ADD INDEX idxSource (`idSource`);
ALTER TABLE `cpt_compta` ADD CONSTRAINT `FK_COMPTA_SOURCE` FOREIGN KEY (`idSource`) REFERENCES `cpt_compta_source`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
UPDATE `cpt_compta` SET idSource = 2 WHERE `source` = 'ADSENSE';
ALTER TABLE `cpt_compta` DROP `source`;

UPDATE cpt_donation d, vgr_player p
SET d.idUser = p.normandie_user_id
WHERE d.idPlayer = p.id;
ALTER TABLE cpt_donation DROP FOREIGN KEY cpt_donation_ibfk_1;
ALTER TABLE `cpt_donation` DROP `idPlayer`;
ALTER TABLE `cpt_donation` ADD CONSTRAINT `FK_DONATION_USER` FOREIGN KEY (`idUser`) REFERENCES `user`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `vgr_player` CHANGE `pseudo` `pseudo` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';
ALTER TABLE `user` CHANGE `username` `username` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `user` CHANGE `username_canonical` `username_canonical` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

-- BADGES
ALTER TABLE `vgr_player_badge` CHANGE `created_at` `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `vgr_player_badge` CHANGE `updated_at` `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP;