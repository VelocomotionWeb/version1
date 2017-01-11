<?php
	class Mpstockavailable extends ObjectModel
	{
		public $mp_id_stock_available;
		public $mp_id_product;
		public $mp_id_product_attribute;
		public $id_shop;
		public $id_shop_group;
		public $quantity;
		public $depends_on_stock;
		public $out_of_stock;		
		public static $definition = array(
			'table' => 'mp_stock_available',
			'primary' => 'mp_id_stock_available',
			'fields' => array(
				'mp_id_product' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_id_product_attribute' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'id_shop' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'id_shop_group' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'quantity' => 	array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId', 'required' => true),
				'depends_on_stock' => 	array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId'),
				'out_of_stock' => 	array('type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isUnsignedId', 'required' => true)
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
		public static function dependsOnStock($mp_id_product, $id_shop = null)
		{
			if (!Validate::isUnsignedId($mp_id_product))
				return false;

			$query = new DbQuery();
			$query->select('depends_on_stock');
			$query->from('mp_stock_available');
			$query->where('mp_id_product = '.(int)$mp_id_product);
			$query->where('mp_id_product_attribute = 0');

			$query = StockAvailable::addSqlShopRestriction($query, $id_shop);
			return (bool)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		}
		public static function removeProductFromStockAvailable($mp_id_product, $mp_id_product_attribute = null, $shop = null)
		{
			if (!Validate::isUnsignedId($mp_id_product))
				return false;
			if (Shop::getContext() == SHOP::CONTEXT_SHOP)
				if (Shop::getContextShopGroup()->share_stock == 1)
				{
					$pa_sql = '';
					if ($mp_id_product_attribute !== null)
					{
						$pa_sql = '_attribute';
						$id_product_attribute_sql = $mp_id_product_attribute;
					}
					else
						$id_product_attribute_sql = $mp_id_product;
						
					if ((int)Db::getInstance()->getValue('SELECT COUNT(*)
							FROM '._DB_PREFIX_.'mp_product'.$pa_sql.'_shop
							WHERE mp_id_product'.$pa_sql.'='.(int)$id_product_attribute_sql.' 
								AND id_shop IN ('.implode(',', array_map('intval', Shop::getContextListShopID(SHOP::SHARE_STOCK))).')'))
							return true;
				}

			$res = Db::getInstance()->execute('
			DELETE FROM '._DB_PREFIX_.'mp_stock_available
			WHERE mp_id_product = '.(int)$mp_id_product.
			($mp_id_product_attribute ? ' AND mp_id_product_attribute = '.(int)$mp_id_product_attribute : '').
			StockAvailable::addSqlShopRestriction(null, $shop));
			if ($mp_id_product_attribute)
			{
				if ($shop === null || !Validate::isLoadedObject($shop))
				{
					$shop_datas = array();
					StockAvailable::addSqlShopParams($shop_datas);
					$id_shop = (int)$shop_datas['id_shop'];
				}
				else
					$id_shop = (int)$shop->id;

				$stock_available = new Mpstockavailable();
				$stock_available->mp_id_product = (int)$mp_id_product;
				$stock_available->mp_id_product_attribute = (int)$mp_id_product_attribute;
				$stock_available->id_shop = (int)$id_shop;
				$stock_available->postSave();
			}
			return $res;
		}
		public function postSave()
		{
			if ($this->mp_id_product_attribute == 0)
				return true;
			$id_shop = (Shop::getContext() != Shop::CONTEXT_GROUP ? $this->id_shop : null);
			
			(int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT SUM(quantity) as quantity
				FROM '._DB_PREFIX_.'mp_stock_available
				WHERE mp_id_product = '.(int)$this->mp_id_product.'
				AND mp_id_product_attribute <> 0 '.
				StockAvailable::addSqlShopRestriction(null, $id_shop)
			);
			return true;
		}		
		public static function setQuantity($mp_id_product, $mp_id_product_attribute, $quantity, $id_shop = null)
		{
			if (!Validate::isUnsignedId($mp_id_product))
				return false;
			$context = Context::getContext();
			// if there is no $id_shop, gets the context one
			if ($id_shop === null && Shop::getContext() != Shop::CONTEXT_GROUP)
				$id_shop = (int)$context->shop->id;

			$depends_on_stock = Mpstockavailable::dependsOnStock($mp_id_product);

			//Try to set available quantity if product does not depend on physical stock
			if (!$depends_on_stock)
			{
				$mp_id_stock_available = (int)Mpstockavailable::getStockAvailableIdByProductId($mp_id_product, $mp_id_product_attribute, $id_shop);
				if ($mp_id_stock_available)
				{
					$stock_available = new Mpstockavailable($mp_id_stock_available);
					$stock_available->quantity = (int)$quantity;
					$stock_available->update();
				}
				else
				{
					$out_of_stock = Mpstockavailable::outOfStock($mp_id_product, $id_shop);
					$stock_available = new Mpstockavailable();
					$stock_available->out_of_stock = (int)$out_of_stock;
					$stock_available->mp_id_product = (int)$mp_id_product;
					$stock_available->mp_id_product_attribute = (int)$mp_id_product_attribute;
					$stock_available->quantity = (int)$quantity;
					if ($id_shop === null)
						$shop_group = Shop::getContextShopGroup();
					else
						$shop_group = new ShopGroup((int)Shop::getGroupFromShop((int)$id_shop));
					// if quantities are shared between shops of the group
					if ($shop_group->share_stock)
					{
						$stock_available->id_shop = 0;
						$stock_available->id_shop_group = (int)$shop_group->id;
					}
					else
					{
						$stock_available->id_shop = (int)$id_shop;
						$stock_available->id_shop_group = 0;
					}
					$stock_available->add();
				}
			}
		}
		public static function getStockAvailableIdByProductId($mp_id_product, $mp_id_product_attribute = null, $id_shop = null)
		{
			if (!Validate::isUnsignedId($mp_id_product))
				return false;

			$query = new DbQuery();
			$query->select('mp_id_stock_available');
			$query->from('mp_stock_available');
			$query->where('mp_id_product = '.(int)$mp_id_product);

			if ($mp_id_product_attribute !== null)
				$query->where('mp_id_product_attribute = '.(int)$mp_id_product_attribute);

			$query = StockAvailable::addSqlShopRestriction($query, $id_shop);
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		}
		public static function deleteProductAttributes($mp_id_product)
		{
			// Hook::exec('actionProductAttributeDelete', array('id_product_attribute' => 0, 'id_product' => $this->id, 'deleteAllAttributes' => true));
			$result = true;
			$combinations = new Collection('Mpproductattribute');
			$combinations->where('mp_id_product', '=', $mp_id_product);
			foreach ($combinations as $combination)
				$result &= $combination->delete();
			//SpecificPriceRule::applyAllRules(array((int)$this->id));
			return $result;
		}
		public static function generateMultipleCombinations($combinations, $attributes, $mp_product_id, $mp_id_shop = 1, $id_shop = 1, $combinations1 = false, $attributes1 = false)
		{
			Mpcombinationmap::deleteFromMap($mp_product_id);
			$obj_seller_product = new SellerProductDetail($mp_product_id);
			$mp_shop_product = $obj_seller_product->getMarketPlaceShopProductDetailBYmspid($mp_product_id);
			$mp_product_attribute_id_array = array();
			if ($mp_shop_product)
			{
				$main_product_id = $mp_shop_product['id_product'];
				$mp_id_shop = $mp_shop_product['id_shop'];
				$obj_product = new Product($main_product_id);
				$is_in_main = true;
			}
			else
				$is_in_main = false;
			$res = true;
			$default_on = 1;
			
			foreach ($combinations as $key => $combination)
			{
				$obj_mp_product_attr = new Mpproductattribute();
				$id_combination = (int)$obj_mp_product_attr->productAttributeExists($attributes[$key], false, null, true, true, $mp_product_id);
				$obj = new Mpproductattribute($id_combination);
				if ($id_combination)
				{
					$obj->mp_minimal_quantity = 1;
					$obj->mp_available_date = '0000-00-00';
				}
				foreach ($combination as $field => $value)
					$obj->$field = $value;
				$obj->mp_default_on = $default_on;
				$obj->save();
				$mp_product_attribute_id_array[] = $obj->id;
				Db::getInstance()->insert('mp_product_attribute_shop', array(
											'mp_id_product_attribute' => (int)$obj->id,
											'id_shop' => $id_shop,
											'mp_id_shop' => $mp_id_shop,
											'mp_wholesale_price' => $obj->mp_wholesale_price,
											'mp_price' => $obj->mp_price,
											'mp_ecotax' => $obj->mp_ecotax,
											'mp_weight' => $obj->mp_weight,
											'mp_unit_price_impact' => $obj->mp_unit_price_impact,
											'mp_default_on' => $obj->mp_default_on,
											'mp_minimal_quantity' => $obj->mp_minimal_quantity,
											'mp_available_date' => $obj->mp_available_date,
										));
				if (!$id_combination)
				{
					$attribute_list = array();
					foreach ($attributes[$key] as $id_attribute)
						$attribute_list[] = array(
							'mp_id_product_attribute' => (int)$obj->id,
							'id_ps_attribute' => (int)$id_attribute
						);
					$res &= Db::getInstance()->insert('mp_product_attribute_combination', $attribute_list);
				}				
				$default_on = 0;
			}
			$i = 0;
			$default_on = 1;
			if ($is_in_main)
			{
				unset($combinations);
				unset($attributes);
				$combinations = $combinations1;
				$attributes = $attributes1;

				// var_dump($combinations1);
				// var_dump($attributes);
				foreach ($combinations as $key => $combination)
				{
					$id_combination = $obj_product->productAttributeExists($attributes[$key], false, null, true, true);
					$obj = new Combination($id_combination);
					if ($id_combination)
					{
						$obj->minimal_quantity = 1;
						$obj->available_date = '0000-00-00';
					}					
					foreach ($combination as $field => $value)
					{
						$obj->$field = $value;
						if ($field == 'mp_price')
							$obj->price = $value;
						if ($field == 'mp_weight')
							$obj->weight = $value;
						if ($field == 'mp_ecotax')
							$obj->ecotax = $value;
						if ($field == 'mp_quantity')
							$obj->quantity = $value;
						if ($field == 'mp_reference')
							$obj->reference = $value;
					}
					$obj->default_on = $default_on;
					$obj->id_product = $main_product_id;
					$default_on = 0;
					$obj->save();
					if (!$id_combination)
					{
						$attribute_list = array();
						foreach ($attributes[$key] as $id_attribute)
							$attribute_list[] = array(
								'id_product_attribute' => (int)$obj->id,
								'id_attribute' => (int)$id_attribute
							);
						$res &= Db::getInstance()->insert('product_attribute_combination', $attribute_list);
					}					
					Db::getInstance()->insert('mp_combination_map', array(
											'id_ps_product_attribute' => (int)$obj->id,
											'mp_id_product_attribute' => $mp_product_attribute_id_array[$i],
											'mp_product_id' => $mp_product_id,
											'main_product_id' => $main_product_id,
										));
					$i++;
				}
			}
			return $res;
		}
		public static function outOfStock($mp_id_product, $id_shop = null)
		{
			if (!Validate::isUnsignedId($mp_id_product))
				return false;

			$query = new DbQuery();
			$query->select('out_of_stock');
			$query->from('mp_stock_available');
			$query->where('mp_id_product = '.(int)$mp_id_product);
			$query->where('mp_id_product_attribute = 0');

			$query = StockAvailable::addSqlShopRestriction($query, $id_shop);

			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		}
		public static function deleteFromProductAttributeShop($mp_id_product_attribute) 
		{
			Db::getInstance()->delete('mp_product_attribute_shop', '`mp_id_product_attribute` = '.(int)$mp_id_product_attribute);	
		}
		public static function getQuantityAvailableByProduct($mp_id_product = null, $mp_id_product_attribute = null, $id_shop = null)
		{
			// if null, it's a product without attributes
			if ($mp_id_product_attribute === null)
				$mp_id_product_attribute = 0;
			$query = new DbQuery();
			$query->select('SUM(quantity)');
			$query->from('mp_stock_available');
			// if null, it's a product without attributes
			if ($mp_id_product !== null)
				$query->where('mp_id_product = '.(int)$mp_id_product);
			$query->where('mp_id_product_attribute = '.(int)$mp_id_product_attribute);
			$query = StockAvailable::addSqlShopRestriction($query, $id_shop);
			return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($query);
		}
	}
?>