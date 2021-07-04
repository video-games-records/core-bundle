DELIMITER &&
CREATE PROCEDURE purge_lost_position()
BEGIN

    DECLARE done INT DEFAULT FALSE;
    DECLARE lostposition_id INT;
    DECLARE player_id INT;
    DECLARE chart_id INT;
    DECLARE old_rank INT;
    DECLARE new_rank INT;
    DECLARE nb INT;


    DECLARE cursor1 CURSOR FOR
    SELECT MAX(id) as id,idPlayer,idChart,oldRank,newRank,COUNT(id) as nb
    FROM vgr_lostposition
    GROUP BY idPlayer,idChart,oldRank,newRank
    HAVING COUNT(id) > 1;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    open cursor1;
    read_loop: LOOP
        FETCH cursor1 INTO lostposition_id, player_id, chart_id, old_rank, new_rank, nb;

        IF done THEN
            LEAVE read_loop;
        END IF;

        -- DELETE
        DELETE FROM vgr_lostposition
        WHERE idPlayer = player_id AND idChart = chart_id AND oldRank=old_rank AND newRank=new_rank AND id != lostposition_id;
    END LOOP;
    CLOSE cursor1;

    DELETE vgr_lostposition
    FROM vgr_lostposition
        INNER JOIN vgr_player_chart ON vgr_lostposition.idPlayer = vgr_player_chart.idPlayer AND vgr_lostposition.idChart = vgr_player_chart.idChart
    WHERE (vgr_player_chart.rank <= vgr_lostposition.oldRank)
    OR (vgr_player_chart.rank =1 AND vgr_player_chart.nbEqual = 1 AND vgr_lostposition.oldRank = 0);

END &&

DELIMITER ;

CALL purge_lost_position();
DROP PROCEDURE purge_lost_position;
