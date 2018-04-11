CREATE TABLE IF NOT EXISTS `#__vmmigrate_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `extension` VARCHAR( 50 ) NOT NULL,
  `task` varchar(50) NOT NULL,
  `note` mediumtext NOT NULL,
  `source_id` varchar(10) NOT NULL,
  `state` int(4) unsigned NOT NULL DEFAULT '1',
  `destination_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__vmmigrate_temp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
