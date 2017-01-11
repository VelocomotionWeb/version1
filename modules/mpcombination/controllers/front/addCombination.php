<?php
	class mpcombinationaddCombinationModuleFrontController extends ModuleFrontController
	{	
		public function initContent()
		{
			parent::initContent();
			$link = new Link();
			$this->mp_product_id = Tools::getValue('mp_product_id');
			if (!is_array(Tools::getValue('options'))) 
			{
				$extra = array('msg'=>1,'mp_product_id'=>$this->mp_product_id);
				$generate_combination_link = $link->getModuleLink('mpcombination', 'generatecombination', $extra);
				Tools::redirect($generate_combination_link);
			}
			else
			{
				$obj_seller_product = new SellerProductDetail($this->mp_product_id);
				$mp_shop_product = $obj_seller_product->getMarketPlaceShopProductDetailBYmspid($this->mp_product_id);				
				if ($mp_shop_product) 
				{
					$main_product_id = $mp_shop_product['id_product'];
					$mp_id_shop = $mp_shop_product['id_shop'];
					$is_in_main = true;
				}
				else
					$is_in_main = false;
				$tab = array_values(Tools::getValue('options'));
				if (count($tab) && Validate::isLoadedObject($obj_seller_product))
				{
					$this->setAttributesImpacts($obj_seller_product->id, $tab);
					if ($is_in_main)
						$this->setAttributesImpacts1($main_product_id, $tab);
					$this->combinations = array_values($this->createCombinations($tab));
					//$combinations = array_values($this->createCombinations($tab));
					$values = array_values(array_map(array($this, 'addAttribute'), $this->combinations));
					if ($is_in_main)
					{
						$this->combinations1 = array_values($this->createCombinations1($tab));
						//$combinations1 = array_values($this->createCombinations1($tab));
						$values1 = array_values(array_map(array($this, 'addAttribute'), $this->combinations1));
					}					
					$id_shop = $this->context->shop->id;
					$depends_on_stock = Mpstockavailable::dependsOnStock($this->mp_product_id, $id_shop);					
					if ($is_in_main)
						$depends_on_stock = StockAvailable::dependsOnStock($main_product_id, $id_shop);
					// @since 1.5.0
					if ($depends_on_stock == 0)
					{
						$attributes = Mpproductattribute::getProductAttributesIds($this->mp_product_id, true);						
						foreach ($attributes as $attribute) 
						{		
							Mpstockavailable::deleteFromProductAttributeShop($attribute['mp_id_product_attribute']);
							Mpstockavailable::removeProductFromStockAvailable($this->mp_product_id, $attribute['mp_id_product_attribute'], Context::getContext()->shop);
						}						
						if ($is_in_main) 
						{
							$attributes = Product::getProductAttributesIds($main_product_id, true);
							foreach ($attributes as $attribute)
								StockAvailable::removeProductFromStockAvailable($main_product_id, $attribute['id_product_attribute'], Context::getContext()->shop);
						}
					}
					Mpstockavailable::deleteProductAttributes($this->mp_product_id);
					if ($is_in_main) 
					{
						$obj_product = new Product($main_product_id);
						$obj_product->deleteProductAttributes();
					}
					if ($is_in_main) 
						Mpstockavailable::generateMultipleCombinations($values, $this->combinations,$this->mp_product_id, $mp_id_shop, $this->context->shop->id, $values1, $this->combinations1);
					else 
						Mpstockavailable::generateMultipleCombinations($values, $this->combinations,$this->mp_product_id, $mp_id_shop, $this->context->shop->id, false, false);			
					//@since 1.5.0
					 if ($depends_on_stock == 0)
					 {
						$attributes = Mpproductattribute::getProductAttributesIds($this->mp_product_id, true);
						$quantity = (int)Tools::getValue('quantity');						
						foreach ($attributes as $attribute)
							Mpstockavailable::setQuantity($this->mp_product_id, $attribute['mp_id_product_attribute'], $quantity);
					 }
					if ($is_in_main)
					{
						if ($depends_on_stock == 0)
						{
							$attributes = Product::getProductAttributesIds($main_product_id, true);
							$quantity = (int)Tools::getValue('quantity');
							foreach ($attributes as $attribute)
								StockAvailable::setQuantity($main_product_id, $attribute['id_product_attribute'], $quantity);
						}
						else
							StockAvailable::synchronize($main_product_id);
					}
					$mp_prd_qty = Mpproductattribute::getMpProductQty($this->mp_product_id);
					Mpproductattribute::updateMpProductQty($mp_prd_qty, $this->mp_product_id);
					
					$param = array('flag'=>1,'id'=>$this->mp_product_id,'editproduct'=>1);
					$prod_update_link = $link->getModuleLink('marketplace', 'productupdate', $param);
					Tools::redirect($prod_update_link);
				}
				else 
				{
					$extra = array('msg'=>2,'mp_product_id'=>$this->mp_product_id);
					$generate_combination_link = $link->getModuleLink('mpcombination', 'generatecombination', $extra);
					Tools::redirect($generate_combination_link);
				}
			}
		}		
		public function setAttributesImpacts($id_product, $tab)
		{
			$attributes = array();
			foreach ($tab as $group)
				foreach ($group as $attribute)
					{
						$price = preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('price_impact_'.(int)$attribute)));
						
						$weight = preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('weight_impact_'.(int)$attribute)));
						
						$attributes[] = '('.(int)$id_product.', '.(int)$attribute.', '.(float)$price.', '.(float)$weight.')';
					}
			return Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'mp_attribute_impact` (`mp_id_product`, `id_attribute`, `mp_price`, `mp_weight`)
					VALUES '.implode(',', $attributes).'
					ON DUPLICATE KEY UPDATE `mp_price` = VALUES(mp_price), `mp_weight` = VALUES(mp_weight)');
		}		
		public function setAttributesImpacts1($id_product, $tab)
		{
			$attributes = array();
			foreach ($tab as $group)
				foreach ($group as $attribute)
					{
						$price = preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('price_impact_'.(int)$attribute)));
						$weight = preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('weight_impact_'.(int)$attribute)));
						$attributes[] = '('.(int)$id_product.', '.(int)$attribute.', '.(float)$price.', '.(float)$weight.')';
					}

			return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
			VALUES '.implode(',', $attributes).'
			ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');
		}	
		public function createCombinations($list)
		{
			if (count($list) <= 1)
				return count($list) ? array_map(create_function('$v', 'return (array($v));'), $list[0]) : $list;
			$res = array();
			$first = array_pop($list);
			foreach ($first as $attribute)
			{
				$tab = $this->createCombinations($list);
				foreach ($tab as $to_add)
					$res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
			}			
			return $res;			
		}		
		public function addAttribute($attributes, $price = 0, $weight = 0)
		{
			foreach ($attributes as $attribute)
			{
				$price += (float)preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('price_impact_'.(int)$attribute)));
				$weight += (float)preg_replace('/[^0-9.]/', '', str_replace(',', '.', Tools::getValue('weight_impact_'.(int)$attribute)));
			}
			if ($this->mp_product_id)
			{
				return array(
					'mp_id_product' => (int)$this->mp_product_id,
					'mp_price' => (float)$price,
					'mp_weight' => (float)$weight,
					'mp_ecotax' => 0,
					'mp_quantity' => (int)Tools::getValue('quantity'),
					'mp_reference' => pSQL(Tools::getValue('reference')),
					'mp_default_on' => 0,
					'mp_available_date' => '0000-00-00'
				);
			}
			return array();			
		}		
		public function createCombinations1($list)
		{			
			if (count($list) <= 1)
				return count($list) ? array_map(create_function('$v', 'return (array($v));'), $list[0]) : $list;
			$res = array();
			$first = array_pop($list);
			foreach ($first as $attribute)
			{
				$tab = $this->createCombinations1($list);
				foreach ($tab as $to_add)
					$res[] = is_array($to_add) ? array_merge($to_add, array($attribute)) : array($to_add, $attribute);
			}						
			return $res;			
		}
	}
?>