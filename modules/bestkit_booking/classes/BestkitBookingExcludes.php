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

class BestkitBookingExcludes extends ObjectModel
{
    public $id;
    public $id_bestkit_booking_exclude_day;
    public $id_bestkit_booking_product;
    public $date_1;
    public $date_2;
    public $recurrent_day;
    public $date_add;

    public static $definition = array(
        'table' => 'bestkit_booking_product',
        'primary' => 'id_bestkit_booking_exclude_day',
        'multilang' => FALSE,
        'fields' => array(
            'id_bestkit_booking_product' =>             array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => TRUE),
            'date_1' =>              array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat'),
            'date_2' =>                array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat'),
            'recurrent_day' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
        ),
    );

    public static function loadByIdProduct($id_product)
    {
        $sql = 'SELECT `' . self::$definition['primary'] . '`
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '` be
            JOIN `' . _DB_PREFIX_ . 'bestkit_booking_product` bp ON (be.`id_bestkit_booking_product` = bp.`id_bestkit_booking_product`)
            WHERE id_product = ' . (int)$id_product;

        return new self(Db::getInstance()->getValue($sql));
    }
}