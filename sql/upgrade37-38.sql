ALTER TABLE `plugin` ADD `plugin_priority` tinyint(4) DEFAULT '0' AFTER `plugin_description`; 

UPDATE `settings` SET `db_version` = '38' WHERE `id`=1 LIMIT 1;