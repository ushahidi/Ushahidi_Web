-- Ushahidi Engine
-- version 60
-- http://www.ushahidi.com


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


-- --------------------------------------------------------

/**
 * Table structure for table `actions`
 * 
 */

CREATE TABLE `actions` (
	`action_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`action` VARCHAR( 75 ) NOT NULL ,
	`qualifiers` TEXT NOT NULL ,
	`response` VARCHAR( 75 ) NOT NULL ,
	`response_vars` TEXT NOT NULL,
	`active` TINYINT NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/**
 * Table structure for table `actions_log`
 * 
 */

CREATE TABLE `actions_log` (
`id` BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`action_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`time` INT( 10 ) NOT NULL
) ENGINE = MYISAM DEFAULT CHARSET=utf8;

/**
 * Table structure for table `badge`
 * 
 */

CREATE TABLE `badge` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 250 ) NOT NULL ,
`description` TEXT NOT NULL
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

/**
 * Table structure for table `badge_assignment`
 * 
 */

CREATE TABLE `badge_users` (
`user_id` INT NOT NULL ,
`badge_id` INT NOT NULL ,
PRIMARY KEY ( `user_id` , `badge_id` )
) ENGINE=MYISAM DEFAULT CHARSET=utf8;

/**
 * Table structure for table `category`
 * 
 */

