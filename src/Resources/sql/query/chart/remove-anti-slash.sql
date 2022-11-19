-- select
SELECT * FROM `vgr_chart` WHERE `libChartEn` LIKE '%\\\\''%';

-- update
UPDATE vgr_chart SET libChartEn = replace(libChartEn, '\\', '')
WHERE `libChartEn` LIKE '%\\\\''%';
UPDATE vgr_chart SET slug=get_slug(libChartEn);