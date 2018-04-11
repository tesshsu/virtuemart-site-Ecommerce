DROP TABLE IF EXISTS `#__huge_itslideshow_slideshows`;

CREATE TABLE `#__huge_itslideshow_slideshows` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `sl_height` int(11) unsigned DEFAULT NULL,
  `sl_width` int(11) unsigned DEFAULT NULL,
  `pause_on_hover` text,
  `slideshow_list_effects_s` text,
  `description` text,
  `param` text,
  `sl_position` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `#__huge_itslideshow_images`;

CREATE TABLE IF NOT EXISTS `#__huge_itslideshow_images` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `slideshow_id` varchar(200) DEFAULT NULL,
  `description` text,
  `image_url` text,
  `sl_url` varchar(128) DEFAULT NULL,
  `sl_type` text NOT NULL,
  `link_target` text NOT NULL,
  `sl_stitle` text NOT NULL,
  `sl_sdesc` text NOT NULL,
  `sl_postlink` text NOT NULL,
  `ordering` int(11) NOT NULL,
  `published` tinyint(4) unsigned DEFAULT NULL,
  `published_in_sl_width` tinyint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;



DROP TABLE IF EXISTS `#__huge_itslideshow_params`;
CREATE TABLE IF NOT EXISTS `#__huge_itslideshow_params` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8 NOT NULL,
  `title` varchar(200) CHARACTER SET utf8 NOT NULL,
  `description` text CHARACTER SET utf8 NOT NULL,
  `value` varchar(200) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=132 DEFAULT CHARSET=latin1;

