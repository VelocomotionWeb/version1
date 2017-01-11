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

class BestkitBookingProduct extends ObjectModel
{
    public $id;
    public $id_bestkit_booking_product;
    public $id_product;
    public $quantity;
    public $date_from;
    public $date_to;
    public $range_type;
    public $time_from;
    public $time_to;
    public $qratio_multiplier;
    public $excluded_days;
    public $available_period;
    public $billable_interval;
	
    public $show_map;
    public $address1;
    public $latitude;
    public $longitude;
    public $zoom;
	
    public $active;
    public $date_add;

    public static $definition = array(
        'table' => 'bestkit_booking_product',
        'primary' => 'id_bestkit_booking_product',
        'multilang' => FALSE,
        'fields' => array(
            'id_product' =>             array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => TRUE),
            'quantity' =>               array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', /*'required' => TRUE*/),
            'date_from' =>              array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'date_to' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'range_type' =>             array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('date_fromto', 'time_fromto', 'datetime_fromto'), 'default' => 'date_fromto', 'required' => TRUE),
            'time_from' =>              array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat'),
            'time_to' =>                array('type' => self::TYPE_STRING, 'validate' => 'isPhpDateFormat'),
            'qratio_multiplier' =>      array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('days', 'hours', 'minutes'), 'default' => 'days', 'required' => TRUE),
            'excluded_days' =>          array('type' => self::TYPE_NOTHING, 'validate' => 'isAnything'),
            'available_period' =>       array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => TRUE),
            'billable_interval' =>      array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => TRUE),
            'show_map' =>               array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'address1' =>               array('type' => self::TYPE_STRING, 'validate' => 'isAddress', 'size' => 128),
            'latitude' =>               array('type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13),
            'longitude' =>              array('type' => self::TYPE_FLOAT, 'validate' => 'isCoordinate', 'size' => 13),
            'zoom' =>              		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'active' =>                 array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 				array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
        ),
    );

	public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
		parent::__construct($id, $id_lang, $id_shop);
		
		if ($this->id && $this->date_from != '0000-00-00')
			$this->date_from = $this->date_from . ' 00:00:00';
		if ($this->id && $this->date_to != '0000-00-00')
			$this->date_to = $this->date_to . ' 23:59:59';
	}
	
    public static function loadByIdProduct($id_product)
    {
        $sql = 'SELECT `' . self::$definition['primary'] . '`
            FROM `' . _DB_PREFIX_ . self::$definition['table'] . '`
            WHERE id_product = ' . (int)$id_product;

        return new self(Db::getInstance()->getValue($sql));
    }
	
	public static function getSecondsByBillablePeriod($qratio_multiplier)
	{
		switch ($qratio_multiplier) {
			case 'days':
				return 86400;
			case 'hours':
				return 3600;
			case 'minutes':
				return 60;
		}
	}

	public function getExcludedDays()
	{
		if ($this->id) {
			return unserialize($this->excluded_days);
		}

		return array();
	}
}