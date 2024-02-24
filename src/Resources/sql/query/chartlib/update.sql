UPDATE vgr_chartlib,vgr_chart
SET idType=124, vgr_chartlib.name = 'Feet and Inches'
WHERE vgr_chartlib.idChart = vgr_chart.id
AND vgr_chart.idGroup = 23422;
