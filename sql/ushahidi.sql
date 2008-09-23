-- Ushahidi Engine
-- version 0.1
-- http://www.ushahidi.com


SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE IF NOT EXISTS `category` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `category_type` tinyint(4) default NULL,
  `category_title` varchar(255) default NULL,
  `category_description` text,
  `category_color` varchar(20) default NULL,
  `category_visible` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `category_type`, `category_title`, `category_description`, `category_color`, `category_visible`) VALUES
(1, 5, 'RIOTS', 'RIOTS', '9900CC', 1),
(2, 5, 'DEATHS', 'DEATHS', '3300FF', 1),
(3, 5, 'PROPERTY LOSS', 'PROPERTY LOSS', '663300', 1),
(4, 5, 'SEXUAL ASSAULT', 'SEXUAL ASSAULT', 'CC0000', 1),
(5, 5, 'INTERNALLY DISPLACED PEOPLE ', 'INTERNALLY DISPLACED PEOPLE 	', 'CC9933', 1),
(6, 5, 'GOVERNMENT FORCESS', 'GOVERNMENT FORCESS', '9999FF', 1),
(7, 5, 'CIVILIANS', 'CIVILIANS', '66CC00', 1),
(8, 5, 'LOOTING', 'LOOTING', 'FFCC00', 1),
(9, 5, 'PEACE EFFORTS', 'PEACE EFFORTS', 'FAEBD7', 1);

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
  `id` int(11) NOT NULL auto_increment,
  `iso` varchar(10) default NULL,
  `country` varchar(100) default NULL,
  `capital` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `iso`, `country`, `capital`) VALUES
