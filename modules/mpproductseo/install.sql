CREATE TABLE IF NOT EXISTS `PREFIX_mp_product_seo` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`mp_product_id` int(10) unsigned NOT NULL,
	`meta_title` varchar(255) character set utf8 NOT NULL,
	`meta_description` varchar(255) character set utf8 NOT NULL,
	`friendly_url` varchar(255) character set utf8 NOT NULL,
	`date_add` datetime NOT NULL,
	`date_upd` datetime NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;