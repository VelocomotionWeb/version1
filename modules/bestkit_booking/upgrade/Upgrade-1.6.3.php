<?php

/*
* File: /upgrade/Upgrade-1.6.3.php
*/
function upgrade_module_1_6_3() {
	return Db::getInstance()->execute('
		ALTER TABLE `'._DB_PREFIX_.'bestkit_booking_product`
			ADD COLUMN `show_map` TINYINT( 1 ) UNSIGNED NOT NULL,
			ADD COLUMN `address1` varchar(128) NOT NULL,
			ADD COLUMN `latitude` decimal(13,8) DEFAULT NULL,
			ADD COLUMN `longitude` decimal(13,8) DEFAULT NULL,
			ADD COLUMN `zoom` INT( 11 ) UNSIGNED NOT NULL
    ');
}