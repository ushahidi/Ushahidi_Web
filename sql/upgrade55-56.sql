UPDATE `settings` SET `blocks` = replace(`blocks`, ";", "|");

UPDATE `settings` SET `db_version` = 56 WHERE `id` = 1 LIMIT 1;