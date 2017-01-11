CREATE TABLE IF NOT EXISTS `PREFIX_wk_booking_attr_group` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_attribute_group` int(10) unsigned NOT NULL,
  `date_add` datetime NOT NULL,
  `date_upd` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

ALTER TABLE `PREFIX_attribute_group` MODIFY COLUMN group_type ENUM('select','radio','color','date');