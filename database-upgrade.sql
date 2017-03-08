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
RENAME TABLE VGR_etatrecord TO vgr_player_chart_status;
RENAME TABLE t_pays TO country;
RENAME TABLE t_email TO email;
RENAME TABLE t_membre TO vgr_player;
RENAME TABLE t_team TO vgr_team;
RENAME TABLE mv_team_record TO vgr_team_chart;
RENAME TABLE mv_team_groupe TO vgr_team_group;
RENAME TABLE mv_team_jeu TO vgr_team_game;

ALTER TABLE `vgr_player` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL AUTO_INCREMENT, CHANGE `idPays` `idPays` INT(11) NULL DEFAULT NULL;
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

--
-- VGR Part
--

-- Series
ALTER TABLE vgr_serie CHANGE idSerie id INT AUTO_INCREMENT NOT NULL;
CREATE TABLE vgr_serie_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_B355773C2C2AC5D3 (translatable_id), UNIQUE INDEX serie_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
-- Transfert des données
INSERT INTO vgr_serie_translation (translatable_id, name, locale) SELECT id, libSerie, 'fr' FROM vgr_serie;
INSERT INTO vgr_serie_translation (translatable_id, name, locale) SELECT id, libSerie, 'en' FROM vgr_serie;
ALTER TABLE vgr_serie DROP libSerie;

