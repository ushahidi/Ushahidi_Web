/**
* Add new column to Alert table. Associates alert to user
*/
ALTER TABLE `alert` ADD `user_id` int(11) DEFAULT '0'  AFTER `id`;

/**
* Add new column to Ratings table. Associates alert to user
*/
ALTER TABLE `rating` ADD `user_id` int(11) DEFAULT '0'  AFTER `id`;

/**
* Add new column to Checkin table. Associates checkin to incident
*/
ALTER TABLE `checkin` ADD `incident_id` int(11) NULL DEFAULT '0'  AFTER `location_id`;


/**
* Create new Member Role
*/
INSERT INTO `roles` (`name`,`description`, `reports_view`, `reports_edit`, `reports_evaluation`, `reports_comments`, `reports_download`, `reports_upload`, `messages`, `messages_reporters`, `stats`, `settings`, `manage`, `users`, `manage_roles`, `checkin`, `checkin_admin`) VALUES
('member','Regular user with access only to the member area', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

/**
* Table structure for table `openid`
*/
CREATE TABLE IF NOT EXISTS `openid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `openid_email` varchar(127) NOT NULL,
  `openid_server` varchar(255) NOT NULL,
  `openid_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/**
* Table structure for table `private_message`
*/
CREATE TABLE IF NOT EXISTS `private_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL,
  `from_user_id` int(11) DEFAULT '0',
  `private_subject` varchar(255) NOT NULL,
  `private_message` text NOT NULL,
  `private_message_date` datetime NOT NULL,
  `private_message_new` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/**
* Add new columns for blocks!
*/
ALTER TABLE `settings` ADD `blocks` text  AFTER `twitter_hashtags`;
ALTER TABLE `settings` ADD `blocks_per_row` tinyint NOT NULL DEFAULT '2'  AFTER `blocks`;
UPDATE `settings` SET `blocks`='reports_block|news_block' WHERE `id` = '1';


UPDATE `settings` SET `db_version` = 55 WHERE `id` = 1 LIMIT 1;