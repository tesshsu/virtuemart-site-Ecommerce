CREATE TABLE IF NOT EXISTS `#__wishlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `virtuemart_product_id` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
)  DEFAULT CHARSET=utf8  ;

CREATE TABLE IF NOT EXISTS `#__ztvirtuemarter` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting` LONGTEXT NOT NULL,
   PRIMARY KEY  (`id`)
) DEFAULT CHARSET=utf8;
