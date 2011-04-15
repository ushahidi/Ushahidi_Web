CREATE TABLE `mhi_log` (
  `id` bigint(20) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `notes` varchar(255) NOT NULL,
  `ip` int(10) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `mhi_log_actions` (
  `id` int(11) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `mhi_log_actions` (`id`, `description`) VALUES
(1, 'Logged in'),
(2, 'Logged out'),
(3, 'Created a deployment.'),
(4, 'Disabled a deployment.'),
(5, 'Password reset.'),
(6, 'New user created.'),
(7, 'Updated account information.');

UPDATE `settings` SET `db_version` = '25' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `ushahidi_version` = '1.0.1' WHERE `id`=1 LIMIT 1;