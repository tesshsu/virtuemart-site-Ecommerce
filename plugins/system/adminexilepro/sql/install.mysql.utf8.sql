CREATE TABLE IF NOT EXISTS `#__adminexilepro` (
  `ts` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `address` varchar(48) NOT NULL,
  `rule` varchar(48) NOT NULL,
  `fail` int(11) NOT NULL DEFAULT '0',
  `expire` timestamp NOT NULL DEFAULT 0,
  UNIQUE KEY `entry` (`ts`,`type`,`address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;