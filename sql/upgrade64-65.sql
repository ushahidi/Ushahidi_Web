ALTER TABLE `location` MODIFY `country_id` int(11) NOT NULL default '0';

UPDATE `settings` SET `db_version` = '65' WHERE `id`=1 LIMIT 1;
