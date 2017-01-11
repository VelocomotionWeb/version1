<?php
	class Mpattributeimpact extends ObjectModel
	{
		public $mp_id_attribute_impact;
		public $mp_id_product;
		public $id_attribute;
		public $mp_weight;
		public $mp_price;		
		public static $definition = array(
			'table' => 'mp_attribute_impact',
			'primary' => 'mp_id_attribute_impact',
			'fields' => array(
				'mp_id_product' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'id_attribute' => 	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_weight' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
				'mp_price' => 		array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'size' => 20),
			),
		);
		public function entryExist($mp_id_product_check, $id_attribute_check)
		{
			$existance = null;
			$existance = db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'mp_attribute_impact` 
			WHERE `mp_id_product` = '.(int)$mp_id_product_check.' AND `id_attribute` = '.(int)$id_attribute_check);			
			if ($existance == null)
				return true;
			return false;
		}
	}
?>