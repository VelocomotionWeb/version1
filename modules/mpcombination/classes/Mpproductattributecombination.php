<?php
	class Mpproductattributecombination extends ObjectModel
	{
		public $id_ps_attribute;
		public $mp_id_product_attribute;

		public static $definition = array(
			'table' => 'mp_product_attribute_combination',
			'primary' => array('id_ps_attribute','mp_id_product_attribute'),
			'fields' => array(
				'id_ps_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_id_product_attribute' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
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
		public static function getAttributeId($mp_id_product_attribute_check)
		{
			return DB::getInstance()->executeS('SELECT DISTINCT `id_ps_attribute` FROM `'._DB_PREFIX_.'mp_product_attribute_combination` WHERE `mp_id_product_attribute` = '.(int)$mp_id_product_attribute_check);
		}

		public static function isProductCombinationExists($mp_id_product, $product_att_list, $edit_mp_id_product_attribute = false)
		{
			$mp_product_attribute_ids = DB::getInstance()->executeS('SELECT `mp_id_product_attribute` FROM `'._DB_PREFIX_.'mp_product_attribute` WHERE `mp_id_product` = '.(int)$mp_id_product);
			
			if (empty($mp_product_attribute_ids))
					return false;
			else
			{				
				foreach ($mp_product_attribute_ids as $mp_id_product_attr)
				{
					$mp_id_product_attribute = $mp_id_product_attr['mp_id_product_attribute'];

					if($edit_mp_id_product_attribute)
						$ps_attribute_ids = DB::getInstance()->executeS('SELECT `id_ps_attribute` FROM `'._DB_PREFIX_.'mp_product_attribute_combination` WHERE `mp_id_product_attribute` = '.(int)$mp_id_product_attribute.' AND `mp_id_product_attribute` != '.(int)$edit_mp_id_product_attribute);	
					else
						$ps_attribute_ids = DB::getInstance()->executeS('SELECT `id_ps_attribute` FROM `'._DB_PREFIX_.'mp_product_attribute_combination` WHERE `mp_id_product_attribute` = '.(int)$mp_id_product_attribute);

					$ps_product_att_list = array();
					foreach ($ps_attribute_ids as $value)
						$ps_product_att_list[] = $value['id_ps_attribute'];

					sort($ps_product_att_list);
					sort($product_att_list);
					
					if ($ps_product_att_list == $product_att_list)
						return $mp_id_product_attribute;					
				}				
			}
		}

		public static function checkCombinationByGroup($id_lang, $attribute_group)
		{
			$group_attribute = AttributeGroup::getAttributes($id_lang, $attribute_group);			
			$exist_flag = 0;			
			foreach ($group_attribute as $group_attribute_each)
			{
				$att_id = $group_attribute_each['id_attribute'];
				$result = Db::getInstance()->getValue('SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_attribute` ='.$att_id);
					if ($result)
						$exist_flag = 1;
			}
			return $exist_flag;
		}

		public static function checkCombination($attribute_id)
		{
			$result = Db::getInstance()->getValue('SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_attribute` ='.$attribute_id);
			if (!$result)
				return false;
			return true;
		}

		public static function checkCombinationByattribute($attribute_id)
		{
			$result = Db::getInstance()->getValue('SELECT `id_product_attribute` FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_attribute` ='.$attribute_id);
			if (!$result)
				return false;
			return true;
		}

	}
?>