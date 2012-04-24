-- Altering table structures for the following tables based on recommendations from http://lopad.org/6aOddOZx72

-- actions_log
ALTER TABLE `actions_log` ADD INDEX `action_id` (`action_id`);

-- alert
ALTER TABLE  `alert` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT  '0';
ALTER TABLE  `alert` ADD INDEX  `user_id` (  `user_id` );

-- alert_category
ALTER TABLE  `alert_category` CHANGE  `alert_id`  `alert_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `alert_category` CHANGE  `category_id`  `category_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL;

-- alert_sent
ALTER TABLE  `alert_sent` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NOT NULL;
ALTER TABLE  `alert_sent` CHANGE  `alert_id`  `alert_id` BIGINT( 20 ) UNSIGNED NOT NULL;

ALTER TABLE `alert_sent` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE `alert_sent` ADD INDEX  `alert_id` (  `alert_id` );

-- badge_users
ALTER TABLE  `badge_users` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL;

-- category
ALTER TABLE  `category` DROP  `category_type` ,
DROP  `category_image_shadow` ;

-- category_lang
ALTER TABLE  `category_lang` CHANGE  `category_id`  `category_id` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE  `category_lang` ADD INDEX  `category_id` (  `category_id` );

-- checkin
ALTER TABLE  `checkin` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE  `checkin` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT  '0';
ALTER TABLE  `checkin` CHANGE  `location_id`  `location_id` BIGINT( 20 ) UNSIGNED NOT NULL;

ALTER TABLE `checkin` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE `checkin` ADD INDEX  `user_id` (  `user_id` );
ALTER TABLE `checkin` ADD INDEX  `location_id` (  `location_id` );

-- city
ALTER TABLE  `city` ADD INDEX  `country_id` (  `country_id` );

-- cluster
ALTER TABLE  `cluster` DROP  `incident_title` ,
DROP  `incident_date` ,
DROP  `category_color` ;

ALTER TABLE  `cluster` CHANGE  `location_id`  `location_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `cluster` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `cluster` ADD INDEX  `location_id` (  `location_id` );
ALTER TABLE  `cluster` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE  `cluster` ADD INDEX  `category_id` (  `category_id` );

-- comment
ALTER TABLE  `comment` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `comment` CHANGE  `checkin_id`  `checkin_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `comment` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT  '0';

ALTER TABLE  `comment` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE  `comment` ADD INDEX  `checkin_id` (  `checkin_id` );
ALTER TABLE  `comment` ADD INDEX  `user_id` (  `user_id` );

ALTER TABLE  `comment` DROP  `comment_rating`;

-- feed_item
ALTER TABLE  `feed_item` CHANGE  `feed_id`	`feed_id` int(11) unsigned NOT NULL;	
ALTER TABLE  `feed_item` CHANGE  `location_id`  `location_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT  '0';
ALTER TABLE  `feed_item` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `feed_item` ADD INDEX  `feed_id` (  `feed_id` );
ALTER TABLE  `feed_item` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE  `feed_item` ADD INDEX  `location_id` (  `location_id` );

-- form_field_option
ALTER TABLE  `form_field_option` ADD INDEX  `form_field_id` (  `form_field_id` );

-- form_response
ALTER TABLE  `form_response` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NOT NULL;
ALTER TABLE  `form_response` ADD INDEX  `incident_id` (  `incident_id` );


-- geometry
ALTER TABLE  `geometry` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NOT NULL;
ALTER TABLE  `geometry` ADD INDEX  `incident_id` (  `incident_id` );

-- incident
ALTER TABLE  `incident` CHANGE  `location_id`  `location_id` BIGINT( 20 ) UNSIGNED NOT NULL;
ALTER TABLE  `incident` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE  `incident` ADD INDEX  `form_id` (  `form_id` );
ALTER TABLE  `incident` ADD INDEX  `user_id` (  `user_id` );

ALTER TABLE `incident` DROP `incident_rating`;


-- incident_category
ALTER TABLE  `incident_category` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NOT NULL DEFAULT  '0';
ALTER TABLE  `incident_category` CHANGE  `category_id`  `category_id` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '5';

-- incident_lang
ALTER TABLE  `incident_lang` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NOT NULL;
ALTER TABLE  `incident_lang` ADD INDEX  `incident_id` (  `incident_id` );

-- incident_person
ALTER TABLE  `incident_person` DROP  `location_id`;
ALTER TABLE  `incident_person` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `incident_person` ADD INDEX  `incident_id` (  `incident_id` );

-- location
ALTER TABLE  `location` ADD INDEX  `country_id` (  `country_id` );

-- maintenance
ALTER TABLE  `maintenance` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;

-- media
ALTER TABLE  `media` CHANGE  `location_id`  `location_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `media` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `media` CHANGE  `checkin_id`  `checkin_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `media` CHANGE  `message_id`  `message_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;

