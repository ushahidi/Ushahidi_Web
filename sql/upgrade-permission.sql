/*
 * Create new permissions tabls
 */

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=16 COMMENT='Stores permissions used for access control';

/* Add existing permissions */
INSERT IGNORE INTO `permissions` VALUES 
(1,'reports_view'),
(2,'reports_edit'),
(3,'reports_evaluation'),
(4,'reports_comments'),
(5,'reports_download'),
(6,'reports_upload'),
(7,'messages'),
(8,'messages_reporters'),
(9,'stats'),
(10,'settings'),
(11,'manage'),
(12,'users'),
(13,'manage_roles'),
(14,'checkin'),
(15,'checkin_admin');

CREATE TABLE IF NOT EXISTS `roles_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`)
) ENGINE=MyISAM COMMENT='Stores permissions assigned to roles';

/* Grab existing permissions-roles matches */
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 1 as permission_id FROM roles WHERE reports_view = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 2 as permission_id FROM roles WHERE reports_edit = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 3 as permission_id FROM roles WHERE reports_evaluation = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 4 as permission_id FROM roles WHERE reports_comments = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 5 as permission_id FROM roles WHERE reports_download = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 6 as permission_id FROM roles WHERE reports_upload = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 7 as permission_id FROM roles WHERE messages = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 8 as permission_id FROM roles WHERE messages_reporters = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 9 as permission_id FROM roles WHERE stats = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 10 as permission_id FROM roles WHERE settings = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 11 as permission_id FROM roles WHERE manage = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 12 as permission_id FROM roles WHERE users = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 13 as permission_id FROM roles WHERE manage_roles = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 14 as permission_id FROM roles WHERE checkin = 1;
INSERT IGNORE INTO roles_permissions (role_id, permission_id) SELECT id as role_id, 15 as permission_id FROM roles WHERE checkin_admin = 1;

/* Backup old roles tables and then remove fields */

