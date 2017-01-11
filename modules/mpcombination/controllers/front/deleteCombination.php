<?php
	class mpcombinationdeleteCombinationModuleFrontController extends ModuleFrontController
	{
		public function initContent()
		{
			parent::initContent();
			$fun = Tools::getValue('fun');
			if ($fun == 'del_com')
			{
				$mp_id_product_attribute = Tools::getValue('mp_product_attr_id');
				$is_in_map = $this->isInmap($mp_id_product_attribute);
				if ($is_in_map)
				{
					$main_product_id = $is_in_map['main_product_id'];
					$id_product_attribute = $is_in_map['id_ps_product_attribute'];
					$obj_product = new Product($main_product_id);
					$obj_product->deleteAttributeCombination($id_product_attribute);
				}
				$this->deleteMpCombination($mp_id_product_attribute);
			}
			if ($fun == 'upd_default')
			{
				$mp_id_product_attribute = Tools::getValue('mp_product_attr_id');
				$is_in_map = $this->isInmap($mp_id_product_attribute);
				if ($is_in_map)
				{
					$main_product_id = $is_in_map['main_product_id'];
					$id_product_attribute = $is_in_map['id_ps_product_attribute'];
					$obj_product = new Product($main_product_id);
					$obj_product->deleteDefaultAttributes();
					$obj_product->setDefaultAttribute($id_product_attribute);
				}
				$this->setDefaultAttribute($mp_id_product_attribute);
			}
		}		
		public function isInmap($mp_id_product_attribute)
		{
			$is_in_map = Mpcombinationmap::isInMap($mp_id_product_attribute);
			if ($is_in_map)
				return $is_in_map;
			else
				return false;
		}		
		public function deleteMpCombination($mp_id_product_attribute)
		{
			Mpstockavailable::deleteFromProductAttributeShop($mp_id_product_attribute);
			$combination = new Mpproductattribute($mp_id_product_attribute);
			$res = $combination->delete();
			return $res;
		}		
		public function setDefaultAttribute($mp_id_product_attribute)
		{
			$combination = new Mpproductattribute($mp_id_product_attribute);
			$mp_id_product = $combination->mp_id_product;
			$combination->changeDefaultOn($mp_id_product);
			$combination->mp_default_on = 1;
			$combination->save();			
			$attributes = Mpproductattribute::getProductAttributesIds($mp_id_product, true);
			foreach ($attributes as $attribute)
			{
				if ($attribute['mp_id_product_attribute'] == $mp_id_product_attribute)
					Mpproductattributeshop::updateDefaultOn($attribute['mp_id_product_attribute'], 1);
				else
					Mpproductattributeshop::updateDefaultOn($attribute['mp_id_product_attribute']);
			}
		}
	}
?>