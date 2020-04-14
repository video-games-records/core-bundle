-- nbPost
SELECT SUM(nbPostJour) FROM `membre` WHERE nbPostJour > 0 AND `date` between '2020-03-01' AND '2020-03-31'

-- nbJeu
SELECT COUNT(idJeu) FROM `vgr_jeu` WHERE `dateActivation` between '2020-03-01' AND '2020-03-31'

-- nbPreuve
SELECT COUNT(idPreuve) FROM `vgr_preuves` WHERE `dateAjout` between '2020-03-01' AND '2020-03-31'

