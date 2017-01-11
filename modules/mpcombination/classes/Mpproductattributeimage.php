<?php
	class Mpproductattributeimage extends ObjectModel
	{
		public $mp_id_product_attribute;
		public $mp_id_image;
		public static $definition = array(
			'table' => 'mp_product_attribute_image',
			'primary' => array('mp_id_product_attribute','mp_id_image')
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
	}
?>