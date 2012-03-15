-- Check to ensure that child categories of recently altered parent category are also updated
UPDATE `category` set `parent_id` = '999' where `parent_id` = '5';

UPDATE `settings` SET `db_version` = '78' WHERE `id`=1 LIMIT 1;