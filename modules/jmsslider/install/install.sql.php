<?php
/**
* 2007-2015 PrestaShop
*
* Slider Layer module for prestashop
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2015 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

$query = "CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides` (
  `id_slide` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `class_suffix` varchar(100) NOT NULL,
  `bg_type` int(10) NOT NULL DEFAULT '1',
  `bg_image` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `bg_color` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '#FFF',
  `slide_link` varchar(100) NOT NULL,
  `order` int(10) NOT NULL,
  `status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_slide`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

INSERT INTO `_DB_PREFIX_jms_slides` (`id_slide`, `title`, `class_suffix`, `bg_type`, `bg_image`, `bg_color`, `slide_link`, `order`, `status`) VALUES
(7, 'Slide 1', '', 1, 'afe966c83746e2fa22bcb9a89b536d43.jpg', '#000000', '', 0, 1),
(8, 'Slide 2', '', 1, 'f85eb56df482191f4aabca4d9b3fc699.jpg', '', '', 0, 1),
(9, 'Slide 3', '', 1, '6fd04eea7f0a97974af00ffc956efae0.jpg', '', '', 0, 1),
(10, 'Slide 4', '', 1, '99b9fe3fd9fb899a5aba127e6c0cd8cf.jpg', '', '', 0, 1),
(11, 'Slide 5', '', 1, 'a89e049f93dcfb4c91fa2973cb1d9ca9.jpg', '', '', 0, 1),
(12, 'Slide 6', '', 1, '99d12ce75568c8bbf1185bc8b898562b.jpg', '', '', 0, 1);
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides_homes` (
  `id_slide` int(10) NOT NULL,
  `id_homes` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_slide`,`id_homes`)
) ENGINE=_MYSQL_ENGINE_ DEFAULT CHARSET=utf8;

INSERT INTO `_DB_PREFIX_jms_slides_homes` (`id_slide`, `id_homes`) VALUES
(7, '1'),
(8, '1'),
(9, '1'),
(10, '2'),
(11, '2'),
(12, '2');

CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides_lang` (
  `id_slide` int(10) NOT NULL AUTO_INCREMENT,
  `id_lang` int(10) NOT NULL,
  PRIMARY KEY (`id_slide`,`id_lang`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

INSERT INTO `_DB_PREFIX_jms_slides_lang` (`id_slide`, `id_lang`) VALUES
(7, 0),
(8, 0),
(9, 0),
(10, 0),
(11, 0),
(12, 0);

CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides_layers` (
  `id_layer` int(10) NOT NULL AUTO_INCREMENT,
  `id_slide` int(10) NOT NULL,
  `data_title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_class_suffix` varchar(50) NOT NULL,
  `data_fixed` int(10) NOT NULL DEFAULT '0',
  `data_delay` int(10) NOT NULL DEFAULT '1000',
  `data_time` int(10) NOT NULL DEFAULT '1000',
  `data_x` int(10) NOT NULL DEFAULT '0',
  `data_y` int(10) NOT NULL DEFAULT '0',
  `data_in` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'left',
  `data_out` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'right',
  `data_ease_in` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'linear',
  `data_ease_out` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'linear',
  `data_step` int(10) NOT NULL DEFAULT '0',
  `data_special` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'cycle',
  `data_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `data_image` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `data_html` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `data_video` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `data_video_controls` int(10) NOT NULL DEFAULT '1',
  `data_video_muted` int(10) NOT NULL DEFAULT '0',
  `data_video_autoplay` int(10) NOT NULL DEFAULT '1',
  `data_video_loop` int(10) NOT NULL DEFAULT '1',
  `data_video_bg` int(10) NOT NULL DEFAULT '0',
  `data_font_size` int(10) NOT NULL DEFAULT '14',
  `data_line_height` int(10) NOT NULL,
  `data_style` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'normal',
  `data_color` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '#FFFFFF',
  `data_width` int(10) NOT NULL,
  `data_height` int(10) NOT NULL,
  `data_order` int(10) NOT NULL,
  `data_status` int(10) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id_layer`,`id_slide`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;
INSERT INTO `_DB_PREFIX_jms_slides_layers` (`id_layer`, `id_slide`, `data_title`, `data_class_suffix`, `data_fixed`, `data_delay`, `data_time`, `data_x`, `data_y`, `data_in`, `data_out`, `data_ease_in`, `data_ease_out`, `data_step`, `data_special`, `data_type`, `data_image`, `data_html`, `data_video`, `data_video_controls`, `data_video_muted`, `data_video_autoplay`, `data_video_loop`, `data_video_bg`, `data_font_size`, `data_line_height`, `data_style`, `data_color`, `data_width`, `data_height`, `data_order`, `data_status`) VALUES
(1, 7, 'Fly Around World', 'montserrat_bold italic', 0, 1000, 2000, 479, 353, 'top', 'bottom', 'linear', 'linear', 0, '', 'text', '', 'Fly Around World', '', 0, 0, 0, 0, 0, 110, 0, 'normal', '#ffffff', 1097, 115, 0, 1),
(2, 7, 'Morbi massa arcu', 'raleway_regular', 0, 1500, 2400, 544, 529, 'fade', 'bottom', 'linear', 'linear', 0, '', 'text', '', 'Morbi massa arcu, sollicitudin sit amet purus vestibulum, vestibulum porta dui. Aenean risus purus,<br />\r\nconvallis eget lobortis feugiat risus maecenas', '', 0, 0, 0, 0, 0, 18, 45, 'normal', '#ffffff', 1217, 26, 0, 1),
(3, 7, 'ONLY FOR 499.00', 'montserrat_30', 0, 2000, 2700, 826, 673, 'bottom', 'bottom', 'linear', 'linear', 0, '', 'text', '', 'ONLY FOR  <span class=\"fa fa-gbp\"></span>  499.00', '', 0, 0, 0, 0, 0, 30, 0, 'normal', '#ffffff', 309, 48, 0, 1),
(4, 8, 'Family Holidays', 'montserrat_bold', 0, 800, 1700, 522, 357, 'top', 'fade', 'linear', 'linear', 0, '', 'text', '', 'Family Holidays', '', 0, 0, 0, 0, 0, 110, 0, 'normal', '#ffffff', 914, 99, 0, 1),
(5, 8, 'Morbi massa arcu', 'raleway_regular', 0, 2000, 3000, 552, 585, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', 'Morbi massa arcu, sollicitudin sit amet purus vestibulu, vestibulum porta dui. Aenean risus purus, <br />\r\nconvallis eget lobortis, feugiat a risus, maecenas eget mauris magna. Morbi ornare massa<br />\r\nullamcorper imperdiet est, eget lacinia libero', '', 0, 0, 0, 0, 0, 18, 0, 'normal', '#ffffff', 828, 56, 0, 1),
(6, 8, 'a-anchor', 'anchor', 0, 1500, 2200, 960, 525, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<i class=\"fa fa-anchor\"></i>', '', 0, 0, 0, 0, 0, 20, 0, 'normal', '#ffffff', 10, 14, 0, 1),
(7, 9, 'Great Selection', 'montserrat_70', 0, 1000, 1700, 686, 361, 'top', 'top', 'linear', 'linear', 0, '', 'text', '', 'Great Selection', '', 0, 0, 0, 0, 0, 70, 0, 'normal', '#ffffff', 606, 70, 0, 1),
(8, 9, 'Times Square Newyork', 'montserrat_bold', 0, 1500, 2200, 318, 440, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', 'Times Square Newyork', '', 0, 0, 0, 0, 0, 110, 0, 'normal', '#ffffff', 1348, 110, 0, 1),
(9, 9, 'We’ll bring to you a great trip and memorable', '', 0, 2200, 2700, 706, 640, 'bottom', 'bottom', 'linear', 'linear', 0, '', 'text', '', 'We’ll bring to you a great trip and memorable', '', 0, 0, 0, 0, 0, 24, 0, 'normal', '#ffffff', 609, 24, 0, 1),
(10, 10, 'Great Holiday Deals', 'montserrat_bold', 0, 500, 2000, 412, 400, 'top', 'fade', 'linear', 'linear', 0, '', 'text', '', 'Great <span>Holiday</span> Deals', '', 0, 0, 0, 0, 0, 110, 110, 'normal', '#ffffff', 809, 116, 0, 1),
(11, 10, 'We’ll bring', 'raleway_medium', 0, 3000, 3700, 511, 610, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', 'We’ll bring to you a great trip and memorable, we also ensure the safety and committed <br />\r\nexciting trip to our customers, and afraid anymore, tell us how you want to go.', '', 0, 0, 0, 0, 0, 22, 45, 'normal', '#ffffff', 962, 59, 0, 1),
(12, 10, 'anchor', 'anchor', 0, 1700, 2500, 960, 542, 'fade', 'fade', 'linear', 'linear', 0, '', 'text', '', '<i class=\"fa fa-anchor\"></i>', '', 0, 0, 0, 0, 0, 14, 0, 'normal', '#ffffff', 10, 14, 0, 1),
(13, 11, 'Discover Adventure', 'montserrat_bold', 0, 1500, 2000, 608, 305, 'top', 'left', 'easeInBounce', 'linear', 0, '', 'text', '', 'Discover Adventure', '', 0, 0, 0, 0, 0, 110, 0, 'normal', '#ffffff', 1190, 118, 0, 1),
(14, 11, 'Morbi massa arcu', 'raleway_regular', 0, 1500, 2500, 742, 486, 'fade', 'left', 'linear', 'linear', 0, '', 'text', '', 'Morbi massa arcu, sollicitudin sit amet purus vestibulum, vestibulum porta dui. Aenean risus purus, <br />\r\nconvallis eget lobortis feugiat risus maecenas', '', 0, 0, 0, 0, 0, 18, 45, 'normal', '#ffffff', 938, 59, 0, 1),
(15, 11, 'ONLY FOR ', 'montserrat_30', 0, 2200, 3000, 1015, 639, 'left', 'left', 'linear', 'linear', 0, '', 'text', '', 'ONLY FOR   <span class=\"fa fa-gbp\"></span>  499.00', '', 0, 0, 0, 0, 0, 30, 0, 'normal', '#ffffff', 400, 30, 0, 1),
(16, 12, 'BECOME AN EXPLORER TO GET STARTED', 'montserrat_30', 0, 1500, 2500, 468, 324, 'right', 'topRight', 'linear', 'linear', 0, '', 'text', '', 'BECOME AN EXPLORER TO GET STARTED', '', 0, 0, 0, 0, 0, 30, 0, 'normal', '#ffffff', 670, 28, 0, 1),
(17, 12, 'Mountain Canada', 'montserrat_bold', 0, 1200, 2000, 311, 365, 'right', 'topRight', 'linear', 'linear', 0, '', 'text', '', 'Mountain Canada', '', 0, 0, 0, 0, 0, 110, 0, 'normal', '#ffffff', 1154, 108, 0, 1),
(18, 12, 'Morbi massa arcu', 'raleway_regular', 0, 2200, 3000, 382, 539, 'fade', 'topRight', 'linear', 'linear', 0, '', 'text', '', 'Morbi massa arcu, sollicitudin sit amet purus vestibulum, vestibulum porta dui. Aenean risus purus, <br />\r\nconvallis eget lobortis feugiat risus maecenas', '', 0, 0, 0, 0, 0, 18, 45, 'normal', '#ffffff', 988, 48, 0, 1);
CREATE TABLE IF NOT EXISTS `_DB_PREFIX_jms_slides_shop` (
  `id_slide` int(10) NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) NOT NULL,
  PRIMARY KEY (`id_slide`,`id_shop`)
) ENGINE=_MYSQL_ENGINE_  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

INSERT INTO `_DB_PREFIX_jms_slides_shop` (`id_slide`, `id_shop`) VALUES
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1);
";
?>