ALTER TABLE  `media` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE  `media` ADD INDEX  `location_id` (  `location_id` );
ALTER TABLE  `media` ADD INDEX  `checkin_id` (  `checkin_id` );
ALTER TABLE  `media` ADD INDEX  `badge_id` (  `badge_id` );
ALTER TABLE  `media` ADD INDEX  `message_id` (  `message_id` );

-- message
ALTER TABLE  `message` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT  '0';
ALTER TABLE  `message` CHANGE  `reporter_id`  `reporter_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `message` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT  '0';

ALTER TABLE  `message` ADD INDEX  `user_id` (  `user_id` );
ALTER TABLE  `message` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE  `message` ADD INDEX  `reporter_id` (  `reporter_id` );

-- openid
ALTER TABLE  `openid` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE  `openid` ADD INDEX  `user_id` (  `user_id` );

-- private_message
ALTER TABLE  `private_message` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE  `private_message` ADD INDEX  `user_id` (  `user_id` );

-- rating
ALTER TABLE  `rating` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT  '0';
ALTER TABLE  `rating` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `rating` CHANGE  `comment_id`  `comment_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;


ALTER TABLE  `rating` ADD INDEX  `user_id` (  `user_id` );
ALTER TABLE  `rating` ADD INDEX  `incident_id` (  `incident_id` );
ALTER TABLE  `rating` ADD INDEX  `comment_id` (  `comment_id` );

-- reporter
ALTER TABLE  `reporter` DROP  `incident_id` ,
DROP  `service_userid` ;

ALTER TABLE  `reporter` CHANGE  `location_id`  `location_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `reporter` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `reporter` CHANGE  `service_id`  `service_id` INT( 10 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE  `reporter` CHANGE  `level_id`  `level_id` INT( 11 ) UNSIGNED NULL DEFAULT NULL;


ALTER TABLE  `reporter` ADD INDEX  `user_id` (  `user_id` );
ALTER TABLE  `reporter` ADD INDEX  `location_id` (  `location_id` );
ALTER TABLE  `reporter` ADD INDEX  `service_id` (  `service_id` );
ALTER TABLE  `reporter` ADD INDEX  `level_id` (  `level_id` );

-- service
UPDATE `service` SET  `service_description` =  'Email messages sent to your deployment' WHERE  `service`.`id` =2;

-- scheduler log
ALTER TABLE  `scheduler_log` DROP  `scheduler_name` ;

ALTER TABLE  `scheduler_log` CHANGE  `scheduler_id`  `scheduler_id` INT( 10 ) UNSIGNED NOT NULL;
ALTER TABLE  `scheduler_log` ADD INDEX  `scheduler_id` (  `scheduler_id` );

-- user_devices
ALTER TABLE  `user_devices` CHANGE  `user_id`  `user_id` INT( 11 ) UNSIGNED NOT NULL;
ALTER TABLE  `user_devices` ADD INDEX  `user_id` (  `user_id` );

-- verified
ALTER TABLE `verified` DROP `verified_comment`;
ALTER TABLE `verified` CHANGE  `incident_id`  `incident_id` BIGINT( 20 ) UNSIGNED NULL DEFAULT NULL;
ALTER TABLE `verified` ADD INDEX  `incident_id` (  `incident_id` );

UPDATE `settings` SET `db_version` = '80' WHERE `id` = 1 LIMIT 1;