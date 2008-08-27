<div class="box">

<h1>Kohana Auth Module - Installation Instructions &amp; Demo</h1>

<p>The following tables must be installed in your database: <code>users</code>, <code>roles</code>, <code>roles_users</code>, and <code>user_tokens</code>.</p>

<p><code>pending_users</code> is also required if your sign-up / registration process will include a confirmation email.</p>

<p>If you have not already installed these tables, please run the installation query below.</p>

<p>After the tables have been installed, <?php echo html::anchor('auth_demo/create', 'create a user') ?> or <?php echo html::anchor('auth_demo/register', 'register') ?>.</p>

<p><em>This query is MySQL-specific, but should be easy to adapt to a database that supports foreign keys.</em></p>

<?php echo form::textarea('query', <<<EOF
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

INSERT INTO `roles` (`id`, `name`, `description`) VALUES(1, 'login', 'Login privileges, granted after account confirmation');
INSERT INTO `roles` (`id`, `name`, `description`) VALUES(2, 'admin', 'Administrative user, has access to everything.');

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `email` varchar(127) NOT NULL,
  `username` varchar(31) NOT NULL default '',
  `password` char(50) NOT NULL,
  `logins` int(10) unsigned NOT NULL default '0',
  `last_login` int(10) unsigned NULL,
  `updated` timestamp NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(32) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `pending_users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `key` varchar(32) NOT NULL,
  `email` varchar(127) NOT NULL,
  `username` varchar(31) NOT NULL default '',
  `password` char(50) NULL,
  `created` timestamp NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

-- if you want to store sessions in the db

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
EOF
, 'style="width:90%;height:30em;padding:0.5em"') ?>

</div>
