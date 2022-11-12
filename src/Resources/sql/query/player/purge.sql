-- User with 0 Connexion
SELECT
    username,
    email,
    created_at,
    nbChart,
    nbConnexion
FROM user INNER JOIN vgr_player ON user.id = vgr_player.normandie_user_id
WHERE nbConnexion = 0
AND last_login IS NULL
AND created_at < '2021-01-01'
AND nbChart > 0
AND nbForumMessage = 0



UPDATE user, vgr_player
SET nbConnexion = 1
WHERE user.id = vgr_player.normandie_user_id
AND nbConnexion = 0
AND nbChart != 0


SELECT
    user.id,
    username,
    email,
    created_at,
    last_login,
    nbChart,
    nbConnexion
FROM user INNER JOIN vgr_player ON user.id = vgr_player.normandie_user_id
WHERE nbConnexion = 0
AND (nbChart > 0 OR nbForumMessage > 0)

