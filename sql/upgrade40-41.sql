/**
* Table structure for table 'alert_category'
*
*/
CREATE TABLE IF NOT EXISTS `alert_category` (
  `id` int(11) NOT NULL auto_increment,
  `alert_id` int(11),
  `category_id` int(11),
  PRIMARY KEY (`id`),
  KEY `alert_id` (`alert_id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

/*
* Add a trigger to clean up when the alert is deleted
*/

delimiter |

CREATE TRIGGER ac_cleanup BEFORE DELETE ON `alert`
  FOR EACH ROW BEGIN
    DELETE FROM `alert_category` WHERE `alert_id` = OLD.id;
  END;
|

delimiter ;

UPDATE `settings` SET `db_version` = '41' WHERE `id`=1 LIMIT 1;