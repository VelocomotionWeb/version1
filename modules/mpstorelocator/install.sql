CREATE TABLE IF NOT EXISTS `PREFIX_store_locator` (

`id` int(10) unsigned NOT NULL auto_increment,
`name` varchar(1000) NOT NULL,
`id_seller` int(11) NOT NULL,
`country_id` int(10) unsigned NOT NULL,
`state_id` int(10) unsigned default NULL,
`city_name` varchar(64) NOT NULL,
`street` text,
`map_address` text,
`map_address_text` text,
`latitude` decimal(13,8) DEFAULT NULL,
`longitude` decimal(13,8) DEFAULT NULL,
`zip_code` varchar(12) default NULL,
`phone` varchar(32) default NULL,
`active` tinyint(1) NOT NULL,
`date_add` datetime NOT NULL,
`date_upd` datetime NOT NULL,
PRIMARY KEY  (`id`)

) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_store_products` (

`id` int(10) unsigned NOT NULL auto_increment,
`id_product` int(10) NOT NULL,
`id_store` int(10) NOT NULL,
PRIMARY KEY  (`id`)

) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;