(1, 'AD', 'Andorra', 'Andorra la Vella'),
(2, 'AE', 'United Arab Emirates', 'Abu Dhabi'),
(3, 'AF', 'Afghanistan', 'Kabul'),
(4, 'AG', 'Antigua and Barbuda', 'St. John''s'),
(5, 'AI', 'Anguilla', 'The Valley'),
(6, 'AL', 'Albania', 'Tirana'),
(7, 'AM', 'Armenia', 'Yerevan'),
(8, 'AN', 'Netherlands Antilles', 'Willemstad'),
(9, 'AO', 'Angola', 'Luanda'),
(10, 'AQ', 'Antarctica', ''),
(11, 'AR', 'Argentina', 'Buenos Aires'),
(12, 'AS', 'American Samoa', 'Pago Pago'),
(13, 'AT', 'Austria', 'Vienna'),
(14, 'AU', 'Australia', 'Canberra'),
(15, 'AW', 'Aruba', 'Oranjestad'),
(16, 'AX', 'Aland Islands', 'Mariehamn'),
(17, 'AZ', 'Azerbaijan', 'Baku'),
(18, 'BA', 'Bosnia and Herzegovina', 'Sarajevo'),
(19, 'BB', 'Barbados', 'Bridgetown'),
(20, 'BD', 'Bangladesh', 'Dhaka'),
(21, 'BE', 'Belgium', 'Brussels'),
(22, 'BF', 'Burkina Faso', 'Ouagadougou'),
(23, 'BG', 'Bulgaria', 'Sofia'),
(24, 'BH', 'Bahrain', 'Manama'),
(25, 'BI', 'Burundi', 'Bujumbura'),
(26, 'BJ', 'Benin', 'Porto-Novo'),
(27, 'BL', 'Saint Barthélemy', 'Gustavia'),
(28, 'BM', 'Bermuda', 'Hamilton'),
(29, 'BN', 'Brunei', 'Bandar Seri Begawan'),
(30, 'BO', 'Bolivia', 'La Paz'),
(31, 'BR', 'Brazil', 'Brasília'),
(32, 'BS', 'Bahamas', 'Nassau'),
(33, 'BT', 'Bhutan', 'Thimphu'),
(34, 'BV', 'Bouvet Island', ''),
(35, 'BW', 'Botswana', 'Gaborone'),
(36, 'BY', 'Belarus', 'Minsk'),
(37, 'BZ', 'Belize', 'Belmopan'),
(38, 'CA', 'Canada', 'Ottawa'),
(39, 'CC', 'Cocos Islands', 'West Island'),
(40, 'CD', 'Congo - Kinshasa', 'Kinshasa'),
(41, 'CF', 'Central African Republic', 'Bangui'),
(42, 'CG', 'Congo - Brazzaville', 'Brazzaville'),
(43, 'CH', 'Switzerland', 'Berne'),
(44, 'CI', 'Ivory Coast', 'Yamoussoukro'),
(45, 'CK', 'Cook Islands', 'Avarua'),
(46, 'CL', 'Chile', 'Santiago'),
(47, 'CM', 'Cameroon', 'Yaoundé'),
(48, 'CN', 'China', 'Beijing'),
(49, 'CO', 'Colombia', 'Bogotá'),
(50, 'CR', 'Costa Rica', 'San José'),
(51, 'CS', 'Serbia and Montenegro', 'Belgrade'),
(52, 'CU', 'Cuba', 'Havana'),
(53, 'CV', 'Cape Verde', 'Praia'),
(54, 'CX', 'Christmas Island', 'Flying Fish Cove'),
(55, 'CY', 'Cyprus', 'Nicosia'),
(56, 'CZ', 'Czech Republic', 'Prague'),
(57, 'DE', 'Germany', 'Berlin'),
(58, 'DJ', 'Djibouti', 'Djibouti'),
(59, 'DK', 'Denmark', 'Copenhagen'),
(60, 'DM', 'Dominica', 'Roseau'),
(61, 'DO', 'Dominican Republic', 'Santo Domingo'),
(62, 'DZ', 'Algeria', 'Algiers'),
(63, 'EC', 'Ecuador', 'Quito'),
(64, 'EE', 'Estonia', 'Tallinn'),
(65, 'EG', 'Egypt', 'Cairo'),
(66, 'EH', 'Western Sahara', 'El-Aaiun'),
(67, 'ER', 'Eritrea', 'Asmara'),
(68, 'ES', 'Spain', 'Madrid'),
(69, 'ET', 'Ethiopia', 'Addis Ababa'),
(70, 'FI', 'Finland', 'Helsinki'),
(71, 'FJ', 'Fiji', 'Suva'),
(72, 'FK', 'Falkland Islands', 'Stanley'),
(73, 'FM', 'Micronesia', 'Palikir'),
(74, 'FO', 'Faroe Islands', 'Tórshavn'),
(75, 'FR', 'France', 'Paris'),
(76, 'GA', 'Gabon', 'Libreville'),
(77, 'GB', 'United Kingdom', 'London'),
(78, 'GD', 'Grenada', 'St. George''s'),
(79, 'GE', 'Georgia', 'Tbilisi'),
(80, 'GF', 'French Guiana', 'Cayenne'),
(81, 'GG', 'Guernsey', 'St Peter Port'),
(82, 'GH', 'Ghana', 'Accra'),
(83, 'GI', 'Gibraltar', 'Gibraltar'),
(84, 'GL', 'Greenland', 'Nuuk'),
(85, 'GM', 'Gambia', 'Banjul'),
(86, 'GN', 'Guinea', 'Conakry'),
(87, 'GP', 'Guadeloupe', 'Basse-Terre'),
(88, 'GQ', 'Equatorial Guinea', 'Malabo'),
(89, 'GR', 'Greece', 'Athens'),
(90, 'GS', 'South Georgia and the South Sandwich Islands', 'Grytviken'),
(91, 'GT', 'Guatemala', 'Guatemala City'),
(92, 'GU', 'Guam', 'Hagåtña'),
(93, 'GW', 'Guinea-Bissau', 'Bissau'),
(94, 'GY', 'Guyana', 'Georgetown'),
(95, 'HK', 'Hong Kong', 'Hong Kong'),
(96, 'HM', 'Heard Island and McDonald Islands', ''),
(97, 'HN', 'Honduras', 'Tegucigalpa'),
(98, 'HR', 'Croatia', 'Zagreb'),
(99, 'HT', 'Haiti', 'Port-au-Prince'),
(100, 'HU', 'Hungary', 'Budapest'),
(101, 'ID', 'Indonesia', 'Jakarta'),
(102, 'IE', 'Ireland', 'Dublin'),
(103, 'IL', 'Israel', 'Jerusalem'),
(104, 'IM', 'Isle of Man', 'Douglas, Isle of Man'),
(105, 'IN', 'India', 'New Delhi'),
(106, 'IO', 'British Indian Ocean Territory', 'Diego Garcia'),
(107, 'IQ', 'Iraq', 'Baghdad'),
(108, 'IR', 'Iran', 'Tehran'),
(109, 'IS', 'Iceland', 'Reykjavík'),
(110, 'IT', 'Italy', 'Rome'),
(111, 'JE', 'Jersey', 'Saint Helier'),
(112, 'JM', 'Jamaica', 'Kingston'),
(113, 'JO', 'Jordan', 'Amman'),
(114, 'JP', 'Japan', 'Tokyo'),
(115, 'KE', 'Kenya', 'Nairobi'),
(116, 'KG', 'Kyrgyzstan', 'Bishkek'),
(117, 'KH', 'Cambodia', 'Phnom Penh'),
(118, 'KI', 'Kiribati', 'South Tarawa'),
(119, 'KM', 'Comoros', 'Moroni'),
(120, 'KN', 'Saint Kitts and Nevis', 'Basseterre'),
(121, 'KP', 'North Korea', 'Pyongyang'),
(122, 'KR', 'South Korea', 'Seoul'),
(123, 'KW', 'Kuwait', 'Kuwait City'),
(124, 'KY', 'Cayman Islands', 'George Town'),
(125, 'KZ', 'Kazakhstan', 'Astana'),
(126, 'LA', 'Laos', 'Vientiane'),
(127, 'LB', 'Lebanon', 'Beirut'),
(128, 'LC', 'Saint Lucia', 'Castries'),
(129, 'LI', 'Liechtenstein', 'Vaduz'),
(130, 'LK', 'Sri Lanka', 'Colombo'),
(131, 'LR', 'Liberia', 'Monrovia'),
(132, 'LS', 'Lesotho', 'Maseru'),
(133, 'LT', 'Lithuania', 'Vilnius'),
(134, 'LU', 'Luxembourg', 'Luxembourg'),
(135, 'LV', 'Latvia', 'Riga'),
(136, 'LY', 'Libya', 'Tripolis'),
(137, 'MA', 'Morocco', 'Rabat'),
(138, 'MC', 'Monaco', 'Monaco'),
(139, 'MD', 'Moldova', 'Chi_in_u'),
(140, 'ME', 'Montenegro', 'Podgorica'),
(141, 'MF', 'Saint Martin', 'Marigot'),
(142, 'MG', 'Madagascar', 'Antananarivo'),
(143, 'MH', 'Marshall Islands', 'Uliga'),
(144, 'MK', 'Macedonia', 'Skopje'),
(145, 'ML', 'Mali', 'Bamako'),
(146, 'MM', 'Myanmar', 'Yangon'),
(147, 'MN', 'Mongolia', 'Ulan Bator'),
(148, 'MO', 'Macao', 'Macao'),
(149, 'MP', 'Northern Mariana Islands', 'Saipan'),
(150, 'MQ', 'Martinique', 'Fort-de-France'),
(151, 'MR', 'Mauritania', 'Nouakchott'),
(152, 'MS', 'Montserrat', 'Plymouth'),
(153, 'MT', 'Malta', 'Valletta'),
(154, 'MU', 'Mauritius', 'Port Louis'),
(155, 'MV', 'Maldives', 'Malé'),
(156, 'MW', 'Malawi', 'Lilongwe'),
(157, 'MX', 'Mexico', 'Mexico City'),
(158, 'MY', 'Malaysia', 'Kuala Lumpur'),
(159, 'MZ', 'Mozambique', 'Maputo'),
(160, 'NA', 'Namibia', 'Windhoek'),
(161, 'NC', 'New Caledonia', 'Nouméa'),
(162, 'NE', 'Niger', 'Niamey'),
(163, 'NF', 'Norfolk Island', 'Kingston'),
(164, 'NG', 'Nigeria', 'Abuja'),
(165, 'NI', 'Nicaragua', 'Managua'),
(166, 'NL', 'Netherlands', 'Amsterdam'),
(167, 'NO', 'Norway', 'Oslo'),
(168, 'NP', 'Nepal', 'Kathmandu'),
(169, 'NR', 'Nauru', 'Yaren'),
(170, 'NU', 'Niue', 'Alofi'),
(171, 'NZ', 'New Zealand', 'Wellington'),
(172, 'OM', 'Oman', 'Muscat'),
(173, 'PA', 'Panama', 'Panama City'),
(174, 'PE', 'Peru', 'Lima'),
(175, 'PF', 'French Polynesia', 'Papeete'),
(176, 'PG', 'Papua New Guinea', 'Port Moresby'),
(177, 'PH', 'Philippines', 'Manila'),
(178, 'PK', 'Pakistan', 'Islamabad'),
(179, 'PL', 'Poland', 'Warsaw'),
(180, 'PM', 'Saint Pierre and Miquelon', 'Saint-Pierre'),
(181, 'PN', 'Pitcairn', 'Adamstown'),
(182, 'PR', 'Puerto Rico', 'San Juan'),
(183, 'PS', 'Palestinian Territory', 'East Jerusalem'),
(184, 'PT', 'Portugal', 'Lisbon'),
(185, 'PW', 'Palau', 'Koror'),
(186, 'PY', 'Paraguay', 'Asunción'),
(187, 'QA', 'Qatar', 'Doha'),
(188, 'RE', 'Reunion', 'Saint-Denis'),
(189, 'RO', 'Romania', 'Bucharest'),
(190, 'RS', 'Serbia', 'Belgrade'),
(191, 'RU', 'Russia', 'Moscow'),
(192, 'RW', 'Rwanda', 'Kigali'),
(193, 'SA', 'Saudi Arabia', 'Riyadh'),
(194, 'SB', 'Solomon Islands', 'Honiara'),
(195, 'SC', 'Seychelles', 'Victoria'),
(196, 'SD', 'Sudan', 'Khartoum'),
(197, 'SE', 'Sweden', 'Stockholm'),
(198, 'SG', 'Singapore', 'Singapur'),
(199, 'SH', 'Saint Helena', 'Jamestown'),
(200, 'SI', 'Slovenia', 'Ljubljana'),
(201, 'SJ', 'Svalbard and Jan Mayen', 'Longyearbyen'),
(202, 'SK', 'Slovakia', 'Bratislava'),
(203, 'SL', 'Sierra Leone', 'Freetown'),
(204, 'SM', 'San Marino', 'San Marino'),
(205, 'SN', 'Senegal', 'Dakar'),
(206, 'SO', 'Somalia', 'Mogadishu'),
(207, 'SR', 'Suriname', 'Paramaribo'),
(208, 'ST', 'Sao Tome and Principe', 'São Tomé'),
(209, 'SV', 'El Salvador', 'San Salvador'),
(210, 'SY', 'Syria', 'Damascus'),
(211, 'SZ', 'Swaziland', 'Mbabane'),
(212, 'TC', 'Turks and Caicos Islands', 'Cockburn Town'),
(213, 'TD', 'Chad', 'N''Djamena'),
(214, 'TF', 'French Southern Territories', 'Martin-de-Viviès'),
(215, 'TG', 'Togo', 'Lomé'),
(216, 'TH', 'Thailand', 'Bangkok'),
(217, 'TJ', 'Tajikistan', 'Dushanbe'),
(218, 'TK', 'Tokelau', ''),
(219, 'TL', 'East Timor', 'Dili'),
(220, 'TM', 'Turkmenistan', 'Ashgabat'),
(221, 'TN', 'Tunisia', 'Tunis'),
(222, 'TO', 'Tonga', 'Nuku''alofa'),
(223, 'TR', 'Turkey', 'Ankara'),
(224, 'TT', 'Trinidad and Tobago', 'Port of Spain'),
(225, 'TV', 'Tuvalu', 'Vaiaku'),
(226, 'TW', 'Taiwan', 'Taipei'),
(227, 'TZ', 'Tanzania', 'Dar es Salaam'),
(228, 'UA', 'Ukraine', 'Kiev'),
(229, 'UG', 'Uganda', 'Kampala'),
(230, 'UM', 'United States Minor Outlying Islands', ''),
(231, 'US', 'United States', 'Washington'),
(232, 'UY', 'Uruguay', 'Montevideo'),
(233, 'UZ', 'Uzbekistan', 'Tashkent'),
(234, 'VA', 'Vatican', 'Vatican City'),
(235, 'VC', 'Saint Vincent and the Grenadines', 'Kingstown'),
(236, 'VE', 'Venezuela', 'Caracas'),
(237, 'VG', 'British Virgin Islands', 'Road Town'),
(238, 'VI', 'U.S. Virgin Islands', 'Charlotte Amalie'),
(239, 'VN', 'Vietnam', 'Hanoi'),
(240, 'VU', 'Vanuatu', 'Port Vila'),
(241, 'WF', 'Wallis and Futuna', 'Matâ''Utu'),
(242, 'WS', 'Samoa', 'Apia'),
(243, 'YE', 'Yemen', 'San‘a’'),
(244, 'YT', 'Mayotte', 'Mamoudzou'),
(245, 'ZA', 'South Africa', 'Pretoria'),
(246, 'ZM', 'Zambia', 'Lusaka'),
(247, 'ZW', 'Zimbabwe', 'Harare');

