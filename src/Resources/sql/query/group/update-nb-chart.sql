UPDATE vgr_group gr
SET gr.nb_chart = (SELECT COUNT(id) FROM vgr_chart WHERE group_id = gr.id);

