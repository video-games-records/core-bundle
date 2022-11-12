UPDATE vgr_badge b
SET nbPlayer = (
    SELECT COUNT(id)
    FROM vgr_player_badge
    WHERE idBadge = b.id AND ended_at IS NULL
    )