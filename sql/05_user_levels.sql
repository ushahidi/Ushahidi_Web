CREATE TABLE `service`
(
`id` INT unsigned  NOT NULL AUTO_INCREMENT,
`service_name` VARCHAR(100),
`service_description` VARCHAR(255),
`service_url` VARCHAR(255),
`service_api` VARCHAR(255),
PRIMARY KEY (`id`)
);

INSERT INTO service (id, service_name, service_description, service_url) 
VALUES (1,'SMS','Text messages from phones', NULL), 
VALUES (2,'Twitter','Tweets tweets tweets', 'http://twitter.com'),
VALUES (3,'Flickr','Photo sharing', 'http://flickr.com')
;


ALTER TABLE `message`
ADD COLUMN (
`service_messageid` VARCHAR(100), 
`reporter_id` BIGINT
);


CREATE TABLE `reporter`
(
`id` BIGINT(20) unsigned  NOT NULL AUTO_INCREMENT,
`incident_id` BIGINT(20),
`location_id` BIGINT(20),
`user_id` INT,
`service_id` INT,
`service_userid` VARCHAR(255),
`service_username` VARCHAR(255),
`reporter_level` TINYINT DEFAULT 3,
`reporter_first` VARCHAR(200),
`reporter_last` VARCHAR(200),
`reporter_email` VARCHAR(120),
`reporter_phone` VARCHAR(60),
`reporter_ip` VARCHAR(50),
`reporter_date` DATETIME,
PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='InnoDB free: 382976 kB';


CREATE TABLE `level`
(
`id` BIGINT(20) unsigned  NOT NULL AUTO_INCREMENT,
`level_title` VARCHAR(200),
`level_description` VARCHAR(200),
`level_weight` TINYINT NOT NULL,
PRIMARY KEY (`id`)
) ENGINE=InnoDB;


INSERT INTO `level` (`id`, `level_title`, `level_description`, `level_weight`) VALUES
(1, 'SPAM + Delete', 'SPAM + Delete', -2),
(2, 'SPAM', 'SPAM', -1),
(3, 'Untrusted', 'Untrusted', 0),
(4, 'Trusted', 'Trusted', 1),
(5, 'Trusted + Verifiy', 'Trusted + Verify', 2);


CREATE TABLE `incident`
(
`id` BIGINT(20) unsigned  NOT NULL AUTO_INCREMENT,
`location_id` BIGINT(20) NOT NULL UNIQUE,
`incident_person_id` BIGINT,
`locale` VARCHAR(10) DEFAULT 'en_US' NOT NULL,
`user_id` BIGINT(20),
`incident_title` VARCHAR(255),
`incident_description` LONGTEXT,
`incident_date` DATETIME,
`incident_mode` TINYINT(4) DEFAULT 1 NOT NULL COMMENT '1 - WEB, 2 - SMS, 3 - EMAIL, 4 - TWITTER',
`incident_active` TINYINT(4) DEFAULT 0 NOT NULL,
`incident_verified` TINYINT(4) DEFAULT 0 NOT NULL,
`incident_rating` VARCHAR(15) DEFAULT '0' NOT NULL,
`incident_dateadd` DATETIME,
`incident_datemodify` DATETIME,
PRIMARY KEY (`id`)
) ENGINE=InnoDB COMMENT='InnoDB free: 0 kB';


CREATE INDEX `users_uniq_username` ON `users` (`username`);
CREATE INDEX `users_uniq_email` ON `users` (`email`);
