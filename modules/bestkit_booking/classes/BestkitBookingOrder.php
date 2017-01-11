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

require_once _PS_MODULE_DIR_ . 'bestkit_booking/includer.php';

class BestkitBookingOrder extends ObjectModel
{
    public $id;
    public $id_bestkit_booking_order;
    public $id_cart;
    public $from;
    public $to;
    public $range_type;
    public $qratio_multiplier;
    public $billable_interval;

    public static $definition = array(
        'table' => 'bestkit_booking_order',
        'primary' => 'id_bestkit_booking_order',
        'multilang' => FALSE,
        'fields' => array(
            'id_cart' =>                array('type' => self::TYPE_INT, 'required' => TRUE),
            'from' =>              array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => TRUE),
            'to' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => TRUE),
            'range_type' =>             array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('date_fromto', 'time_fromto', 'datetime_fromto')),
            'qratio_multiplier' =>      array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('days', 'hours', 'minutes'), 'default' => 'days', 'required' => TRUE),
            'billable_interval' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => TRUE),
        ),
    );

    public static function getBookingOrdersByProduct($id_product, $valid_only = 1, $date_from = '', $date_to = '')
    {
        return Db::getInstance()->executeS('
            select bo.*, o.id_order, o.valid, od.product_id, od.product_quantity
            from `' . _DB_PREFIX_ . 'bestkit_booking_order` bo
            join `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = bo.id_cart)
            join `' . _DB_PREFIX_ . 'order_detail` od ON (od.id_order = o.id_order)
            where od.product_id = '.(int)$id_product.'
                '.($valid_only ? ' AND o.valid = 1 ' : '').'
				AND (DAY(bo.`from`) BETWEEN DAY("'.pSQL($date_from).'") AND DAY("'.pSQL($date_to).'") or DAY(bo.`to`) BETWEEN DAY("'.pSQL($date_from).'") AND DAY("'.pSQL($date_to).'"))
        '); 
/*
	'.($date_from ? ' AND bo.`from` <= "'.pSQL($date_from).'"' : '').'
	'.($date_to ? ' AND bo.`to` <= "'.pSQL($date_to).'"' : '').'
*/
//AND (DAY(bo.`from`) >= DAY("2016-04-13 10:00:00") OR DAY(bo.`to`) <= DAY("2016-04-13 11:00:00"))
    }

    public static function getBookingOrdersByOrder($id_order, $id_lang = null)
    {
		if (!$id_lang)
			$id_lang = Context::getContext()->language->id;
			
        return Db::getInstance()->executeS('
            select bo.*, o.id_order, o.valid, pl.name
            from `' . _DB_PREFIX_ . 'bestkit_booking_order` bo
			join `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = bo.id_product AND pl.id_lang = '.(int)$id_lang.')
            join `' . _DB_PREFIX_ . 'orders` o ON (o.id_cart = bo.id_cart)
            where o.id_order = '.(int)$id_order.'
			group by bo.id_bestkit_booking_order
        ');
    }
}