<?php
	class Mpproductattributeshop extends ObjectModel
	{
		public $mp_id_product_attribute;
		public $id_shop;
		public $mp_id_shop;
		public $mp_wholesale_price;
		public $mp_price;
		public $mp_ecotax;
		public $mp_weight;
		public $mp_unit_price_impact;
		public $mp_default_on;
		public $mp_minimal_quantity;
		public $mp_available_date;
		public static $definition = array(
			'table' => 'mp_product_attribute_shop',
			'primary' => array('mp_id_product_attribute','id_shop'),
			'fields' => array(
				'mp_id_shop' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_wholesale_price' =>	array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 27),
				'mp_price' =>				array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20),
				'mp_ecotax' =>			array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isPrice', 'size' => 20),
				'mp_weight' =>			array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isFloat'),
				'mp_unit_price_impact' =>	array('type' => self::TYPE_FLOAT, 'shop' => true, 'validate' => 'isNegativePrice', 'size' => 20),
				'mp_minimal_quantity' =>	array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_default_on' =>		array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isBool'),
				'mp_available_date' =>	array('type' => self::TYPE_DATE, 'shop' => true, 'validate' => 'isDateFormat'),
			),
		);
		public function add($autodate = true, $null_values = false)
		{
			if (!parent::add($autodate, $null_values))
				return false;
			return Db::getInstance()->Insert_ID();
		}		
		public function update($null_values = false)
		{
			Cache::clean('getContextualValue_'.$this->id.'_*');
			$success = parent::update($null_values);
			return $success;
		}
		public function delete()
		{
			return parent::delete();
		}
		
		public static function updateDefaultOn($mp_id_product_attribute, $default_on = 0)
		{
			return Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_product_attribute_shop SET mp_default_on='.$default_on.' WHERE mp_id_product_attribute = '.(int)$mp_id_product_attribute);
		}

		public static function updateValue($mp_id_product_attribute, $mp_wholesale_price, $mp_price, $mp_weight, $mp_unit_price_impact, $mp_minimal_quantity, $mp_available_date, $combi_default = false)
		{
			if($combi_default)
				return Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_product_attribute_shop SET mp_wholesale_price="'.$mp_wholesale_price.'" , mp_price="'.$mp_price.'",mp_weight="'.$mp_weight.'",mp_unit_price_impact="'.$mp_unit_price_impact.'",mp_minimal_quantity="'.$mp_minimal_quantity.'",mp_available_date="'.$mp_available_date.'",mp_default_on="'.$combi_default.'" WHERE mp_id_product_attribute = '.(int)$mp_id_product_attribute);
			else
				return Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_product_attribute_shop SET mp_wholesale_price="'.$mp_wholesale_price.'" , mp_price="'.$mp_price.'",mp_weight="'.$mp_weight.'",mp_unit_price_impact="'.$mp_unit_price_impact.'",mp_minimal_quantity="'.$mp_minimal_quantity.'",mp_available_date="'.$mp_available_date.'" WHERE mp_id_product_attribute = '.(int)$mp_id_product_attribute);

		}

		public static function updateProductAttributeShop($id_product_attribute, $wholesale_price, $price,$weight, $unit_price_impact, $minimal_quantity, $available_date)
		{
			return Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET wholesale_price="'.$wholesale_price.'" , price="'.$price.'",weight="'.$weight.'",unit_price_impact="'.$unit_price_impact.'",minimal_quantity="'.$minimal_quantity.'",available_date="'.$available_date.'" WHERE id_product_attribute = '.(int)$id_product_attribute);
		}

		public static function insertProductAttributeShop($mp_id_product_attribute, $mp_id_shop, $mp_wholesale_price, $mp_price, $mp_ecotax, $mp_weight, $mp_unit_price_impact, $mp_minimal_quantity, $mp_available_date, $combi_default = false)
		{
			if($combi_default)
				$mp_default_on = 1;
			else
				$mp_default_on = 0;

			$id_shop = 1;
			//$mp_available_date='0000-00-00';// will be commented
			return Db::getInstance()->insert('mp_product_attribute_shop', array(
											'mp_id_product_attribute' => (int)$mp_id_product_attribute,
											'id_shop' => $id_shop,
											'mp_id_shop' => $mp_id_shop,
											'mp_wholesale_price' => $mp_wholesale_price,
											'mp_price' => $mp_price,
											'mp_ecotax' => $mp_ecotax,
											'mp_weight' => $mp_weight,
											'mp_unit_price_impact' => $mp_unit_price_impact,
											'mp_default_on' => $mp_default_on,
											'mp_minimal_quantity' => $mp_minimal_quantity,
											'mp_available_date' => $mp_available_date,
										));
		}
	}
?>