-- --------------------------------------------------------

--
-- Table structure for table `idp`
--

CREATE TABLE IF NOT EXISTS `idp` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `incident_id` bigint(20) NOT NULL,
  `verified_id` bigint(20) default NULL,
  `idp_idnumber` varchar(100) default NULL,
  `idp_orig_idnumber` varchar(100) default NULL,
  `idp_fname` varchar(50) default NULL,
  `idp_lname` varchar(50) default NULL,
  `idp_email` varchar(100) default NULL,
  `idp_phone` varchar(50) default NULL,
  `current_location_id` bigint(20) default NULL,
  `displacedfrom_location_id` bigint(20) default NULL,
  `movedto_location_id` bigint(20) default NULL,
  `idp_move_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `idp`
--


-- --------------------------------------------------------

--
-- Table structure for table `incident`
--

CREATE TABLE IF NOT EXISTS `incident` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `location_id` bigint(20) NOT NULL,
  `user_id` bigint(20) default NULL,
  `incident_title` varchar(255) default NULL,
  `incident_description` longtext,
  `incident_date` datetime default NULL,
  `incident_mode` tinyint(4) NOT NULL default '1' COMMENT '1 - WEB, 2 - SMS, 3 - EMAIL',
  `incident_active` tinyint(4) NOT NULL default '0',
  `incident_verified` tinyint(4) NOT NULL default '0',
  `incident_dateadd` datetime default NULL,
  `incident_datemodify` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `location_id` (`location_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `incident`