CREATE TABLE IF NOT EXISTS `category` (                                             
    `id` int(11) unsigned NOT NULL auto_increment,                                  
    `parent_id` INT NOT NULL DEFAULT '0',                                           
    `locale` varchar(10) NOT NULL default 'en_US',                                  
    `category_type` tinyint(4) default NULL,
	`category_position` tinyint(4) NOT NULL DEFAULT '0',
    `category_title` varchar(255) default NULL,                                     
    `category_description` text default NULL,                                       
    `category_color` varchar(20) default NULL,                                      
    `category_image` varchar(100) default NULL,                                     
    `category_image_thumb` varchar(100) default NULL,                               
    `category_image_shadow` varchar(100) default NULL,                              
    `category_visible` tinyint(4) NOT NULL default '1',                             
    `category_trusted` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `category_visible` (`category_visible`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `category`

INSERT INTO `category` (`id`, `category_type`, `category_title`, `category_description`, `category_color`, `category_visible`, `category_trusted`) VALUES
(1, 5, 'Category 1', 'Category 1', '9900CC', 1, 0),
(2, 5, 'Category 2', 'Category 2', '3300FF', 1, 0),
(3, 5, 'Category 3', 'Category 3', '663300', 1, 0),
(4, 5, 'Trusted Reports', 'Reports from trusted reporters', '339900', 1, 1);



/**
* Table structure for table `category_lang`
* 
*/

CREATE TABLE IF NOT EXISTS `category_lang`                                          
(
    `id` INT(11) unsigned  NOT NULL AUTO_INCREMENT,                                 
    `category_id` int(11) NOT NULL,                                                 
    `locale` VARCHAR(10) default NULL,                                              
    `category_title` VARCHAR(255) default NULL,                                     
    `category_description` TEXT default NULL,                                       
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `category_lang`



/**
* Table structure for table `country`
* 
*/

CREATE TABLE IF NOT EXISTS `country` (                                              
    `id` int(11) NOT NULL auto_increment,                                           
    `iso` varchar(10) default NULL,                                                 
    `country` varchar(100) default NULL,                                            
    `capital` varchar(100) default NULL,                                            
    `cities` tinyint(4) NOT NULL default '0',                                       
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `country`

INSERT INTO `country` (`id`, `iso`, `country`, `capital`, `cities`) VALUES
(1, 'AD', 'Andorra', 'Andorra la Vella', 0),
(2, 'AE', 'United Arab Emirates', 'Abu Dhabi', 0),
(3, 'AF', 'Afghanistan', 'Kabul', 0),
(4, 'AG', 'Antigua and Barbuda', 'St. John''s', 0),
(5, 'AI', 'Anguilla', 'The Valley', 0),
(6, 'AL', 'Albania', 'Tirana', 0),
(7, 'AM', 'Armenia', 'Yerevan', 0),
(8, 'AN', 'Netherlands Antilles', 'Willemstad', 0),
(9, 'AO', 'Angola', 'Luanda', 0),
(10, 'AQ', 'Antarctica', '', 0),
(11, 'AR', 'Argentina', 'Buenos Aires', 0),
(12, 'AS', 'American Samoa', 'Pago Pago', 0),
(13, 'AT', 'Austria', 'Vienna', 0),
(14, 'AU', 'Australia', 'Canberra', 0),
(15, 'AW', 'Aruba', 'Oranjestad', 0),
(16, 'AX', 'Aland Islands', 'Mariehamn', 0),
(17, 'AZ', 'Azerbaijan', 'Baku', 0),
(18, 'BA', 'Bosnia and Herzegovina', 'Sarajevo', 0),
(19, 'BB', 'Barbados', 'Bridgetown', 0),
(20, 'BD', 'Bangladesh', 'Dhaka', 0),
(21, 'BE', 'Belgium', 'Brussels', 0),
(22, 'BF', 'Burkina Faso', 'Ouagadougou', 0),
(23, 'BG', 'Bulgaria', 'Sofia', 0),
(24, 'BH', 'Bahrain', 'Manama', 0),
(25, 'BI', 'Burundi', 'Bujumbura', 0),
(26, 'BJ', 'Benin', 'Porto-Novo', 0),
(27, 'BL', 'Saint Barthélemy', 'Gustavia', 0),
(28, 'BM', 'Bermuda', 'Hamilton', 0),
(29, 'BN', 'Brunei', 'Bandar Seri Begawan', 0),
(30, 'BO', 'Bolivia', 'La Paz', 0),
(31, 'BR', 'Brazil', 'Brasília', 0),
(32, 'BS', 'Bahamas', 'Nassau', 0),
(33, 'BT', 'Bhutan', 'Thimphu', 0),
(34, 'BV', 'Bouvet Island', '', 0),
(35, 'BW', 'Botswana', 'Gaborone', 0),
(36, 'BY', 'Belarus', 'Minsk', 0),
(37, 'BZ', 'Belize', 'Belmopan', 0),
(38, 'CA', 'Canada', 'Ottawa', 0),
(39, 'CC', 'Cocos Islands', 'West Island', 0),
(40, 'CD', 'Democratic Republic of the Congo', 'Kinshasa', 0),
(41, 'CF', 'Central African Republic', 'Bangui', 0),
(42, 'CG', 'Congo Brazzavile', 'Brazzaville', 0),
(43, 'CH', 'Switzerland', 'Berne', 0),
(44, 'CI', 'Ivory Coast', 'Yamoussoukro', 0),
(45, 'CK', 'Cook Islands', 'Avarua', 0),
(46, 'CL', 'Chile', 'Santiago', 0),
(47, 'CM', 'Cameroon', 'Yaoundé', 0),
(48, 'CN', 'China', 'Beijing', 0),
(49, 'CO', 'Colombia', 'Bogotá', 0),
(50, 'CR', 'Costa Rica', 'San José', 0),
(51, 'CS', 'Serbia and Montenegro', 'Belgrade', 0),
(52, 'CU', 'Cuba', 'Havana', 0),
(53, 'CV', 'Cape Verde', 'Praia', 0),
(54, 'CX', 'Christmas Island', 'Flying Fish Cove', 0),
(55, 'CY', 'Cyprus', 'Nicosia', 0),
(56, 'CZ', 'Czech Republic', 'Prague', 0),
(57, 'DE', 'Germany', 'Berlin', 0),
(58, 'DJ', 'Djibouti', 'Djibouti', 0),
(59, 'DK', 'Denmark', 'Copenhagen', 0),
(60, 'DM', 'Dominica', 'Roseau', 0),
(61, 'DO', 'Dominican Republic', 'Santo Domingo', 0),
(62, 'DZ', 'Algeria', 'Algiers', 0),
(63, 'EC', 'Ecuador', 'Quito', 0),
(64, 'EE', 'Estonia', 'Tallinn', 0),
(65, 'EG', 'Egypt', 'Cairo', 0),
(66, 'EH', 'Western Sahara', 'El-Aaiun', 0),
(67, 'ER', 'Eritrea', 'Asmara', 0),
(68, 'ES', 'Spain', 'Madrid', 0),
(69, 'ET', 'Ethiopia', 'Addis Ababa', 0),
(70, 'FI', 'Finland', 'Helsinki', 0),
(71, 'FJ', 'Fiji', 'Suva', 0),
(72, 'FK', 'Falkland Islands', 'Stanley', 0),
(73, 'FM', 'Micronesia', 'Palikir', 0),
(74, 'FO', 'Faroe Islands', 'Tórshavn', 0),
(75, 'FR', 'France', 'Paris', 0),
(76, 'GA', 'Gabon', 'Libreville', 0),
(77, 'GB', 'United Kingdom', 'London', 0),
(78, 'GD', 'Grenada', 'St. George''s', 0),
(79, 'GE', 'Georgia', 'Tbilisi', 0),
(80, 'GF', 'French Guiana', 'Cayenne', 0),
(81, 'GG', 'Guernsey', 'St Peter Port', 0),
(82, 'GH', 'Ghana', 'Accra', 0),
(83, 'GI', 'Gibraltar', 'Gibraltar', 0),
(84, 'GL', 'Greenland', 'Nuuk', 0),
(85, 'GM', 'Gambia', 'Banjul', 0),
(86, 'GN', 'Guinea', 'Conakry', 0),
(87, 'GP', 'Guadeloupe', 'Basse-Terre', 0),
(88, 'GQ', 'Equatorial Guinea', 'Malabo', 0),
(89, 'GR', 'Greece', 'Athens', 0),
(90, 'GS', 'South Georgia and the South Sandwich Islands', 'Grytviken', 0),
(91, 'GT', 'Guatemala', 'Guatemala City', 0),
(92, 'GU', 'Guam', 'Hagåtña', 0),
(93, 'GW', 'Guinea-Bissau', 'Bissau', 0),
(94, 'GY', 'Guyana', 'Georgetown', 0),
(95, 'HK', 'Hong Kong', 'Hong Kong', 0),
(96, 'HM', 'Heard Island and McDonald Islands', '', 0),
(97, 'HN', 'Honduras', 'Tegucigalpa', 0),
(98, 'HR', 'Croatia', 'Zagreb', 0),
(99, 'HT', 'Haiti', 'Port-au-Prince', 0),
(100, 'HU', 'Hungary', 'Budapest', 0),
(101, 'ID', 'Indonesia', 'Jakarta', 0),
(102, 'IE', 'Ireland', 'Dublin', 0),
(103, 'IL', 'Israel', 'Jerusalem', 0),
(104, 'IM', 'Isle of Man', 'Douglas, Isle of Man', 0),
(105, 'IN', 'India', 'New Delhi', 0),
(106, 'IO', 'British Indian Ocean Territory', 'Diego Garcia', 0),
(107, 'IQ', 'Iraq', 'Baghdad', 0),
(108, 'IR', 'Iran', 'Tehran', 0),
(109, 'IS', 'Iceland', 'Reykjavík', 0),
(110, 'IT', 'Italy', 'Rome', 0),
(111, 'JE', 'Jersey', 'Saint Helier', 0),
(112, 'JM', 'Jamaica', 'Kingston', 0),
(113, 'JO', 'Jordan', 'Amman', 0),
(114, 'JP', 'Japan', 'Tokyo', 0),
(115, 'KE', 'Kenya', 'Nairobi', 0),
(116, 'KG', 'Kyrgyzstan', 'Bishkek', 0),
(117, 'KH', 'Cambodia', 'Phnom Penh', 0),
(118, 'KI', 'Kiribati', 'South Tarawa', 0),
(119, 'KM', 'Comoros', 'Moroni', 0),
(120, 'KN', 'Saint Kitts and Nevis', 'Basseterre', 0),
(121, 'KP', 'North Korea', 'Pyongyang', 0),
(122, 'KR', 'South Korea', 'Seoul', 0),
(123, 'KW', 'Kuwait', 'Kuwait City', 0),
(124, 'KY', 'Cayman Islands', 'George Town', 0),
(125, 'KZ', 'Kazakhstan', 'Astana', 0),
(126, 'LA', 'Laos', 'Vientiane', 0),
(127, 'LB', 'Lebanon', 'Beirut', 0),
(128, 'LC', 'Saint Lucia', 'Castries', 0),
(129, 'LI', 'Liechtenstein', 'Vaduz', 0),
(130, 'LK', 'Sri Lanka', 'Colombo', 0),
(131, 'LR', 'Liberia', 'Monrovia', 0),
(132, 'LS', 'Lesotho', 'Maseru', 0),
(133, 'LT', 'Lithuania', 'Vilnius', 0),
(134, 'LU', 'Luxembourg', 'Luxembourg', 0),
(135, 'LV', 'Latvia', 'Riga', 0),
(136, 'LY', 'Libya', 'Tripolis', 0),
(137, 'MA', 'Morocco', 'Rabat', 0),
(138, 'MC', 'Monaco', 'Monaco', 0),
(139, 'MD', 'Moldova', 'Chi_in_u', 0),
(140, 'ME', 'Montenegro', 'Podgorica', 0),
(141, 'MF', 'Saint Martin', 'Marigot', 0),
(142, 'MG', 'Madagascar', 'Antananarivo', 0),
(143, 'MH', 'Marshall Islands', 'Uliga', 0),
(144, 'MK', 'Macedonia', 'Skopje', 0),
(145, 'ML', 'Mali', 'Bamako', 0),
(146, 'MM', 'Myanmar', 'Yangon', 0),
(147, 'MN', 'Mongolia', 'Ulan Bator', 0),
(148, 'MO', 'Macao', 'Macao', 0),
(149, 'MP', 'Northern Mariana Islands', 'Saipan', 0),
(150, 'MQ', 'Martinique', 'Fort-de-France', 0),
(151, 'MR', 'Mauritania', 'Nouakchott', 0),
(152, 'MS', 'Montserrat', 'Plymouth', 0),
(153, 'MT', 'Malta', 'Valletta', 0),
(154, 'MU', 'Mauritius', 'Port Louis', 0),
(155, 'MV', 'Maldives', 'Malé', 0),
(156, 'MW', 'Malawi', 'Lilongwe', 0),
(157, 'MX', 'Mexico', 'Mexico City', 0),
(158, 'MY', 'Malaysia', 'Kuala Lumpur', 0),
(159, 'MZ', 'Mozambique', 'Maputo', 0),
(160, 'NA', 'Namibia', 'Windhoek', 0),
(161, 'NC', 'New Caledonia', 'Nouméa', 0),
(162, 'NE', 'Niger', 'Niamey', 0),
(163, 'NF', 'Norfolk Island', 'Kingston', 0),
(164, 'NG', 'Nigeria', 'Abuja', 0),
(165, 'NI', 'Nicaragua', 'Managua', 0),
(166, 'NL', 'Netherlands', 'Amsterdam', 0),
(167, 'NO', 'Norway', 'Oslo', 0),
(168, 'NP', 'Nepal', 'Kathmandu', 0),
(169, 'NR', 'Nauru', 'Yaren', 0),
(170, 'NU', 'Niue', 'Alofi', 0),
(171, 'NZ', 'New Zealand', 'Wellington', 0),
(172, 'OM', 'Oman', 'Muscat', 0),
(173, 'PA', 'Panama', 'Panama City', 0),
(174, 'PE', 'Peru', 'Lima', 0),
(175, 'PF', 'French Polynesia', 'Papeete', 0),
(176, 'PG', 'Papua New Guinea', 'Port Moresby', 0),
(177, 'PH', 'Philippines', 'Manila', 0),
(178, 'PK', 'Pakistan', 'Islamabad', 0),
(179, 'PL', 'Poland', 'Warsaw', 0),
(180, 'PM', 'Saint Pierre and Miquelon', 'Saint-Pierre', 0),
(181, 'PN', 'Pitcairn', 'Adamstown', 0),
(182, 'PR', 'Puerto Rico', 'San Juan', 0),
(183, 'PS', 'Palestinian Territory', 'East Jerusalem', 0),
(184, 'PT', 'Portugal', 'Lisbon', 0),
(185, 'PW', 'Palau', 'Koror', 0),
(186, 'PY', 'Paraguay', 'Asunción', 0),
(187, 'QA', 'Qatar', 'Doha', 0),
(188, 'RE', 'Reunion', 'Saint-Denis', 0),
(189, 'RO', 'Romania', 'Bucharest', 0),
(190, 'RS', 'Serbia', 'Belgrade', 0),
(191, 'RU', 'Russia', 'Moscow', 0),
(192, 'RW', 'Rwanda', 'Kigali', 0),
(193, 'SA', 'Saudi Arabia', 'Riyadh', 0),
(194, 'SB', 'Solomon Islands', 'Honiara', 0),
(195, 'SC', 'Seychelles', 'Victoria', 0),
(196, 'SD', 'Sudan', 'Khartoum', 0),
(197, 'SE', 'Sweden', 'Stockholm', 0),
(198, 'SG', 'Singapore', 'Singapur', 0),
(199, 'SH', 'Saint Helena', 'Jamestown', 0),
(200, 'SI', 'Slovenia', 'Ljubljana', 0),
(201, 'SJ', 'Svalbard and Jan Mayen', 'Longyearbyen', 0),
(202, 'SK', 'Slovakia', 'Bratislava', 0),
(203, 'SL', 'Sierra Leone', 'Freetown', 0),
(204, 'SM', 'San Marino', 'San Marino', 0),
(205, 'SN', 'Senegal', 'Dakar', 0),
(206, 'SO', 'Somalia', 'Mogadishu', 0),
(207, 'SR', 'Suriname', 'Paramaribo', 0),
(208, 'ST', 'Sao Tome and Principe', 'São Tomé', 0),
(209, 'SV', 'El Salvador', 'San Salvador', 0),
(210, 'SY', 'Syria', 'Damascus', 0),
(211, 'SZ', 'Swaziland', 'Mbabane', 0),
(212, 'TC', 'Turks and Caicos Islands', 'Cockburn Town', 0),
(213, 'TD', 'Chad', 'N''Djamena', 0),
(214, 'TF', 'French Southern Territories', 'Martin-de-Viviès', 0),
(215, 'TG', 'Togo', 'Lomé', 0),
(216, 'TH', 'Thailand', 'Bangkok', 0),
(217, 'TJ', 'Tajikistan', 'Dushanbe', 0),
(218, 'TK', 'Tokelau', '', 0),
(219, 'TL', 'East Timor', 'Dili', 0),
(220, 'TM', 'Turkmenistan', 'Ashgabat', 0),
(221, 'TN', 'Tunisia', 'Tunis', 0),
(222, 'TO', 'Tonga', 'Nuku''alofa', 0),
(223, 'TR', 'Turkey', 'Ankara', 0),
(224, 'TT', 'Trinidad and Tobago', 'Port of Spain', 0),
(225, 'TV', 'Tuvalu', 'Vaiaku', 0),
(226, 'TW', 'Taiwan', 'Taipei', 0),
(227, 'TZ', 'Tanzania', 'Dar es Salaam', 0),
(228, 'UA', 'Ukraine', 'Kiev', 0),
(229, 'UG', 'Uganda', 'Kampala', 0),
(230, 'UM', 'United States Minor Outlying Islands', '', 0),
(231, 'US', 'United States', 'Washington', 0),
(232, 'UY', 'Uruguay', 'Montevideo', 0),
(233, 'UZ', 'Uzbekistan', 'Tashkent', 0),
(234, 'VA', 'Vatican', 'Vatican City', 0),
(235, 'VC', 'Saint Vincent and the Grenadines', 'Kingstown', 0),
(236, 'VE', 'Venezuela', 'Caracas', 0),
(237, 'VG', 'British Virgin Islands', 'Road Town', 0),
(238, 'VI', 'U.S. Virgin Islands', 'Charlotte Amalie', 0),
(239, 'VN', 'Vietnam', 'Hanoi', 0),
(240, 'VU', 'Vanuatu', 'Port Vila', 0),
(241, 'WF', 'Wallis and Futuna', 'Matâ''Utu', 0),
(242, 'WS', 'Samoa', 'Apia', 0),
(243, 'YE', 'Yemen', 'San‘a’', 0),
(244, 'YT', 'Mayotte', 'Mamoudzou', 0),
(245, 'ZA', 'South Africa', 'Pretoria', 0),
(246, 'ZM', 'Zambia', 'Lusaka', 0),
(247, 'ZW', 'Zimbabwe', 'Harare', 0);


/**
* Table structure for table `incident`
* 
*/

CREATE TABLE IF NOT EXISTS `incident` (                                             
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `location_id` bigint(20) NOT NULL,                                              
    `form_id` int(11) NOT NULL default '1',                                         
    `locale` varchar(10) NOT NULL default 'en_US',                                  
    `user_id` bigint(20) default NULL,                                              
    `incident_title` varchar(255) default NULL,                                     
    `incident_description` longtext,                                                
    `incident_date` datetime default NULL,                                          
    `incident_mode` tinyint(4) NOT NULL default '1' COMMENT '1 - WEB, 2 - SMS, 3 - EMAIL, 4 - TWITTER',    
    `incident_active` tinyint(4) NOT NULL default '0',                              
    `incident_verified` tinyint(4) NOT NULL default '0',                            
    `incident_source` varchar(5) default NULL,                                      
    `incident_information` varchar(5) default NULL,                                 
    `incident_rating` VARCHAR(15) DEFAULT '0' NOT NULL,                             
    `incident_dateadd` datetime default NULL,                                       
    `incident_dateadd_gmt` datetime default NULL,                                   
    `incident_datemodify` datetime default NULL,                                    
    `incident_alert_status` TINYINT NOT NULL DEFAULT '0' COMMENT '0 - Not Tagged for Sending, 1 - Tagged for Sending, 2 - Alerts Have Been Sent',
	`incident_zoom` tinyint NULL DEFAULT NULL,
  PRIMARY KEY  (`id`),
  KEY `location_id` (`location_id`),
  KEY `incident_active` (`incident_active`),
  KEY `incident_date` (`incident_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `incident`


LOCK TABLES `incident` WRITE;
/*!40000 ALTER TABLE `incident` DISABLE KEYS */;
INSERT INTO `incident` VALUES (1,1,1,'en_US',1,'Hello Ushahidi!','Welcome to Ushahidi. Please replace this report with a valid incident','2010-01-01 12:00:00',1,1,1,'0','2010-01-01 12:00:00','0',NULL,NULL,NULL,'0',NULL);
/*!40000 ALTER TABLE `incident` ENABLE KEYS */;
UNLOCK TABLES;


/**
* Table structure for table `incident_lang`
* 
*/

CREATE TABLE IF NOT EXISTS `incident_lang`                                          
(
    `id` BIGINT(20) unsigned  NOT NULL AUTO_INCREMENT,                              
    `incident_id` BIGINT(20) NOT NULL,                                              
    `locale` VARCHAR(10) default NULL,                                              
    `incident_title` VARCHAR(255) default NULL,                                     
    `incident_description` LONGTEXT default NULL,                                   
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `incident_lang`



/**
* Table structure for table `incident_category`
* 
*/

CREATE TABLE IF NOT EXISTS `incident_category` (                                    
    `id` int(11) NOT NULL auto_increment,                                           
    `incident_id` bigint(20) NOT NULL default '0',                                  
    `category_id` int(11) NOT NULL default '0',                                     
  PRIMARY KEY  (`id`),
  UNIQUE KEY `incident_category_ids` (`incident_id`,`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `incident_category`
LOCK TABLES `incident_category` WRITE;
/*!40000 ALTER TABLE `incident_category` DISABLE KEYS */;
INSERT INTO `incident_category` VALUES (1,1,1);
/*!40000 ALTER TABLE `incident_category` ENABLE KEYS */;
UNLOCK TABLES;



/**
* Table structure for table `incident_person`
* 
*/

CREATE TABLE IF NOT EXISTS `incident_person` (                                      
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `incident_id` bigint(20) default NULL,                                          
    `location_id` bigint(20) default NULL,                                          
    `person_first` varchar(200) default NULL,                                       
    `person_last` varchar(200) default NULL,                                        
    `person_email` varchar(120) default NULL,                                       
    `person_phone` varchar(60) default NULL,                                        
    `person_ip` varchar(50) default NULL,                                           
    `person_date` datetime default NULL,                                            
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `incident_person`



/**
* Table structure for table `comment`
* 
*/

CREATE TABLE IF NOT EXISTS `comment`                                                
(
    `id` BIGINT unsigned  NOT NULL AUTO_INCREMENT ,                                 
    `incident_id` BIGINT NOT NULL,                                                  
    `user_id` INT(11) DEFAULT 0,                                                    
    `comment_author` VARCHAR(100) default NULL,                                     
    `comment_email` VARCHAR(120) default NULL,                                      
    `comment_description` TEXT default NULL,                                        
    `comment_ip` VARCHAR(100) default NULL,                                         
    `comment_rating` VARCHAR(15) DEFAULT '0' NOT NULL,                              
    `comment_spam` TINYINT NOT NULL DEFAULT 0,                                      
    `comment_active` TINYINT NOT NULL DEFAULT 0,                                    
    `comment_date` DATETIME default NULL,                                           
    `comment_date_gmt` DATETIME default NULL,                                       
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `comment`



/**
* Table structure for table `rating`
* 
*/

CREATE TABLE IF NOT EXISTS `rating`                                                 
(
    `id` BIGINT unsigned  NOT NULL AUTO_INCREMENT,
	`user_id` int(11) DEFAULT '0',
    `incident_id` BIGINT default NULL,                                              
    `comment_id` BIGINT default NULL,                                               
    `rating` TINYINT DEFAULT 0,                                                     
    `rating_ip` VARCHAR(100) default NULL,                                          
    `rating_date` DATETIME default NULL,                                            
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `rating`



/**
* Table structure for table `location`
* 
*/

CREATE TABLE IF NOT EXISTS `location` (                                             
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `location_name` varchar(255) default NULL,                                      
    `country_id` int(11) NOT NULL default '0',                                              
    `latitude` DOUBLE NOT NULL default '0',                                         
    `longitude` DOUBLE NOT NULL default '0',                                        
    `location_visible` tinyint(4) NOT NULL default '1',                             
    `location_date` datetime default NULL,                                          
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `location`

LOCK TABLES `location` WRITE;
/*!40000 ALTER TABLE `location` DISABLE KEYS */;
INSERT INTO `location` VALUES (1,'Nairobi',115,-1.2873000707050097, 36.821451182008204,1,'2009-06-30 00:00:00');
/*!40000 ALTER TABLE `location` ENABLE KEYS */;
UNLOCK TABLES;

/**
* Table structure for table `media`
* 
*/

CREATE TABLE IF NOT EXISTS `media` (                                                
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `location_id` bigint(20) default NULL,                                          
    `incident_id` bigint(20) default NULL,
    `checkin_id` bigint(20) default NULL,
    `message_id` bigint(20) NULL DEFAULT NULL,
    `badge_id` int(11) default NULL,
    `media_type` tinyint(4) default NULL COMMENT '1 - IMAGES, 2 - VIDEO, 3 - AUDIO, 4 - NEWS, 5 - PODCAST',    
    `media_title` varchar(255) default NULL,                                        
    `media_description` longtext default NULL,                                      
    `media_link` varchar(255) default NULL,                                         
    `media_medium` varchar(255) default NULL,                                        
    `media_thumb` varchar(255) default NULL,                                        
    `media_date` datetime default NULL,                                             
    `media_active` tinyint(4) NOT NULL default '1',                                 
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `media`


/**
* Table structure for table `feed`
* 
*/
CREATE TABLE IF NOT EXISTS `feed`                                                   
(
    `id` int(11) unsigned  NOT NULL AUTO_INCREMENT ,                                
    `feed_name` VARCHAR(255) default NULL,                                          
    `feed_url` VARCHAR(255) default NULL,                                           
    `feed_cache` TEXT default NULL,                                                 
    `feed_active` TINYINT DEFAULT 1,                                                
    `feed_update` INT DEFAULT 0 NOT NULL,                                           
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `feed`


/**
* Table structure for table `feed_item`
* 
*/

CREATE TABLE IF NOT EXISTS `feed_item`                                              
(
    `id` BIGINT unsigned  NOT NULL AUTO_INCREMENT ,                                 
    `feed_id` INT(11) NOT NULL,                                                     
    `location_id` BIGINT default '0',                                               
    `incident_id` INT(11) NOT NULL DEFAULT '0',                                     
    `item_title` VARCHAR(255) default NULL,                                         
    `item_description` TEXT default NULL,                                           
    `item_link` VARCHAR(255) default NULL,                                          
    `item_date` DATETIME default NULL,                                              
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `feed_item`


/**
* Table structure for table `message`
* 
*/

CREATE TABLE IF NOT EXISTS `message`                                                
(
    `id` BIGINT unsigned  NOT NULL AUTO_INCREMENT ,                                 
/*Outgoing Messages From Admin*/
    `parent_id` BIGINT DEFAULT 0,                                                   
    `incident_id` INTEGER DEFAULT 0,                                                
    `user_id` INT DEFAULT 0,                                                        
    `reporter_id` bigint(20) default NULL,                                          
    `service_messageid` varchar(100) default NULL,                                  
    `message_from` VARCHAR(100) DEFAULT NULL,                                       
    `message_to` VARCHAR(100) DEFAULT NULL,                                         
    `message` TEXT default NULL,                                                    
    `message_detail` text default NULL,                                             
    `message_type` TINYINT default 1 COMMENT '1 - INBOX, 2 - OUTBOX (From Admin), 3 - DELETED',
    `message_date` DATETIME default NULL,                                           
    `message_level` TINYINT NULL DEFAULT 0 COMMENT '0 - UNREAD, 1 - READ, 99 - SPAM',                                         
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `message`




/**
* Table structure for table `roles`
* 
*/

CREATE TABLE IF NOT EXISTS `roles` (                                                
    `id` int(11) unsigned NOT NULL auto_increment,                                  
    `name` varchar(32) NOT NULL,                                                    
    `description` varchar(255) NOT NULL,                                         
	`reports_view` tinyint(4) NOT NULL default '0',
	`reports_edit` tinyint(4) NOT NULL default '0',
	`reports_evaluation` tinyint(4) NOT NULL default '0',
	`reports_comments` tinyint(4) NOT NULL default '0',
	`reports_download` tinyint(4) NOT NULL default '0',
	`reports_upload` tinyint(4) NOT NULL default '0',
	`messages` tinyint(4) NOT NULL default '0',
	`messages_reporters` tinyint(4) NOT NULL default '0',
	`stats` tinyint(4) NOT NULL default '0',
	`settings` tinyint(4) NOT NULL default '0',
	`manage` tinyint(4) NOT NULL default '0',
	`users` tinyint(4) NOT NULL default '0',
	`manage_roles` tinyint(4) NOT NULL default '0',
	`checkin` tinyint(4) NOT NULL default '1',
	`checkin_admin` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `roles`

INSERT INTO `roles` (`id`, `name`, `description`, `reports_view`, `reports_edit`, `reports_evaluation`, `reports_comments`, `reports_download`, `reports_upload`, `messages`, `messages_reporters`, `stats`, `settings`, `manage`, `users`, `manage_roles`, `checkin`, `checkin_admin`) VALUES
(1, 'login', 'Login privileges, granted after account confirmation', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0),
(2, 'admin', 'Administrative user, has access to almost everything.', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1),
(3, 'superadmin','Super administrative user, has access to everything.', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
(4, 'member','Regular user with access only to the member area', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);


/**
* Table structure for table `roles_users`
* 
*/

CREATE TABLE IF NOT EXISTS `roles_users` (                                          
    `user_id` int(11) unsigned NOT NULL,                                            
    `role_id` int(11) unsigned NOT NULL,                                            
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `roles_users`

INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2),
(1, 3);


/**
* Table structure for table `sessions`
* 
*/

CREATE TABLE IF NOT EXISTS `sessions` (                                             
    `session_id` varchar(40) NOT NULL,                                              
    `last_activity` int(10) unsigned NOT NULL,                                      
    `data` text NOT NULL,                                                           
  PRIMARY KEY  (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `sessions`



/**
* Table structure for table `settings`
* 
*/

CREATE TABLE IF NOT EXISTS `settings` (                                             
    `id` int(10) unsigned NOT NULL auto_increment,                                  
    `site_name` varchar(255) default NULL,                                          
    `site_tagline` varchar(255) default NULL,
    `site_banner_id` int(11) default NULL,
    `site_email` varchar(120) default NULL,
    `site_key` varchar(100) default NULL,                                           
    `site_language` varchar(10) NOT NULL default 'en_US',                           
    `site_style` varchar(50) NOT NULL default 'default',                            
    `site_timezone` varchar(80) default NULL,                                       
    `site_contact_page` TINYINT NOT NULL DEFAULT '1',                               
    `site_help_page` TINYINT NOT NULL DEFAULT '1',                                  
    `site_message` TEXT NOT NULL DEFAULT '',                                        
    `site_copyright_statement` TEXT DEFAULT NULL,
    `site_submit_report_message` TEXT NOT NULL DEFAULT '',
    `allow_reports` tinyint(4) NOT NULL default '1',                                
    `allow_comments` tinyint(4) NOT NULL default '1',                               
    `allow_feed` tinyint(4) NOT NULL default '1',                                   
    `allow_stat_sharing` tinyint(4) NOT NULL default '1',                           
    `allow_clustering` tinyint(4) NOT NULL default '0',                             
    `cache_pages` tinyint(4) NOT NULL DEFAULT '0',
    `cache_pages_lifetime` int(4) NOT NULL DEFAULT '1800',
    `private_deployment` tinyint(4) NOT NULL DEFAULT '0',
    `default_map` varchar(100) NOT NULL DEFAULT 'google_normal',
    `default_map_all` varchar(20) NOT NULL default 'CC0000',                        
    `api_google` varchar(200) default NULL,                                         
    `api_yahoo` varchar(200) default NULL,                                          
    `api_live` varchar(200) default NULL,                                           
    `api_akismet` VARCHAR( 200 ) default NULL,                                      
    `default_country` int(11) default NULL,                                         
    `multi_country` TINYINT NOT NULL DEFAULT '0',                                   
    `default_city` varchar(150) default NULL,                                       
    `default_lat` varchar(100) default NULL,                                        
    `default_lon` varchar(100) default NULL,                                        
    `default_zoom` tinyint(4) NOT NULL default '10',                                
    `items_per_page` smallint(6) NOT NULL default '20',                             
    `items_per_page_admin` smallint(6) NOT NULL default '20',                       
    `sms_provider` varchar(100) NULL DEFAULT NULL,
    `sms_no1` varchar(100) default NULL,                                            
    `sms_no2` varchar(100) default NULL,                                            
    `sms_no3` varchar(100) default NULL,                                            
    `google_analytics` text,                                                        
    `twitter_hashtags` text default NULL,
	`blocks` text,
	`blocks_per_row` tinyint(4) NOT NULL DEFAULT '2',                              
    `date_modify` datetime default NULL,                                            
    `stat_id` BIGINT default NULL COMMENT 'comes from centralized stats',           
    `stat_key` VARCHAR(30) NOT NULL ,                                               
    `email_username` VARCHAR(100) NOT NULL ,                                        
    `email_password` VARCHAR(100) NOT NULL ,                                        
    `email_port` INT(11) NOT NULL ,                                                 
    `email_host` VARCHAR(100) NOT NULL ,                                            
    `email_servertype` VARCHAR(100) NOT NULL ,                                      
    `email_ssl` INT(5) NOT NULL,                                                    
	`ftp_server` varchar(100) NULL DEFAULT NULL,
	`ftp_user_name` varchar(100) NULL DEFAULT NULL,
    `alerts_email` VARCHAR(120) NOT NULL,
    `checkins` tinyint(4) NOT NULL default '0',
    `facebook_appid` VARCHAR(150) NULL DEFAULT NULL,
    `facebook_appsecret` VARCHAR(150) NULL DEFAULT NULL,
    `db_version` varchar(20) default NULL,                                          
    `ushahidi_version` varchar(20) default NULL,                                    
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8; 


-- Dumping data for table `settings`

INSERT INTO `settings` (`id`, `site_name`, `api_google`, `api_yahoo`, `api_live`, `default_country`, `default_city`, `default_lat`, `default_lon`, `default_zoom`, `items_per_page`, `items_per_page_admin`, `blocks`, `date_modify`) VALUES
(1, 'Ushahidi', 'ABQIAAAAjsEM5UsvCPCIHp80spK1kBQKW7L4j6gYznY0oMkScAbKwifzxxRhJ3SP_ijydkmJpN3jX8kn5r5fEQ', '5CYeWbfV34E21JOW1a4.54Mf6e9jLNkD0HVzaKoQmJZi2qzmSZd5mD8X49x7', NULL, 115, 'nairobi', '-1.2873000707050097', '36.821451182008204', 13, 20, 20, 'reports_block|news_block', '2008-08-25 10:25:18');


/**
* Table structure for table `users`
* 
*/

CREATE TABLE IF NOT EXISTS `users` (                                                
    `id` int(11) unsigned NOT NULL auto_increment,                                  
    `name` varchar(200) default NULL,                                               
    `email` varchar(127) NOT NULL,                                                  
    `username` varchar(31) NOT NULL default '',                                     
    `password` char(50) NOT NULL,                                                   
    `logins` int(10) unsigned NOT NULL default '0',                                 
    `last_login` int(10) unsigned default NULL,                                     
    `notify` tinyint(1) NOT NULL default '0' COMMENT 'Flag incase admin opts in for email notifications',    
    `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    `color` varchar(6) NOT NULL DEFAULT 'FF0000',
    `code` VARCHAR(30) NULL DEFAULT NULL,
    `confirmed` TINYINT(1) NOT NULL DEFAULT '0',
    `public_profile` TINYINT(1) NOT NULL DEFAULT '1',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `users`

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `logins`, `last_login`, `updated`, `public_profile`) VALUES
(1, 'Administrator', 'myemail@example.com', 'admin', 'bae4b17e9acbabf959654a4c496e577003e0b887c6f52803d7', 0, 1221420023, '2008-09-14 14:17:22', 0);


/**
* Table structure for table `user_tokens`
* 
*/

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `user_tokens`



/**
* Table structure for table `verified`
* 
*/

CREATE TABLE IF NOT EXISTS `verified` (                                             
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `incident_id` bigint(20) default NULL,                                          
    `user_id` int(11) default NULL,                                                 
    `verified_comment` longtext default NULL,                                       
    `verified_date` datetime default NULL,                                          
    `verified_status` tinyint(4) NOT NULL default '0',                              
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `verified`



/**
* Table structure for table `alert`
* 
*/

CREATE TABLE IF NOT EXISTS `alert` (                                                
    `id` bigint(20) unsigned NOT NULL auto_increment,
	`user_id` int(11) DEFAULT '0',
    `alert_type` tinyint(4) NOT NULL COMMENT '1 - MOBILE, 2 - EMAIL',
    `alert_recipient` varchar(200) default NULL,                                    
    `alert_code` varchar(30) default NULL,                                          
    `alert_confirmed` tinyint(4) NOT NULL default '0',                              
    `alert_lat` varchar(150) default NULL,                                          
    `alert_lon` varchar(150) default NULL,                                          
    `alert_radius` TINYINT NOT NULL DEFAULT '20',                                   
    `alert_ip` varchar(100) default NULL,                                           
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_alert_code` (`alert_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `alert`



/**
* Table structure for table `alert_sent`
* 
*/

CREATE TABLE IF NOT EXISTS `alert_sent`                                             
(
    `id` BIGINT unsigned  NOT NULL AUTO_INCREMENT,                                  
    `incident_id` BIGINT NOT NULL,                                                  
    `alert_id` BIGINT NOT NULL,                                                     
    `alert_date` DATETIME NULL,                                                     
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `alert_sent`



/**
* Table structure for table `city`
* 
*/

CREATE TABLE IF NOT EXISTS `city` (                                                 
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `country_id` int(11) default NULL,                                              
    `city` varchar(200) default NULL,                                               
    `city_lat` varchar(150) default NULL,                                           
    `city_lon` varchar(200) default NULL,                                           
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `city`



/**
* Table structure for table `scheduler`
* 
*/

CREATE TABLE IF NOT EXISTS `scheduler` (                                            
    `id` int(10) unsigned NOT NULL auto_increment,                                  
    `scheduler_name` varchar(100) NOT NULL,                                         
    `scheduler_last` int(10) unsigned NOT NULL default '0',                         
    `scheduler_weekday` smallint(6) NOT NULL default '-1',                          
    `scheduler_day` smallint(6) NOT NULL default '-1',                              
    `scheduler_hour` smallint(6) NOT NULL default '-1',                             
    `scheduler_minute` smallint(6) NOT NULL,                                        
    `scheduler_controller` varchar(100) NOT NULL,                                   
    `scheduler_active` tinyint(4) NOT NULL default '1',                             
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;



-- Dumping data for table `scheduler`

INSERT INTO `scheduler` (`id`, `scheduler_name`, `scheduler_last`, `scheduler_weekday`, `scheduler_day`, `scheduler_hour`, `scheduler_minute`, `scheduler_controller`, `scheduler_active`) VALUES
(1, 'Feeds', 0, -1, -1, -1, 0, 's_feeds', 1),
(2, 'Alerts', 0, -1, -1, -1, -1, 's_alerts', 1),
(3, 'Email', 0, -1, -1, -1, 0, 's_email', 1),
(4, 'Twitter', 0, -1, -1, -1, 0, 's_twitter', 1),
(5, 'Sharing', 0, -1, -1, -1, 0, 's_sharing', 1),
(6, 'Cleanup', 0, -1, -1, -1, 0, 's_cleanup', 1);


/**
* Table structure for table `scheduler_log`
* 
*/

CREATE TABLE IF NOT EXISTS `scheduler_log` (                                        
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `scheduler_id` int(11) NOT NULL,                                                
    `scheduler_name` varchar(100) NOT NULL,                                         
    `scheduler_status` varchar(20) default NULL,                                    
    `scheduler_date` int(10) unsigned NOT NULL,                                     
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `scheduler_log`



/**
* Table structure for table `cluster`
* 
*/

CREATE TABLE IF NOT EXISTS `cluster` (                                              
    `id` int(11) NOT NULL,                                                          
    `location_id` bigint(20) NOT NULL default '0',                                  
    `latitude` double NOT NULL,                                                     
    `longitude` double NOT NULL,                                                    
    `latitude_min` double NOT NULL,                                                 
    `longitude_min` double NOT NULL,                                                
    `latitude_max` double NOT NULL,                                                 
    `longitude_max` double NOT NULL,                                                
    `child_count` int(11) NOT NULL,                                                 
    `parent_id` int(11) NOT NULL,                                                   
    `left_side` int(11) NOT NULL,                                                   
    `right_side` int(11) NOT NULL,                                                  
    `level` int(11) NOT NULL,                                                       
    `incident_id` bigint(20) NOT NULL default '0',                                  
    `incident_title` varchar(255) default NULL,                                     
    `incident_date` int(10) NOT NULL default 0,                                     
    `category_id` int(11) UNSIGNED NOT NULL default '0',                            
    `category_color` varchar(20) NOT NULL default '990000',                         
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `cluster`



/**
* Table structure for table `form`
* 
*/

CREATE TABLE IF NOT EXISTS `form` (                                                 
    `id` int(11) NOT NULL auto_increment,                                           
    `form_title` varchar(200) NOT NULL,                                             
    `form_description` text,                                                        
    `form_active` tinyint(4) default '1',                                           
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `form`

INSERT INTO `form` (`id`, `form_title`, `form_description`, `form_active`) VALUES
(1, 'Default Form', 'Default form, for report entry', 1);



/**
* Table structure for table `form_field`
* 
*/

CREATE TABLE IF NOT EXISTS `form_field` (                                           
    `id` int(11) NOT NULL auto_increment,                                           
    `form_id` int(11) NOT NULL default '0',                                         
    `field_name` varchar(200) default NULL,                                         
    `field_type` tinyint(4) NOT NULL default '1' COMMENT '1 - TEXTFIELD, 2 - TEXTAREA (FREETEXT), 3 - DATE, 4 - PASSWORD, 5 - RADIO, 6 - CHECKBOX',    
    `field_required` tinyint(4) default '0',                                        
    `field_options` text,                                                           
    `field_position` tinyint(4) NOT NULL default '0',                               
    `field_default` varchar(200) default NULL,                                      
    `field_maxlength` int(11) NOT NULL default '0',                                 
    `field_width` smallint(6) NOT NULL default '0',                                 
    `field_height` tinyint(4) default '5',                                          
    `field_isdate` tinyint(4) NOT NULL default '0',                                 
  PRIMARY KEY  (`id`),
  KEY `fk_form_id` (`form_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `cluster`



/**
* Table structure for table `form_response`
* 
*/

CREATE TABLE IF NOT EXISTS `form_response` (                                        
    `id` bigint(20) NOT NULL auto_increment,                                        
    `form_field_id` int(11) NOT NULL,                                               
    `incident_id` bigint(20) NOT NULL,                                              
    `form_response` text NOT NULL,                                                  
  PRIMARY KEY  (`id`),
  KEY `fk_form_field_id` (`form_field_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `form_response`



/**
* Table structure for table `level`
* 
*/

CREATE TABLE IF NOT EXISTS `level` (                                                
    `id` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,                                
    `level_title` varchar(200) default NULL,                                        
    `level_description` varchar(200) default NULL,                                  
    `level_weight` tinyint(4) NOT NULL,                                             
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `level`

INSERT INTO `level` (`id`, `level_title`, `level_description`, `level_weight`) VALUES
(1, 'SPAM + Delete', 'SPAM + Delete', -2),
(2, 'SPAM', 'SPAM', -1),
(3, 'Untrusted', 'Untrusted', 0),
(4, 'Trusted', 'Trusted', 1),
(5, 'Trusted + Verify', 'Trusted + Verify', 2);



/**
* Table structure for table `reporter`
* 
*/

CREATE TABLE IF NOT EXISTS `reporter` (                                             
    `id` bigint(20) unsigned NOT NULL auto_increment,                               
    `incident_id` bigint(20) default NULL,                                          
    `location_id` bigint(20) default NULL,                                          
    `user_id` int(11) default NULL,                                                 
    `service_id` int(11) default NULL,                                              
    `level_id` INT( 11 ) NULL,                                                      
    `service_userid` varchar(255) default NULL,                                     
    `service_account` varchar(255) default NULL,                                    
    `reporter_first` varchar(200) default NULL,                                     
    `reporter_last` varchar(200) default NULL,                                      
    `reporter_email` varchar(120) default NULL,                                     
    `reporter_phone` varchar(60) default NULL,                                      
    `reporter_ip` varchar(50) default NULL,                                         
    `reporter_date` datetime default NULL,                                          
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/**
* Table structure for table `service`
* 
*/

CREATE TABLE IF NOT EXISTS `service` (                                              
    `id` int(10) unsigned NOT NULL auto_increment,                                  
    `service_name` varchar(100) default NULL,                                       
    `service_description` varchar(255) default NULL,                                
    `service_url` varchar(255) default NULL,                                        
    `service_api` varchar(255) default NULL,                                        
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- Dumping data for table `service`

INSERT INTO `service` (`id`, `service_name`, `service_description`, `service_url`, `service_api`) VALUES
(1, 'SMS', 'Text messages from phones', NULL, NULL),
(2, 'Email', 'Text messages from phones', NULL, NULL),
(3, 'Twitter', 'Tweets tweets tweets', 'http://twitter.com', NULL);



/**
* Table structure for table `sharing`
* 
*/

CREATE TABLE IF NOT EXISTS `sharing` (                                              
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`sharing_name` VARCHAR(150) NOT NULL,				-- name of the sharing website
	`sharing_url` VARCHAR(255) NOT NULL,				-- main url of the sharing website
	`sharing_color` VARCHAR(20) DEFAULT 'CC0000',		-- color for the map layer selector
	`sharing_active` TINYINT DEFAULT 1 NOT NULL,		-- sharing layer active?
	`sharing_date` DATETIME,							-- date of last update
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


/**
* Table structure for table `sharing_log`
* 
*/

CREATE TABLE IF NOT EXISTS `sharing_incident` (                                          
	`id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
	`sharing_id` INT UNSIGNED NOT NULL,
	`incident_id` INT NOT NULL,							-- remote website incident ID
	`incident_title` VARCHAR(255) NOT NULL,				-- remote incident title
	`latitude` DOUBLE NOT NULL,							-- remote incident latitude
	`longitude` DOUBLE NOT NULL,						-- remote incident longitude
	`incident_date` DATETIME,							-- remote incident date
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/**
* Table structure for table `page`
* 
*/

CREATE TABLE IF NOT EXISTS `page` (                                                 
    `id` int(11) NOT NULL auto_increment,                                           
    `page_title` varchar(255) NOT NULL,                                             
    `page_description` longtext,                                                    
    `page_tab` varchar(100) NOT NULL,                                               
    `page_active` tinyint(4) NOT NULL default '0',                                  
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


/**
* Table structure for table `layer`
* 
*/

CREATE TABLE IF NOT EXISTS `layer` (                                                
    `id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,                            
    `layer_name` VARCHAR( 255 ) NULL ,                                              
    `layer_url` VARCHAR( 255 ) NULL ,                                               
    `layer_file` VARCHAR( 100 ) NULL ,                                              
    `layer_color` VARCHAR( 20 ) NULL ,                                              
    `layer_visible` TINYINT NOT NULL DEFAULT '1'                                    
) ENGINE = MYISAM  DEFAULT CHARSET=utf8;


--
-- Definition of table `api_settings`
--

CREATE TABLE IF NOT EXISTS `api_settings` (                                         
  `id` int(11) NOT NULL AUTO_INCREMENT,	                                            
  `default_record_limit` int(11) NOT NULL DEFAULT '20',                             
  `max_record_limit` int(11) DEFAULT NULL,                                          
  `max_requests_per_ip_address` int(11) DEFAULT NULL,                               
  `max_requests_quota_basis` int(11) DEFAULT NULL,                                  
  `modification_date` datetime NOT NULL,                                            
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='For storing API logging settings';

/**
* Table structure for table `api_banned`
* 
*/

CREATE TABLE IF NOT EXISTS `api_banned` (                                           
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,                                  
    `banned_ipaddress` varchar(50) NOT NULL,                                        
    `banned_date` datetime NOT NULL,                                                
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='For logging banned API IP addresses' AUTO_INCREMENT=8 ;


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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


/**
* Table structure for table `api_log`
* 
*/

CREATE TABLE IF NOT EXISTS `api_log` (                                              
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,                                  
    `api_task` varchar(10) NOT NULL,                                                
    `api_parameters` varchar(100) NOT NULL,                                          
    `api_records` tinyint(11) NOT NULL,                                             
    `api_ipaddress` varchar(50) NOT NULL,                                           
    `api_date` datetime NOT NULL,                                                   
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='For logging API activities' AUTO_INCREMENT=19 ;

/**
* Table structure for table `plugin`
* 
*/

CREATE TABLE IF NOT EXISTS `plugin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(100) NOT NULL,
  `plugin_url` varchar(250) NULL,
  `plugin_description` text NULL,
  `plugin_priority` tinyint(4) DEFAULT '0',
  `plugin_active` tinyint(4) DEFAULT '0',
  `plugin_installed` tinyint(4) DEFAULT '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `plugin_name` (`plugin_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


/**
* Table structure for table `geometry`
*/
CREATE TABLE IF NOT EXISTS `geometry` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `incident_id` bigint(20) NOT NULL,
  `geometry` geometry NOT NULL,
  `geometry_label` varchar(150) DEFAULT NULL,
  `geometry_comment` varchar(255) DEFAULT NULL,
  `geometry_color` varchar(20) DEFAULT NULL,
  `geometry_strokewidth` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  SPATIAL KEY `geometry` (`geometry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/**
* Table structure for table `checkin`
*/
CREATE TABLE IF NOT EXISTS `checkin`
(
`id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
`user_id` INT UNSIGNED NOT NULL,
`location_id` BIGINT UNSIGNED NOT NULL,
`incident_id` int(11) DEFAULT '0',
`checkin_description` VARCHAR(255),
`checkin_date` DATETIME NOT NULL,
`checkin_auto` ENUM('0','1') DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/**
* Table structure for table `user_devices`
*/
CREATE TABLE IF NOT EXISTS `user_devices` (
  `id` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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
* Constraints for dumped tables
* 
*/

/**
* Constraints for table `form_field`
*/
ALTER TABLE `form_field`
  ADD CONSTRAINT `form_field_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`) ON DELETE CASCADE;

/**
* Constraints for table `form_response`
*/
ALTER TABLE `form_response`
  ADD CONSTRAINT `form_response_ibfk_1` FOREIGN KEY (`form_field_id`) REFERENCES `form_field` (`id`) ON DELETE CASCADE;
  
/**
* Table Structure for table `form_field_option`
*/
CREATE TABLE IF NOT EXISTS `form_field_option` (
	`id` int(11) NOT NULL auto_increment,
	`form_field_id` int(11) NOT NULL default '0',
	`option_name` varchar(200) default NULL,
	`option_value` text default NULL,
	PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;




/**
* Add fields to table `form_field`
*/

ALTER TABLE `form_field` ADD `field_ispublic_visible` tinyint(4) NOT NULL default '0';
 
ALTER TABLE `form_field` ADD  `field_ispublic_submit` tinyint(4) NOT NULL default '0';


/**
* Add field to table `roles`
*/

ALTER TABLE `roles` ADD `access_level` tinyint(4) NOT NULL default '0';


/**
* Version information for table `settings`
* 
*/
UPDATE `settings` SET `ushahidi_version` = '2.1' WHERE `id`=1 LIMIT 1;
UPDATE `settings` SET `db_version` = '65' WHERE `id`=1 LIMIT 1;
