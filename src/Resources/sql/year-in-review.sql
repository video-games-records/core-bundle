SET @date1 = '2022-01-01';
SET @date2 = '2022-12-31';

--
SELECT COUNT(DISTINCT id) FROM `dwh_player` WHERE nbPostDay > 0 AND `date` between @date1 AND @date2;

--
SELECT SUM(nbPostDay) FROM `dwh_player` WHERE nbPostDay > 0 AND `date` between @date1 AND @date2;

--
SELECT COUNT(*) as nb FROM vgr.vgr_player_chart WHERE `created_at` between @date1  AND @date2;

--
SELECT vgr_player.id, pseudo, SUM(nbPostDay) as nb
FROM `dwh_player` INNER JOIN vgr.vgr_player ON dwh_player.id = vgr.vgr_player.id
WHERE `date` between @date1 AND @date2
GROUP BY vgr_player.id
ORDER BY SUM(nbPostDay) DESC;

--
SELECT vgr_game.id, libGameEn, SUM(nbPostDay) as nb
FROM `dwh_game` INNER JOIN vgr.vgr_game ON dwh_game.id = vgr.vgr_game.id
WHERE `date` between @date1 AND @date2
GROUP BY vgr_game.id
ORDER BY SUM(nbPostDay) DESC;

--
SELECT SUM(nbPost) FROM `dwh_player` WHERE `date` = @date2

[1] players posted / edited [2] scores

There was [3] new submitions

Top 10 players
[4]


Top 10 games
[5]


At the end of the year, we have [6] scores