--


-- --------------------------------------------------------

--
-- Table structure for table `incident_category`
--

CREATE TABLE IF NOT EXISTS `incident_category` (
  `id` int(11) NOT NULL auto_increment,
  `incident_id` bigint(20) NOT NULL default '0',
  `category_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `incident_category`
--


-- --------------------------------------------------------

--
-- Table structure for table `incident_person`
--

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `incident_person`
--


-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE IF NOT EXISTS `location` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `location_name` varchar(255) default NULL,
  `country_id` int(11) default NULL,
  `latitude` varchar(50) default NULL,
  `longitude` varchar(50) default NULL,
  `location_visible` tinyint(4) NOT NULL default '1',
  `location_date` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `location`
--


-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `location_id` bigint(20) default NULL,
  `incident_id` bigint(20) default NULL,
  `media_type` tinyint(4) default NULL COMMENT '1 - IMAGES, 2 - VIDEO, 3 - AUDIO, 4 - NEWS, 5 - PODCAST',
  `media_title` varchar(255) default NULL,
  `media_description` longtext,
  `media_link` varchar(255) default NULL,
  `media_thumb` varchar(255) default NULL,
  `media_date` datetime default NULL,
  `media_active` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `media`
--


-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE IF NOT EXISTS `organization` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `organization_name` varchar(255) default NULL,
  `organization_description` longtext,
  `organization_website` varchar(255) default NULL,
  `organization_address` varchar(255) default NULL,
  `organization_country` varchar(100) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organization`
--


-- --------------------------------------------------------

--
-- Table structure for table `organization_incident`
--

CREATE TABLE IF NOT EXISTS `organization_incident` (
  `organization_id` bigint(20) default NULL,
  `incident_id` bigint(20) default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organization_incident`
--


-- --------------------------------------------------------

--
-- Table structure for table `pending_users`
--

CREATE TABLE IF NOT EXISTS `pending_users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `key` varchar(32) NOT NULL,
  `email` varchar(127) NOT NULL,
  `username` varchar(31) NOT NULL default '',
  `password` char(50) default NULL,
  `created` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pending_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'user', 'Login privileges, granted after account confirmation'),
(2, 'admin', 'Administrative user, has access to everything.');

-- --------------------------------------------------------

--
-- Table structure for table `roles_users`
--

CREATE TABLE IF NOT EXISTS `roles_users` (
  `user_id` int(11) unsigned NOT NULL,
  `role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `fk_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `roles_users`
--

INSERT INTO `roles_users` (`user_id`, `role_id`) VALUES
(1, 1),
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `session_id` varchar(40) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `sessions`
--


-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `site_name` varchar(255) default NULL,
  `default_map` tinyint(4) NOT NULL default '1' COMMENT '1 - GOOGLE MAPS, 2 - LIVE MAPS, 3 - YAHOO MAPS, 4 - OPEN STREET MAPS',
  `api_google` varchar(200) default NULL,
  `api_yahoo` varchar(200) default NULL,
  `api_live` varchar(200) default NULL,
  `default_country` int(11) default NULL,
  `default_city` varchar(150) default NULL,
  `default_lat` varchar(100) default NULL,
  `default_lon` varchar(100) default NULL,
  `default_zoom` tinyint(4) NOT NULL default '10',
  `items_per_page` smallint(6) NOT NULL default '20',
  `items_per_page_admin` smallint(6) NOT NULL default '20',
  `date_modify` datetime default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `default_map`, `api_google`, `api_yahoo`, `api_live`, `default_country`, `default_city`, `default_lat`, `default_lon`, `default_zoom`, `items_per_page`, `items_per_page_admin`, `date_modify`) VALUES
(1, 'Ushahidi Beta', 1, 'ABQIAAAAjsEM5UsvCPCIHp80spK1kBQKW7L4j6gYznY0oMkScAbKwifzxxRhJ3SP_ijydkmJpN3jX8kn5r5fEQ', '5CYeWbfV34E21JOW1a4.54Mf6e9jLNkD0HVzaKoQmJZi2qzmSZd5mD8X49x7', NULL, 115, 'nairobi', '-1.2873000707050097', '36.821451182008204', 13, 20, 20, '2008-08-25 10:25:18');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(200) default NULL,
  `email` varchar(127) NOT NULL,
  `username` varchar(31) NOT NULL default '',
  `password` char(50) NOT NULL,
  `logins` int(10) unsigned NOT NULL default '0',
  `last_login` int(10) unsigned default NULL,
  `updated` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uniq_username` (`username`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `logins`, `last_login`, `updated`) VALUES
(1, 'Administrator', 'david@ushahidi.com', 'admin', 'bae4b17e9acbabf959654a4c496e577003e0b887c6f52803d7', 290, 1221420023, '2008-09-14 14:17:22');

-- --------------------------------------------------------

--
-- Table structure for table `user_tokens`
--

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

--
-- Dumping data for table `user_tokens`
--


-- --------------------------------------------------------

--
-- Table structure for table `verified`
--

CREATE TABLE IF NOT EXISTS `verified` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `incident_id` bigint(20) default NULL,
  `idp_id` bigint(20) default NULL,
  `user_id` int(11) default NULL,
  `verified_comment` longtext,
  `verified_date` datetime default NULL,
  `verified_status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `verified`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `roles_users`
--
ALTER TABLE `roles_users`
  ADD CONSTRAINT `roles_users_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `roles_users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_tokens`
--
ALTER TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
