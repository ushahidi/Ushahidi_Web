UPDATE `scheduler` SET `scheduler_controller`='s_feeds' WHERE `id` = '1';
UPDATE `scheduler` SET `scheduler_controller`='s_alerts' WHERE `id` = '2';
UPDATE `scheduler` SET `scheduler_controller`='s_email' WHERE `id` = '3';
UPDATE `scheduler` SET `scheduler_controller`='s_twitter' WHERE `id` = '4';
UPDATE `scheduler` SET `scheduler_controller`='s_sharing' WHERE `id` = '5';

UPDATE `settings` SET `db_version` = '24' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '1.0.1' WHERE `id`=1 LIMIT 1;