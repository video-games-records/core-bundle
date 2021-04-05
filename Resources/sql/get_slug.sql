SET GLOBAL log_bin_trust_function_creators = 1;
DROP FUNCTION IF EXISTS get_slug;
DELIMITER $$
CREATE FUNCTION `get_slug`(`label` VARCHAR(255) CHARSET utf8) RETURNS varchar(255) CHARSET utf8
BEGIN
    DECLARE slug VARCHAR(255);
    SET slug = lower(label);
    SET slug = replace(slug, '.', ' ');
    SET slug = replace(slug, ',', ' ');
    SET slug = replace(slug, ';', ' ');
    SET slug = replace(slug, ':', ' ');
    SET slug = replace(slug, '?', ' ');
    SET slug = replace(slug, '%', ' ');
    SET slug = replace(slug, '&', ' ');
    SET slug = replace(slug, '#', ' ');
    SET slug = replace(slug, '*', ' ');
    SET slug = replace(slug, '!', ' ');
    SET slug = replace(slug, '_', ' ');
    SET slug = replace(slug, '@', ' ');
    SET slug = replace(slug, '+', ' ');
    SET slug = replace(slug, '(', ' ');
    SET slug = replace(slug, ')', ' ');
    SET slug = replace(slug, '[', ' ');
    SET slug = replace(slug, ']', ' ');
    SET slug = replace(slug, '/', ' ');
    SET slug = replace(slug, '-', ' ');
    SET slug = replace(slug, '\'', '');
    SET slug = trim(slug);
    SET slug = replace(slug, ' ', '-');
    SET slug = replace(slug, '--', '-');
    SET slug = replace(slug, '--', '-');
    return slug;
END$$
DELIMITER ;