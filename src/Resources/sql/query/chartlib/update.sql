UPDATE vgr_chartlib,vgr_chart
SET type_id=6
WHERE vgr_chartlib.chart_id = vgr_chart.id
AND vgr_chart.group_id = 23930
AND vgr_chart.created_at >= '2025-04-05';

SELECT * from vgr_chartlib,vgr_chart
WHERE vgr_chartlib.chart_id = vgr_chart.id
AND vgr_chart.group_id = 23930
AND vgr_chart.created_at >= '2025-04-05';
