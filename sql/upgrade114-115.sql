-- Add Kosovo and South Sudan
INSERT INTO `country` (`id`, `iso`, `country`, `capital`, `cities`) VALUES
(248, 'XK', 'Kosovo', 'Pristina', 0),
(249, 'SS', 'South Sudan', 'Juba', 0);

-- Add feed_geolocation_user as a setting
INSERT into settings(`key`, `value`) values('feed_geolocation_user', '');

-- Update DB Version --
UPDATE `settings` SET `value` = '115' WHERE `key` = 'db_version';