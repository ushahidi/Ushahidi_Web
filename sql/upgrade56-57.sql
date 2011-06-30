INSERT INTO `scheduler` (`scheduler_name`,`scheduler_last`,`scheduler_weekday`,`scheduler_day`,`scheduler_hour`,`scheduler_minute`,`scheduler_controller`,`scheduler_active`) VALUES ('Cleanup','0','-1','-1','-1','0','s_cleanup','1');

UPDATE `settings` SET `db_version` = 57 WHERE `id` = 1 LIMIT 1;