INSERT INTO `#__huge_itslideshow_params` (`id`, `name`, `title`, `description`, `value`) VALUES
(89, 'slideshow_crop_image', 'Slideshow crop image', 'Slideshow crop image', 'resize'),
(90, 'slideshow_title_color', 'Slideshow title color', 'Slideshow title color', '000000'),
(91, 'slideshow_title_font_size', 'Slideshow title font size', 'Slideshow title font size', '13'),
(92, 'slideshow_description_color', 'Slideshow description color', 'Slideshow description color', 'ffffff'),
(93, 'slideshow_description_font_size', 'Slideshow description font size', 'Slideshow description font size', '13'),
(94, 'slideshow_title_position', 'Slideshow title position', 'Slideshow title position', 'right-top'),
(95, 'slideshow_description_position', 'Slideshow description position', 'Slideshow description position', 'right-bottom'),
(96, 'slideshow_title_border_size', 'Slideshow Title border size', 'Slideshow Title border size', '0'),
(97, 'slideshow_title_border_color', 'Slideshow title border color', 'Slideshow title border color', 'ffffff'),
(98, 'slideshow_title_border_radius', 'Slideshow title border radius', 'Slideshow title border radius', '4'),
(99, 'slideshow_description_border_size', 'Slideshow description border size', 'Slideshow description border size', '0'),
(100, 'slideshow_description_border_color', 'Slideshow description border color', 'Slideshow description border color', 'ffffff'),
(101, 'slideshow_description_border_radius', 'Slideshow description border radius', 'Slideshow description border radius', '0'),
(102, 'slideshow_slideshow_border_size', 'Slideshow border size', 'Slideshow border size', '0'),
(103, 'slideshow_slideshow_border_color', 'Slideshow border color', 'Slideshow border color', 'ffffff'),
(104, 'slideshow_slideshow_border_radius', 'Slideshow border radius', 'Slideshow border radius', '0'),
(105, 'slideshow_navigation_type', 'Slideshow navigation type', 'Slideshow navigation type', '1'),
(106, 'slideshow_navigation_position', 'Slideshow navigation position', 'Slideshow navigation position', 'bottom'),
(107, 'slideshow_title_background_color', 'Slideshow title background color', 'Slideshow title background color', 'ffffff'),
(108, 'slideshow_description_background_color', 'Slideshow description background color', 'Slideshow description background color', '000000'),
(109, 'slideshow_title_transparent', 'Slideshow title has background', 'Slideshow title has background', 'on'),
(110, 'slideshow_description_transparent', 'Slideshow description has background', 'Slideshow description has background', 'on'),
(111, 'slideshow_slideshow_background_color', 'Slideshow slideshow background color', 'Slideshow slideshow background color', 'ffffff'),
(112, 'slideshow_active_dot_color', 'slideshow active dot color', '', 'ffffff'),
(113, 'slideshow_dots_color', 'slideshow dots color', '', '000000'),
(114, 'slideshow_description_width', 'Slideshow description width', 'Slideshow description width', '70'),
(115, 'slideshow_description_height', 'Slideshow description height', 'Slideshow description height', '50'),
(116, 'slideshow_description_background_transparency', 'slideshow description background transparency', 'slideshow description background transparency', '70'),
(117, 'slideshow_description_text_align', 'description text-align', 'description text-align', 'justify'),
(118, 'slideshow_title_width', 'slideshow title width', 'slideshow title width', '30'),
(119, 'slideshow_title_height', 'slideshow title height', 'slideshow title height', '50'),
(120, 'slideshow_title_background_transparency', 'slideshow title background transparency', 'slideshow title background transparency', '70'),
(121, 'slideshow_title_text_align', 'title text-align', 'title text-align', 'right'),
(122, 'slideshow_title_has_margin', 'title has margin', 'title has margin', 'on'),
(123, 'slideshow_description_has_margin', 'description has margin', 'description has margin', 'on'),
(124, 'slideshow_show_arrows', 'Slideshow show left right arrows', 'Slideshow show left right arrows', 'on'),
(125, 'loading_icon_type', 'Slideshow loading icon type', 'Slideshow loading icon type', '1'),
(126, 'slideshow_thumb_count_slides', 'Slide thumbs count', 'Slide thumbs count', '3'),
(127, 'slideshow_dots_position_new', 'Slide Dots Position', 'Slide Dots Position', 'dotstop'),
(128, 'slideshow_thumb_back_color', 'Thumbnail Background Color', 'Thumbnail Background Color', 'FFFFFF'),
(129, 'slideshow_thumb_passive_color', 'Passive Thumbnail Color', 'Passive Thumbnail Color', 'FFFFFF'),
(130, 'slideshow_thumb_passive_color_trans', 'Passive Thumbnail Color Transparency', 'Passive Thumbnail Color Transparency', '50'),
(131, 'slideshow_thumb_height', 'Slideshow Thumb Height', 'Slideshow Thumb Height', '100');

INSERT INTO `#__huge_itslideshow_images` (`name`, `slideshow_id`, `description`, `image_url`, `sl_url`, `sl_type`, `link_target`, `sl_stitle`, `sl_sdesc`, `sl_postlink`, `ordering`, `published`, `published_in_sl_width`) VALUES
( '', '1', '', 'media/com_slideshow/images/slide1.jpg', 'http://huge-it.com', 'image', 'on', '', '', '', 1, 1, NULL),
('Simple Usage', '1', '', 'media/com_slideshow/images/slide2.jpg', 'http://huge-it.com', 'image', 'on', '', '', '', 2, 1, NULL),
('Huge-IT Slideshow', '1', 'The slideshow allows having unlimited amount of images with their titles and descriptions. The slideshow uses autogenerated shortcodes making it easier for the users to add it to the custom location.', 'media/com_slideshow/images/slide3.jpg', 'http://huge-it.com', 'image', 'on', '', '', '', 3, 1, NULL);


INSERT INTO `#__huge_itslideshow_slideshows` (`name`, `sl_height`, `sl_width`, `pause_on_hover`, `slideshow_list_effects_s`, `description`, `param`, `sl_position`, `ordering`, `published`) VALUES
('My First Slideshow', 375, 600, 'on', 'random', '4000', '1000', 'center', 1, '300');


