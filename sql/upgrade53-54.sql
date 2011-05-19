/**
* Add new column to Alert table. Associates alert to user
*/
ALTER TABLE `alert` ADD `user_id` int(11) DEFAULT '0'  AFTER `id`;

/**
* Create new Member Role
*/
INSERT INTO `roles` (`name`,`description`, `reports_view`, `reports_edit`, `reports_evaluation`, `reports_comments`, `reports_download`, `reports_upload`, `messages`, `messages_reporters`, `stats`, `settings`, `manage`, `users`, `manage_roles`, `checkin`, `checkin_admin`) VALUES
('member','Regular user with access only to the member area', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

/**
* Table structure for table `openid`
*/
CREATE TABLE `openid` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `openid` varchar(255) NOT NULL,
  `openid_email` varchar(127) NOT NULL,
  `openid_server` varchar(255) NOT NULL,
  `openid_date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `openid` (`openid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

UPDATE `settings` SET `db_version` = '54' WHERE `id`=1 LIMIT 1;