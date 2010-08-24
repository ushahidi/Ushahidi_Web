/**
* Table structure for table `sharing`
* 
*/
DROP TABLE IF EXISTS `sharing`;

CREATE TABLE IF NOT EXISTS `sharing` (                                              -- table description
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`sharing_name` VARCHAR(150) NOT NULL,				-- name of the sharing website
	`sharing_url` VARCHAR(255) NOT NULL,				-- main url of the sharing website
	`sharing_color` VARCHAR(20) DEFAULT 'CC0000',		-- color for the map layer selector
	`sharing_active` TINYINT DEFAULT 1 NOT NULL,		-- sharing layer active?
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/**
* Table structure for table `sharing_log`
* 
*/
DROP TABLE IF EXISTS `sharing_incident`;

CREATE TABLE IF NOT EXISTS `sharing_incident` (                                          -- table description
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`sharing_id` INT UNSIGNED NOT NULL,
	`incident_id` INT NOT NULL,							-- remote website incident ID
	`incident_title` VARCHAR(255) NOT NULL,				-- remote incident title
	`latitude` DOUBLE NOT NULL,							-- remote incident latitude
	`longitude` DOUBLE NOT NULL,						-- remote incident longitude
	`incident_date` DATETIME,							-- remote incident date
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

UPDATE `settings` SET `db_version` = '27' WHERE `id`=1 LIMIT 1;
