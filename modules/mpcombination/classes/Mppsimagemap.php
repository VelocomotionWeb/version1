<?php
	class Mppsimagemap extends ObjectModel
	{
		public $id;
		public $mp_id_product;
		public $mp_id_image;
		public $id_ps_product;
		public $id_ps_image;
		/**
		 * @see ObjectModel::$definition
		 */
		public static $definition = array(
			'table' => 'mp_ps_image_map',
			'primary' => 'id',
			'fields' => array(
				'mp_id_product' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'mp_id_image' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_ps_product' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
				'id_ps_image' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			),
		);
		public function add($autodate = true, $null_values = false)
		{
			if (!parent::add($autodate, $null_values))
				return false;
			return true;
		}
		public function update($null_values = false)
		{
			Cache::clean('getContextualValue_'.$this->id.'_*');
			$success = parent::update($null_values);
			return $success;
		}
		public function getPsImageIdFromMpImageId($mp_id_image)
		{
			return Db::getInstance()->getValue('SELECT `id_ps_image` from `'._DB_PREFIX_.'mp_ps_image_map` WHERE `mp_id_image` = '.(int)$mp_id_image);
		}
	}
?>