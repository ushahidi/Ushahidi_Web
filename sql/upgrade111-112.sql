/**
 * Table structure for table `feed_item_category`
 *
 */

CREATE TABLE IF NOT EXISTS `feed_item_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `feed_item_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `category_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `feed_item_category_ids` (`feed_item_id`,`category_id`),
  KEY `feed_item_id` (`feed_item_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='Stores fetched feed items categories' AUTO_INCREMENT=1 ;

-- UPDATE db_version
UPDATE `settings` SET `value` = 112 WHERE `key` = 'db_version';
