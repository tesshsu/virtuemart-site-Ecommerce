CREATE TABLE IF NOT EXISTS `#__rokgallery_files` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `guid` char(36) NOT NULL,
  `md5` char(32) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text,
  `license` varchar(255) DEFAULT NULL,
  `xsize` int(10) UNSIGNED NOT NULL,
  `ysize` int(10) UNSIGNED NOT NULL,
  `filesize` int(10) UNSIGNED NOT NULL,
  `type` char(20) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rokgallery_files_guid_idx` (`guid`),
  UNIQUE KEY `files_sluggable_idx` (`slug`),
  KEY `rokgallery_files_published_idx` (`published`),
  KEY `rokgallery_files_md5_idx` (`md5`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_files_index` (
  `keyword` varchar(200) NOT NULL DEFAULT '',
  `field` varchar(50) NOT NULL DEFAULT '',
  `position` bigint(20) NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`keyword`,`field`,`position`,`id`),
  KEY `rokgallery_files_index_id_idx` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_file_loves` (
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `kount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_file_tags` (
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tag` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`file_id`,`tag`),
  KEY `rokgallery_file_tags_file_id_idx` (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_file_views` (
  `file_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `kount` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_filters` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `query` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rokgallery_profiles_name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_galleries` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `filetags` longtext,
  `width` int(10) UNSIGNED NOT NULL DEFAULT '910',
  `height` int(10) UNSIGNED NOT NULL DEFAULT '500',
  `keep_aspect` tinyint(1) DEFAULT '0',
  `force_image_size` tinyint(1) DEFAULT '0',
  `thumb_xsize` int(10) UNSIGNED NOT NULL DEFAULT '190',
  `thumb_ysize` int(10) UNSIGNED NOT NULL DEFAULT '150',
  `thumb_background` varchar(12) DEFAULT NULL,
  `thumb_keep_aspect` tinyint(1) DEFAULT '0',
  `auto_publish` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `rokgallery_galleries_auto_publish_idx` (`auto_publish`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_jobs` (
  `id` char(36) NOT NULL DEFAULT '',
  `type` varchar(255) NOT NULL,
  `properties` text,
  `state` varchar(255) NOT NULL,
  `status` text,
  `percent` bigint(20) UNSIGNED DEFAULT NULL,
  `sm` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_profiles` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `profile` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `rokgallery_profiles_name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_schema_version` (
  `version` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_slices` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` int(10) UNSIGNED NOT NULL,
  `gallery_id` int(10) UNSIGNED DEFAULT NULL,
  `guid` char(36) NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `caption` text,
  `link` text,
  `filesize` int(10) UNSIGNED NOT NULL,
  `xsize` int(10) UNSIGNED NOT NULL,
  `ysize` int(10) UNSIGNED NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `admin_thumb` tinyint(1) NOT NULL DEFAULT '0',
  `manipulations` longtext,
  `palette` text,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `thumb_xsize` int(10) UNSIGNED NOT NULL,
  `thumb_ysize` int(10) UNSIGNED NOT NULL,
  `thumb_keep_aspect` tinyint(1) NOT NULL DEFAULT '1',
  `thumb_background` varchar(12) DEFAULT NULL,
  `ordering` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rokgallery_slices_sluggable_idx` (`slug`,`gallery_id`),
  UNIQUE KEY `rokgallery_slices_guid_idx` (`guid`),
  KEY `rokgallery_slices_published_idx` (`published`),
  KEY `file_id_idx` (`file_id`),
  KEY `gallery_id_idx` (`gallery_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_slices_index` (
  `keyword` varchar(200) NOT NULL DEFAULT '',
  `field` varchar(50) NOT NULL DEFAULT '',
  `position` bigint(20) NOT NULL DEFAULT '0',
  `id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`keyword`,`field`,`position`,`id`),
  KEY `rokgallery_slices_index_id_idx` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__rokgallery_slice_tags` (
  `slice_id` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `tag` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`slice_id`,`tag`),
  KEY `rokgallery_slice_tags_slice_id_idx` (`slice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `#__rokgallery_files_index`
  ADD CONSTRAINT `rokgallery_files_index_id_idx` FOREIGN KEY (`id`) REFERENCES `#__rokgallery_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__rokgallery_file_loves`
  ADD CONSTRAINT `file_loves_file_id_files_id` FOREIGN KEY (`file_id`) REFERENCES `#__rokgallery_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__rokgallery_file_tags`
  ADD CONSTRAINT `file_tags_file_id_files_id` FOREIGN KEY (`file_id`) REFERENCES `#__rokgallery_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__rokgallery_file_views`
  ADD CONSTRAINT `file_views_file_id_files_id` FOREIGN KEY (`file_id`) REFERENCES `#__rokgallery_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__rokgallery_slices`
  ADD CONSTRAINT `slices_file_id_files_id` FOREIGN KEY (`file_id`) REFERENCES `#__rokgallery_files` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `slices_gallery_id_galleries_id` FOREIGN KEY (`gallery_id`) REFERENCES `#__rokgallery_galleries` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `#__rokgallery_slices_index`
  ADD CONSTRAINT `rokgallery_slices_index_id_idx` FOREIGN KEY (`id`) REFERENCES `#__rokgallery_slices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `#__rokgallery_slice_tags`
  ADD CONSTRAINT `slice_tags_slice_id_slices_id` FOREIGN KEY (`slice_id`) REFERENCES `#__rokgallery_slices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

INSERT IGNORE INTO `#__rokgallery_schema_version` (`version`)
VALUES (2);
