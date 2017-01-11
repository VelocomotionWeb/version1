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
*  @copyright  best-kit
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

require_once _PS_MODULE_DIR_ . 'bestkit_booking/includer.php';

class BestkitBookingPriceRules extends ObjectModel
{
    public $id;
    public $id_bestkit_booking_price_rule;
    public $id_bestkit_booking_product;
    public $date_from;
    public $date_to;
    public $time_from;
    public $time_to;
    public $day;
    public $recurrent_date;
    public $type;
    public $price;

    public static $definition = array(
        'table' => 'bestkit_booking_price_rule',
        'primary' => 'id_bestkit_booking_price_rule',
        'multilang' => FALSE,
        'fields' => array(
            'id_bestkit_booking_product' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => TRUE),
            'date_from' =>              	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_to' =>                	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'time_from' =>              	array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat'),
            'time_to' =>                	array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat'),
            'day' =>                    	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'recurrent_date' =>         	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'type' =>                		array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => TRUE),
			'price' => 						array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => TRUE),
        ),
    );

    public static function clearByIdProduct($id_product)
    {
        $sql = 'SELECT `' . self::$definition['primary'] . '`
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` be
            JOIN `' . _DB_PREFIX_ . 'bestkit_booking_product` bp ON (be.`id_bestkit_booking_product` = bp.`id_bestkit_booking_product`)
            WHERE id_product = ' . (int)$id_product;
		$_rules = Db::getInstance()->executeS($sql);
		
		foreach ($_rules as $_rule) {
			$tmpRuleObj = new self($_rule['id_bestkit_booking_price_rule']);
			$tmpRuleObj->delete();
		}
    }

    public static function getPriceRulesByIdProduct($id_product)
    {
        $sql = 'SELECT be.*, bp.id_product
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` be
            JOIN `' . _DB_PREFIX_ . 'bestkit_booking_product` bp ON (be.`id_bestkit_booking_product` = bp.`id_bestkit_booking_product`)
            WHERE id_product = ' . (int)$id_product . '
			ORDER BY `id_bestkit_booking_price_rule`';

        return Db::getInstance()->executeS($sql);
    }

    public static function getPriceRulesForBookingId($id_bestkit_booking_product)
    {
        $sql = 'SELECT *
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` be
            WHERE id_bestkit_booking_product = ' . (int)$id_bestkit_booking_product;

        return Db::getInstance()->executeS($sql);
    }
}