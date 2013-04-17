CREATE TABLE `user` (
  `id` varchar(64) character set utf8 NOT NULL default '',
  `user_id` varchar(32) NOT NULL default '',
  `password` varchar(32) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `date_created` datetime NOT NULL default '0000-00-00 00:00:00',
  `date_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `last_activity` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

CREATE TABLE `role` (
  `id` int(11) NOT NULL auto_increment,
  `parent_id` varchar(64) NOT NULL default '',
  `role` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  FOREIGN KEY (parent_id) REFERENCES user(id)
                      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8