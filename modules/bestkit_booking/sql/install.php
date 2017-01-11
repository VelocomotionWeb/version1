<?php
/**
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *         DISCLAIMER   *
 * *************************************** */
/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* ****************************************************
*
*  @author     BEST-KIT
*  @copyright  BEST-KIT
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/


$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_booking_product` (
            `id_bestkit_booking_product` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_product` INT( 11 ) UNSIGNED,
            `quantity` INT( 11 ) UNSIGNED,
            `date_from` DATE NOT NULL,
            `date_to` DATE NOT NULL,
            `range_type` enum(\'date_fromto\',\'time_fromto\',\'datetime_fromto\') NOT NULL DEFAULT \'date_fromto\',
            `time_from` TIME NOT NULL,
            `time_to` TIME NOT NULL,
            `qratio_multiplier` enum(\'days\',\'hours\',\'minutes\') NOT NULL DEFAULT \'days\',
            `date_add` DATETIME NOT NULL,
            `excluded_days` TEXT NOT NULL,
            `available_period` INT( 11 ) UNSIGNED NOT NULL,
            `billable_interval` INT( 11 ) UNSIGNED NOT NULL,
            `active` TINYINT( 1 ) UNSIGNED NOT NULL,
            `show_map` TINYINT( 1 ) UNSIGNED NOT NULL,
			`address1` varchar(128) NOT NULL,
			`latitude` decimal(13,8) DEFAULT NULL,
			`longitude` decimal(13,8) DEFAULT NULL,
			`zoom` INT( 11 ) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_bestkit_booking_product`),
            UNIQUE KEY (`id_product`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_booking_order` (
            `id_bestkit_booking_order` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_cart` INT( 11 ) UNSIGNED,
            `id_product` INT( 11 ) UNSIGNED,
            `id_product_attribute` INT( 11 ) UNSIGNED,
            `from` DATETIME NOT NULL,
            `to` DATETIME NOT NULL,
            `range_type` enum(\'date_fromto\',\'time_fromto\',\'datetime_fromto\') NOT NULL DEFAULT \'date_fromto\',
            `qratio_multiplier` enum(\'days\',\'hours\',\'minutes\') NOT NULL DEFAULT \'days\',
            `billable_interval` INT( 11 ) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_bestkit_booking_order`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_booking_price_rule` (
            `id_bestkit_booking_price_rule` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_bestkit_booking_product` INT( 11 ) UNSIGNED NOT NULL,
            `date_from` DATE NOT NULL,
            `date_to` DATE NOT NULL,
			`time_from` TIME NOT NULL,
            `time_to` TIME NOT NULL,
            `day` TINYINT NOT NULL,
            `recurrent_date` DATE NOT NULL,
            `type` enum(\'from_to_date\',\'from_to_time\',\'from_to_datetime\',\'recurrent_day\',\'recurrent_date\') NOT NULL DEFAULT \'recurrent_date\',
            `price` decimal(20,6) NOT NULL DEFAULT \'0.000000\',
            PRIMARY KEY (`id_bestkit_booking_price_rule`),
            KEY `id_bestkit_booking_product_idx` (`id_bestkit_booking_product`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

/*$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'bestkit_booking_exclude_day` (
            `id_bestkit_booking_exclude_day` INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT,
            `id_bestkit_booking_product` INT( 11 ) UNSIGNED NOT NULL,
            `date_1` DATE NOT NULL,
            `date_2` DATE NOT NULL,
            `recurrent_day` TINYINT(1) UNSIGNED NOT NULL,
            PRIMARY KEY (`id_bestkit_booking_exclude_day`),
            KEY `id_bestkit_booking_exclude_day_product_idx` (`id_bestkit_booking_product`)
        ) ENGINE = ' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';*/
