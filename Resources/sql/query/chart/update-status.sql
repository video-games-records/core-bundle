UPDATE vgr_chart SET statusPlayer = 'NORMAL' WHERE id NOT IN (SELECT idChart FROM vgr_player_chart)


UPDATE vgr_chart SET statusPlayer='MAJ' WHERE statusPlayer='goToMaj'

UPDATE vgr_chart SET statusTeam='MAJ' WHERE statusTeam='goToMaj'

UPDATE vgr_chart SET statusPlayer='MAJ',statusTeam='MAJ'  WHERE
id in (SELECT DISTINCT idChart FROM vgr_player_chart WHERE lastUpdate > '2021-08-10')


