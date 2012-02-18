=== About ===
name: Viddler
website: http://www.ushahidi.com
description: Allow users to upload videos to a Viddler account. Before enabling, you must configure the plugin manually in the plugins/viddler/config/viddler.php file.
version: 0.1
requires: 2.1
tested up to: 2.1
author: Brian Herbert
author website: http://www.ushahidi.com

== Description ==
Allow users to upload videos to a Viddler account.

The heavy lifting in this plugin is done by the included Viddler PHP library
that can be found at https://github.com/viddler/phpviddler. This library is
licensed under the MIT License found at /plugins/viddler/ViddlerLicense.txt

== Installation ==
1. Copy the entire /viddler/ directory into your /plugins/ directory.
2. Open /plugins/viddler/config/viddler.php and set the appropriate settings
3. Activate the plugin.

== Changelog ==
0.1
* Created the plugin




CREATE TABLE `viddler` (
`viddler_id` VARCHAR( 16 ) NOT NULL ,
`incident_id` INT NOT NULL ,
`checkin_id` INT NOT NULL ,
`url` VARCHAR( 255 ) NOT NULL ,
`embed` TEXT NOT NULL ,
PRIMARY KEY ( `viddler_id` )
) ENGINE = MYISAM ;