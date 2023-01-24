UPDATE vgr_group gr
SET gr.nbChart = (SELECT COUNT(id) FROM vgr_chart WHERE idGroup = gr.id);

