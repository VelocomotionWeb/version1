CREATE TABLE IF NOT EXISTS `PREFIX_mp_product_attribute` (
	`mp_id_product_attribute` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`mp_id_product` int(10) unsigned NOT NULL,
	`mp_reference` varchar(32) DEFAULT NULL,
	`mp_supplier_reference` varchar(32) DEFAULT NULL,
	`mp_location` varchar(64) DEFAULT NULL,
	`mp_ean13` varchar(13) DEFAULT NULL,
	`mp_upc` varchar(12) DEFAULT NULL,
	`mp_wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`mp_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`mp_ecotax` decimal(17,6) NOT NULL DEFAULT '0.000000',
	`mp_quantity` int(10) NOT NULL DEFAULT '0',
	`mp_weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`mp_unit_price_impact` decimal(17,2) NOT NULL DEFAULT '0.00',
	`mp_default_on` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`mp_minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
	`mp_available_date` date NOT NULL,
	PRIMARY KEY (`mp_id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_product_attribute_image` (
	`mp_id_product_attribute` int(10) unsigned NOT NULL,
	`mp_id_image` int(10) unsigned NOT NULL,
	PRIMARY KEY (`mp_id_product_attribute`,`mp_id_image`),
	KEY `mp_id_image` (`mp_id_image`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_product_attribute_combination` (
	`id_ps_attribute` int(10) unsigned NOT NULL,
	`mp_id_product_attribute` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_ps_attribute`,`mp_id_product_attribute`),
	KEY `mp_id_product_attribute` (`mp_id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_product_attribute_shop` (
	`mp_id_product_attribute` int(10) unsigned NOT NULL,
	`id_shop` int(10) unsigned NOT NULL,
	`mp_id_shop` int(10) unsigned NOT NULL,
	`mp_wholesale_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`mp_price` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`mp_ecotax` decimal(17,6) NOT NULL DEFAULT '0.000000',
	`mp_weight` decimal(20,6) NOT NULL DEFAULT '0.000000',
	`mp_unit_price_impact` decimal(17,2) NOT NULL DEFAULT '0.00',
	`mp_default_on` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`mp_minimal_quantity` int(10) unsigned NOT NULL DEFAULT '1',
	`mp_available_date` date NOT NULL,
	PRIMARY KEY (`mp_id_product_attribute`,`id_shop`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_stock_available` (
	`mp_id_stock_available` int(11) unsigned NOT NULL AUTO_INCREMENT,
	`mp_id_product` int(11) unsigned NOT NULL,
	`mp_id_product_attribute` int(11) unsigned NOT NULL,
	`id_shop` int(11) unsigned NOT NULL,
	`id_shop_group` int(11) unsigned NOT NULL,
	`quantity` int(10) NOT NULL DEFAULT '0',
	`depends_on_stock` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`out_of_stock` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`mp_id_stock_available`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_combination_map` (
	`id_ps_product_attribute` int(10) unsigned NOT NULL,
	`mp_id_product_attribute` int(10) unsigned NOT NULL,
	`mp_product_id` int(10) unsigned NOT NULL,
	`main_product_id` int(10) unsigned NOT NULL,
	PRIMARY KEY (`id_ps_product_attribute`,`mp_id_product_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_attribute_impact` (
  `mp_id_attribute_impact` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_id_product` int(11) unsigned NOT NULL,
  `id_attribute` int(11) unsigned NOT NULL,
  `mp_weight` decimal(20,6) NOT NULL,
  `mp_price` decimal(17,2) NOT NULL,
  PRIMARY KEY (`mp_id_attribute_impact`),
  UNIQUE KEY `id_product` (`mp_id_product`,`id_attribute`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `PREFIX_mp_ps_image_map` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mp_id_product` int(11) unsigned NOT NULL,
  `mp_id_image` int(11) unsigned NOT NULL,
  `id_ps_product` int(11) unsigned NOT NULL,
  `id_ps_image` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=ENGINE_TYPE DEFAULT CHARSET=utf8;