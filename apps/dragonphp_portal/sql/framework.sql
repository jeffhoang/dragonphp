SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `organizations` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(128) collate utf8_bin NOT NULL default '',
  `city` varchar(128) collate utf8_bin default NULL,
  `state` varchar(128) collate utf8_bin default NULL,
  `zipcode` varchar(12) collate utf8_bin default NULL,
  `province` varchar(128) collate utf8_bin default NULL,
  `country` varchar(128) collate utf8_bin default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

insert into `organizations` values('1','IT administration','sunnyvale','ca','94089','n/a','usa'),
 ('4','customer service','sunnyvale','ca','94089','','usa');

CREATE TABLE `role_permissions` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `role_id` int(3) unsigned NOT NULL,
  `permission` varchar(128) collate utf8_bin NOT NULL,
  `permission_type` varchar(64) collate utf8_bin NOT NULL,
  `pattern` tinytext collate utf8_bin,
  PRIMARY KEY  (`id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

insert into `role_permissions` values('1','1','crud','action',null),
 ('2','1','all','url',''),
 ('3','12','crud','action',null);

CREATE TABLE `roles` (
  `id` int(3) unsigned NOT NULL auto_increment,
  `name` varchar(36) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

insert into `roles` values('1','super'),
 ('12','customer service');

CREATE TABLE `user_roles` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(3) unsigned NOT NULL,
  PRIMARY KEY  (`user_id`,`role_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

insert into `user_roles` values('1','1'),
 ('6','12');

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `first_name` varchar(128) collate utf8_bin default NULL,
  `last_name` varchar(128) collate utf8_bin default NULL,
  `email` varchar(256) collate utf8_bin NOT NULL,
  `encrypted_password` varchar(64) collate utf8_bin NOT NULL,
  `organization_id` int(10) unsigned NOT NULL,
  `created_date` datetime NOT NULL,
  `updated_date` timestamp NULL default NULL on update CURRENT_TIMESTAMP,
  `is_active` int(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `organization_id` (`organization_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

insert into `users` values('1','jeff','dragon','dragon@yourcompany.com','CcKPa8ONRiHDk3PDisOeTsKDJifCtMO2','1','2010-03-22 12:11:48','2010-03-27 19:36:10','1'),
 ('6','bob','smith','bob@yourcompany.com','w5QdwozDmcKPAMKyBMOpwoAJwpjDrMO4Qn4=','4','2010-03-22 16:01:36','2010-03-23 17:19:32','1');

SET FOREIGN_KEY_CHECKS = 1;