-- Games
CREATE TABLE vgr_game_translation (id INT AUTO_INCREMENT NOT NULL, translatable_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, locale VARCHAR(255) NOT NULL, INDEX IDX_6A3C076D2C2AC5D3 (translatable_id), UNIQUE INDEX game_translation_unique_translation (translatable_id, locale), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
ALTER TABLE `vgr_game` CHANGE `idJeu` `id` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_game` CHANGE `imageJeu` `picture` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_game` CHANGE `nbMembre` `nbPlayer` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `boolDLC` `boolDlc` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game` CHANGE `statut` `status` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_game` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_game` CHANGE dateModification updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_game` DROP `imagePlateForme`;
-- Transfert des données
INSERT INTO vgr_game_translation (translatable_id, name, locale) SELECT id, libJeu_fr, 'fr' FROM vgr_game;
INSERT INTO vgr_game_translation (translatable_id, name, locale) SELECT id, libJeu_en, 'en' FROM vgr_game;
ALTER TABLE vgr_game DROP libJeu_fr, DROP libJeu_en;

-- Groups
ALTER TABLE `vgr_group` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_group` CHANGE `libGroupe_fr` `libGroupFr` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_group` CHANGE `libGroupe_en` `libGroupEn` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_group` CHANGE `idJeu` `idGame` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `boolDLC` `boolDlc` TINYINT(1) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE `nbMembre` `nbPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_group` CHANGE nbPost nbPost INT NOT NULL;
ALTER TABLE `vgr_group` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_group` CHANGE dateModification updated_at DATETIME DEFAULT NULL;

-- Charts
ALTER TABLE `vgr_chart` CHANGE `idRecord` `idChart` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_chart` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `libRecord_fr` `libChartFr` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
ALTER TABLE `vgr_chart` CHANGE `libRecord_en` `libChartEn` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `statut` `statusPlayer` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_chart` CHANGE `statutTeam` `statusTeam` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `vgr_chart` CHANGE dateCreation created_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_chart` CHANGE dateModification updated_at DATETIME DEFAULT NULL;
ALTER TABLE `vgr_chart` CHANGE statusPlayer statusPlayer VARCHAR(255) NOT NULL, CHANGE statusTeam statusTeam VARCHAR(255) NOT NULL, CHANGE nbPost nbPost INT NOT NULL;

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
ALTER TABLE `vgr_player_chart` ADD INDEX `idxPlayerDateModif` (`idPlayer`, `dateModif`);

ALTER TABLE `vgr_player_chartlib` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_player_chartlib` CHANGE `idLibRecord` `idLibChart` INT(11) NOT NULL;

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

ALTER TABLE `vgr_player_group` CHANGE `idMembre` `idPlayer` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `rank` `rankPoint` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `nbRecord` `nbChart` INT(11) NOT NULL;
ALTER TABLE `vgr_player_group` CHANGE `nbRecordProuve` `nbChartProven` INT(11) NOT NULL;

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
ALTER TABLE `vgr_platform` CHANGE `idPlateforme` `idPlatform` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_platform` CHANGE `libPlateforme` `libPlatform` VARCHAR(50) NOT NULL;
ALTER TABLE `vgr_platform` CHANGE `statut` `status` ENUM('ACTIF','INACTIF') NOT NULL DEFAULT 'INACTIF';
ALTER TABLE `vgr_platform` CHANGE `image` `picture` VARCHAR(30) NOT NULL DEFAULT '';
ALTER TABLE `vgr_platform` CHANGE `classPlateforme` `class` VARCHAR(30) NOT NULL;

ALTER TABLE `vgr_game_platform` CHANGE `idJeu` `idGame` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_game_platform` CHANGE `idPlateForme` `idPlatform` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `vgr_player_chart_status` CHANGE `idEtat` `idStatus` INT(11) NOT NULL AUTO_INCREMENT;
ALTER TABLE `vgr_player_chart_status` CHANGE `libEtat` `libStatus` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

-- Team
ALTER TABLE `vgr_team` CHANGE `statut` `status` ENUM('OPEN','CLOSED') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'OPEN';
ALTER TABLE `vgr_team` CHANGE `nbMembre` `nbPlayer` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_team` CHANGE `vgr_pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_pointBadge` `pointBadge` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `vgr_team` CHANGE `vgr_rank0` `chartRank0` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank1` `chartRank1` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank2` `chartRank2` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank3` `chartRank3` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_point` `rankPointChart` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_medal` `rankMedal` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_badge` `rankBadge` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_cup` `rankCup` INT(11) NULL DEFAULT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank0` `gameRank0` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank1` `gameRank1` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank2` `gameRank2` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_cup_rank3` `gameRank3` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_nbMasterBadge` `nbMasterBadge` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_pointJeu` `pointGame` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `vgr_rank_pointJeu` `rankPointGame` INT(11) NOT NULL;
ALTER TABLE `vgr_team` CHANGE `dateCreation` `created_at` DATETIME NOT NULL;
ALTER TABLE `vgr_team` CHANGE `dateModification` `updated_at` DATETIME NOT NULL;

ALTER TABLE `vgr_team_chart` CHANGE `idRecord` `idChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_chart` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;

ALTER TABLE `vgr_team_group` CHANGE `idGroupe` `idGroup` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` CHANGE `rank` `rankPoint` INT(11) NOT NULL;
ALTER TABLE `vgr_team_group` ADD `rankMedal` INT NOT NULL AFTER `rankPoint`;

ALTER TABLE `vgr_team_game` CHANGE `idJeu` `idGame` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `pointJeu` `pointGame` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `pointRecord` `pointChart` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` CHANGE `rank` `rankPoint` INT(11) NOT NULL;
ALTER TABLE `vgr_team_game` ADD `rankMedal` INT NOT NULL AFTER `rankPoint`;


--
-- Members
--
CREATE TABLE member_group (userId INT NOT NULL, groupId INT NOT NULL, INDEX IDX_FE1D13664B64DCC (userId), INDEX IDX_FE1D136ED8188B0 (groupId), PRIMARY KEY(userId, groupId)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
CREATE TABLE groupRole (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', UNIQUE INDEX UNIQ_39A2D4D75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

CREATE TABLE member (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT '(DC2Type:array)', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, nbConnexion INT NOT NULL, locale VARCHAR(2) DEFAULT NULL, firstName VARCHAR(255) DEFAULT NULL, lastName VARCHAR(255) DEFAULT NULL, address LONGTEXT DEFAULT NULL, birthDate DATE DEFAULT NULL, gender VARCHAR(1) DEFAULT NULL, timeZone INT DEFAULT NULL, personalWebsite VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, googleplus VARCHAR(255) DEFAULT NULL, youtube VARCHAR(255) DEFAULT NULL, dailymotion VARCHAR(255) DEFAULT NULL, twitch VARCHAR(255) DEFAULT NULL, skype VARCHAR(255) DEFAULT NULL, snapchat VARCHAR(255) DEFAULT NULL, pinterest VARCHAR(255) DEFAULT NULL, trumblr VARCHAR(255) DEFAULT NULL, blogger VARCHAR(255) DEFAULT NULL, reddit VARCHAR(255) DEFAULT NULL, deviantart VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, idPays INT DEFAULT NULL, UNIQUE INDEX UNIQ_70E4FA7892FC23A8 (username_canonical), UNIQUE INDEX UNIQ_70E4FA78A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_70E4FA78C05FB297 (confirmation_token), INDEX IDX_70E4FA7847626230 (idPays), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE = InnoDB;
ALTER TABLE member ADD CONSTRAINT FK_70E4FA7847626230 FOREIGN KEY (idPays) REFERENCES country (id);
ALTER TABLE member_group ADD CONSTRAINT FK_FE1D13664B64DCC FOREIGN KEY (userId) REFERENCES member (id);
ALTER TABLE member_group ADD CONSTRAINT FK_FE1D136ED8188B0 FOREIGN KEY (groupId) REFERENCES groupRole (id);

-- New id for link between normandie & vgr
ALTER TABLE vgr_player ADD normandie_user_id INT DEFAULT NULL;

-- Procedure to migrate member
DELIMITER &&
CREATE PROCEDURE member_migrate()
BEGIN
  DECLARE done, locked INT DEFAULT FALSE;
  DECLARE duplicateIncrement INT DEFAULT 100;
  DECLARE member_id, vgr_member_id, pays, nb_connection INT;
  DECLARE userName varchar(180) CHARSET utf8;
  DECLARE gender varchar(1);
  DECLARE birthdate date;
  DECLARE userDateCreation, userDateModification, userDerniereConnexion datetime;
  DECLARE v_email, nom, prenom, siteWeb, statutCompte, sexe varchar(255);
  DECLARE cur1 CURSOR FOR SELECT idUser, pseudo, email, nom, prenom, dateNaissance, nbConnexion, siteWeb, statutCompte,
                            dateCreation, dateModification, derniereConnexion, sexe, idPays
                            /*, MSN, presentation, nbForumMessage, nbCommentaire, boolTeam, boolNewsletter, boolAssoc,
                            boolShowFbLikeBox, boolNotifCommentaire, signature, dateFormat, utcFormat, mailSending, don,
                            idLangue, idLangueForum, idRang, idStatut, idTeam*/
                          FROM vgr_player;
  -- Handler for duplicate email
  DECLARE CONTINUE HANDLER FOR 1062
    BEGIN
      -- Log for duplicate email
      SELECT CONCAT('Duplicate email for: ', v_email);
      SET duplicateIncrement = duplicateIncrement + 1;
      SET v_email = duplicateIncrement;
      SET locked = TRUE;
      -- Retry with new mail
      INSERT INTO member (username, username_canonical, password, email, email_canonical, firstName, lastName, birthDate,
                          enabled, locked, expired, credentials_expired, salt, roles, nbConnexion, personalWebsite, gender,
                          created_at, updated_at, last_login, idPays, confirmation_token, password_requested_at)
      VALUES
        (userName, userName, "", v_email, v_email, prenom, nom, birthdate, false, locked, false, true,
         MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), 'a:0:{}', nb_connection, siteWeb, gender,
         userDateCreation, userDateModification, userDerniereConnexion, pays,
         MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), NOW()
        );
    END;

  -- Handler for finishing the loop
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur1;
  read_loop: LOOP
    FETCH cur1 INTO vgr_member_id, userName, v_email, nom, prenom, birthdate, nb_connection, siteWeb, statutCompte,
      userDateCreation, userDateModification, userDerniereConnexion, sexe, pays;
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

    INSERT INTO member (username, username_canonical, password, email, email_canonical, firstName, lastName, birthDate,
              enabled, locked, expired, credentials_expired, salt, roles, nbConnexion, personalWebsite, gender,
              created_at, updated_at, last_login, idPays, confirmation_token, password_requested_at)
    VALUES
      (userName, userName, "", v_email, v_email, prenom, nom, birthdate, false, locked, false, true,
       MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), 'a:0:{}', nb_connection, siteWeb, gender,
       userDateCreation, userDateModification, userDerniereConnexion, pays,
       MD5(CONCAT(LEFT(UUID(),8), LEFT(UUID(),8), LEFT(UUID(),8))), NOW()
      );
    SET member_id = LAST_INSERT_ID();
    UPDATE vgr_player SET normandie_player_id = member_id WHERE idUser = vgr_member_id;
  END LOOP;
  CLOSE cur1;
END&&

DELIMITER ;

CALL member_migrate();
DROP PROCEDURE member_migrate;

ALTER TABLE vgr_player DROP password, DROP email, DROP confirm_email, DROP nom, DROP prenom, DROP dateNaissance,
DROP statutCompte, DROP siteWeb, DROP nbConnexion, DROP derniereConnexion, DROP sexe, DROP dateCreation, DROP dateModification
/*DROP MSN, DROP presentation,
DROP nbForumMessage, DROP nbCommentaire, DROP boolTeam, DROP boolContact, DROP boolNewsletter, DROP boolAssoc,
DROP boolShowFbLikeBox, DROP boolNotifCommentaire, DROP signature, DROP dateFormat, DROP utcFormat, DROP mailSending,
DROP don, DROP idLangue, DROP idPays, DROP idLangueForum, DROP idRang, DROP idStatut, DROP idTeam*/;
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
CHANGE vgr_collection collection INT DEFAULT NULL;
