<?php
	class Mpcombinationmap extends ObjectModel
	{
		public $id_ps_product_attribute;
		public $mp_id_product_attribute;
		public $mp_product_id;
		public $main_product_id;

		public static $definition = array(
			'table' => 'mp_combination_map',
			'primary' => array('id_ps_product_attribute','mp_id_product_attribute'),
			'fields' => array(
				'id_ps_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'main_product_id' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			),
		);

		public static function insertIntoMpCombinationMap($id_ps_product_attribute, $mp_id_product_attribute, $mp_id_product, $main_product_id)
		{
			Db::getInstance()->insert('mp_combination_map', array(
											'id_ps_product_attribute' => (int)$id_ps_product_attribute,
											'mp_id_product_attribute' => $mp_id_product_attribute,
											'mp_product_id' => $mp_id_product,
											'main_product_id' => $main_product_id,
										));
		}

		public static function isInMap($mp_id_product_attribute)
		{
			$is_in_map = Db::getInstance()->getRow('
			SELECT * FROM `'._DB_PREFIX_.'mp_combination_map`
			WHERE `mp_id_product_attribute` = '.(int)$mp_id_product_attribute
			);
			
			if (empty($is_in_map))
				return false;

			return $is_in_map;
		}

		public static function deleteFromMap($mp_product_id)
		{
			$result = Db::getInstance()->delete('mp_combination_map', '`mp_product_id` = '.(int)$mp_product_id);
			return $result;
		}

	}
?>