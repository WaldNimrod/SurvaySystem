ALTER TABLE `companies` ADD `parentId` INT(11) NULL DEFAULT NULL AFTER `contactEmail`, ADD `lastPasswordChange` INT(11) NULL DEFAULT NULL AFTER `parentId`;
DROP TABLE IF EXISTS `logs`;
CREATE TABLE `logs` (
  `id` int(11) NOT NULL auto_increment,
  `created` varchar(255) default NULL,
  `username` varchar(255) default NULL,
  `result` varchar(255) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
