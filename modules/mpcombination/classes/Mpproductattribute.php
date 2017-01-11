<?php
	class Mpproductattribute extends ObjectModel
	{
		public $mp_id_product_attribute;
		public $mp_id_product;
		public $mp_reference;
		public $mp_supplier_reference;
		public $mp_location;
		public $mp_ean13;
		public $mp_upc;
		public $mp_wholesale_price;
		public $mp_price;
		public $mp_unit_price_impact;
		public $mp_ecotax;
		public $mp_minimal_quantity = 1;
		public $mp_quantity;
		public $mp_weight;
		public $mp_default_on;
		public $mp_available_date = '0000-00-00';
		public static $definition = array(
			'table' => 'mp_product_attribute',
			'primary' => 'mp_id_product_attribute',
			'fields' => array(
				'mp_id_product' => 		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
				'mp_location' => 			array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 64),
				'mp_ean13' => 				array('type' => self::TYPE_STRING, 'validate' => 'isEan13', 'size' => 13),
				'mp_upc' => 				array('type' => self::TYPE_STRING, 'validate' => 'isUpc', 'size' => 12),
				'mp_quantity' => 			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 10),
				'mp_reference' => 			array('type' => self::TYPE_STRING, 'size' => 32),
				'mp_supplier_reference' => array('type' => self::TYPE_STRING, 'size' => 32),

				/* Shop fields */
				'mp_wholesale_price' =>	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 27),
				'mp_price' => 				array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'size' => 20),
				'mp_ecotax' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 20),
				'mp_weight' => 			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat'),
				'mp_unit_price_impact' => 	array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'size' => 20),
				'mp_minimal_quantity' => 	array('type' => self::TYPE_INT,  'validate' => 'isUnsignedInt', 'required' => true),
				'mp_default_on' => 		array('type' => self::TYPE_INT,'validate' => 'isBool'),
				'mp_available_date' => 	array('type' => self::TYPE_DATE,  'validate' => 'isDateFormat'),
			),
		);

		public function delete()
		{
			if (!parent::delete())
				return false;
			
			Mpstockavailable::removeProductFromStockAvailable((int)$this->mp_id_product, (int)$this->id);
			
			if (!$this->deleteAssociations())
				return false;
			return true;
		}
		
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
		public function deleteAssociations()
		{
			$result = Db::getInstance()->delete('mp_product_attribute_combination', '`mp_id_product_attribute` = '.(int)$this->id);
			$result &= Db::getInstance()->delete('mp_product_attribute_image', '`mp_id_product_attribute` = '.(int)$this->id);

			return $result;
		}

		public static function getProductAttributesIds($mp_id_product, $shop_only = false)
		{
			return Db::getInstance()->executeS('
			SELECT pa.mp_id_product_attribute
			FROM `'._DB_PREFIX_.'mp_product_attribute` pa'.
			($shop_only ? Shop::addSqlAssociation('mp_product_attribute', 'pa') : '').'
			WHERE pa.`mp_id_product` = '.(int)$mp_id_product);
		}

		public static function getPsProductDefaultAttributesIds($id_product)
		{
			return Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'product_attribute` 
			WHERE `default_on` = 1 AND `id_product` = '.(int)$id_product);
		}

		public static function getMpProductQty($mp_id_product)
		{		
			$qty = Db::getInstance()->getValue('select SUM(`mp_quantity`) from `'._DB_PREFIX_.'mp_product_attribute` where `mp_id_product`='.$mp_id_product);
			if (isset($qty))
				return $qty;
			else
				return false;
		}
		public static function updateMpProductQty($mp_prd_qty, $mp_id_product)
		{
			Db::getInstance()->execute('update `'._DB_PREFIX_.'marketplace_seller_product` set quantity='.$mp_prd_qty.' where id='.$mp_id_product);
		}
		public static function getMpProductIdByAttrId($mp_id_product_attribute)
		{
			$mp_id = Db::getInstance()->getValue('select `mp_id_product` from `'._DB_PREFIX_.'mp_product_attribute` where `mp_id_product_attribute`='.$mp_id_product_attribute);
			if ($mp_id)
		    	return $mp_id;
		    else
		    	return false;
		}	
		public function productAttributeExists($attributes_list, $current_product_attribute = false, Context $context = null, $all_shops = false, $return_id = false, $mp_id_product)
		{
			if (!Combination::isFeatureActive())
				return false;
			if ($context === null)
				$context = Context::getContext();
			$result = Db::getInstance()->executeS(
				'SELECT pac.`id_ps_attribute`, pac.`mp_id_product_attribute`
				FROM `'._DB_PREFIX_.'mp_product_attribute` pa
				JOIN `'._DB_PREFIX_.'mp_product_attribute_shop` pas ON (pas.mp_id_product_attribute = pa.mp_id_product_attribute)
				LEFT JOIN `'._DB_PREFIX_.'mp_product_attribute_combination` pac ON (pac.`mp_id_product_attribute` = pa.`mp_id_product_attribute`)
				WHERE 1 '.(!$all_shops ? ' AND pas.id_shop ='.(int)$context->shop->id : '').' AND pa.`mp_id_product` = '.(int)$mp_id_product.
				($all_shops ? ' GROUP BY pac.id_ps_attribute, pac.mp_id_product_attribute ' : '')
			);
			/* If something's wrong */
			if (!$result || empty($result))
				return false;
			/* Product attributes simulation */
			$product_attributes = array();
			foreach ($result as $product_attribute)
				$product_attributes[$product_attribute['id_product_attribute']][] = $product_attribute['id_attribute'];
			/* Checking product's attribute existence */
			foreach ($product_attributes as $key => $product_attribute)
				if (count($product_attribute) == count($attributes_list))
				{
					$diff = false;
					for ($i = 0; $diff == false && isset($product_attribute[$i]); $i++)
						if (!in_array($product_attribute[$i], $attributes_list) || $key == $current_product_attribute)
							$diff = true;
					if (!$diff)
					{
						if ($return_id)
							return $key;
						return true;
					}
				}
			return false;
		}		
		public function getAttributesResume($id_shop = 1, $mp_id_product, $id_lang, $attribute_value_separator = ' - ', $attribute_separator = ', ')
		{
			if (!Combination::isFeatureActive())
				return array();
			$combinations = Db::getInstance()->executeS('SELECT pa.*, product_attribute_shop.*
					FROM `'._DB_PREFIX_.'mp_product_attribute` pa
					INNER JOIN `'._DB_PREFIX_.'mp_product_attribute_shop` product_attribute_shop ON (product_attribute_shop.mp_id_product_attribute = pa.mp_id_product_attribute AND product_attribute_shop.id_shop='.$id_shop.')
					WHERE pa.`mp_id_product` = '.(int)$mp_id_product.'
					GROUP BY pa.`mp_id_product_attribute`');
			// var_dump($combinations);
			// die();
			
			if (!$combinations)
				return false;

			$product_attributes = array();
			foreach ($combinations as $combination)
				$product_attributes[] = (int)$combination['mp_id_product_attribute'];
			
			$lang = Db::getInstance()->executeS('SELECT pac.mp_id_product_attribute, GROUP_CONCAT(agl.`name`, \''.pSQL($attribute_value_separator).'\',al.`name` ORDER BY agl.`id_attribute_group` SEPARATOR \''.pSQL($attribute_separator).'\') as attribute_designation
					FROM `'._DB_PREFIX_.'mp_product_attribute_combination` pac 
					LEFT JOIN `'._DB_PREFIX_.'attribute` a ON a.`id_attribute` = pac.`id_ps_attribute`
					LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON ag.`id_attribute_group` = a.`id_attribute_group`
					LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = '.(int)$id_lang.')
					LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND agl.`id_lang` = '.(int)$id_lang.')
					WHERE pac.mp_id_product_attribute IN ('.implode(',', $product_attributes).')
					GROUP BY pac.mp_id_product_attribute');
			
			foreach ($lang as $k => $row)
				$combinations[$k]['attribute_designation'] = $row['attribute_designation'];
				
			
			//Get quantity of each variations
			foreach ($combinations as $key => $row)
			{
				$cache_key = $row['mp_id_product'].'_'.$row['mp_id_product_attribute'].'_quantity';
				if (!Cache::isStored($cache_key))
					Cache::store(
						$cache_key,
						Mpstockavailable::getQuantityAvailableByProduct($row['mp_id_product'], $row['mp_id_product_attribute'])
					);
				$combinations[$key]['quantity'] = Cache::retrieve($cache_key);
			}
			return $combinations;
		}
		
		public function changeDefaultOn($mp_id_product)
		{
			return Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'mp_product_attribute SET mp_default_on = 0 WHERE mp_id_product = '.(int)$mp_id_product);
		}
		public function groupTable($id_lang, $ps_id_shop, $mp_id_product)
		{			
			$combinations_groups = $this->getAttributesGroups($id_lang, $ps_id_shop, $mp_id_product);
			
			return $combinations_groups;
		}
		public function getAttributesGroups($id_lang, $ps_id_shop, $mp_id_product)
		{
			$sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name, a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, mp_product_attribute_shop.`mp_id_product_attribute`, IFNULL(stock.quantity, 0) as quantity, mp_product_attribute_shop.`mp_price`, mp_product_attribute_shop.`mp_ecotax`, mp_product_attribute_shop.`mp_weight`, mp_product_attribute_shop.`mp_default_on`, pa.`mp_reference` as reference, mp_product_attribute_shop.`mp_minimal_quantity` as minimal_quantity, mp_product_attribute_shop.`mp_minimal_quantity` as minimal_quantity, mp_product_attribute_shop.`mp_available_date` as available_date, ag.`group_type` FROM `'._DB_PREFIX_.'mp_product_attribute` pa INNER JOIN `'._DB_PREFIX_.'mp_product_attribute_shop` mp_product_attribute_shop ON (mp_product_attribute_shop.mp_id_product_attribute = pa.mp_id_product_attribute AND mp_product_attribute_shop.id_shop ='.$ps_id_shop.') LEFT JOIN `'._DB_PREFIX_.'mp_stock_available` stock ON (stock.mp_id_product = pa.mp_id_product AND stock.mp_id_product_attribute = IFNULL(`pa`.mp_id_product_attribute, 0) AND stock.id_shop ='.$ps_id_shop.') LEFT JOIN `'._DB_PREFIX_.'mp_product_attribute_combination` pac ON (pac.`mp_id_product_attribute` = pa.`mp_id_product_attribute`) LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_ps_attribute`) LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`) LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`) LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`) INNER JOIN `'._DB_PREFIX_.'attribute_shop` attribute_shop ON (attribute_shop.id_attribute = a.id_attribute AND attribute_shop.id_shop ='.$ps_id_shop.') WHERE pa.`mp_id_product` ='.$mp_id_product.' AND al.`id_lang` ='.$id_lang.' AND agl.`id_lang`='.$id_lang.' GROUP BY id_attribute_group, mp_id_product_attribute ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';
			
			return Db::getInstance()->executeS($sql);
		}
		public function getAttributeIdForPsProduct($mp_product_id)
		{
			return Db::getInstance()->executeS('Select mp_id_product_attribute from `'._DB_PREFIX_.'mp_product_attribute` where mp_id_product = '.$mp_product_id);
		}
		public function getPsAttributeIdForMpProduct($mp_attribute_id)
		{
			return Db::getInstance()->executeS('Select id_ps_attribute from `'._DB_PREFIX_.'mp_product_attribute_combination` where mp_id_product_attribute = '.$mp_attribute_id);
		}
		public function getAttributeValuesForMpProduct($mp_product_id)
		{
			return Db::getInstance()->executeS('Select * from `'._DB_PREFIX_.'mp_product_attribute` where mp_id_product = '.$mp_product_id);
		}
		public function insertIntoPsProductAttrShop($id_product_attribute, $id_shop, $wholesale_price, $price, $ecotax, $weight, $unit_price_impact, $minimal_quantity, $available_date)
		{
			return Db::getInstance()->insert('product_attribute_shop', array(
											'id_product_attribute' => $id_product_attribute,
											'id_shop' => $id_shop,
											'wholesale_price' => $wholesale_price,
											'price' => $price,
											'ecotax' => $ecotax,
											'weight' => $weight,
											'unit_price_impact' => $unit_price_impact,
											'minimal_quantity' => $minimal_quantity,
											//'default_on' => $default_on,
											'available_date' => $available_date
										));

		}
		public function insertIntoPsProductCombination($id_attribute, $id_product_attribute)
		{
			return Db::getInstance()->insert('product_attribute_combination', array(
											'id_attribute' => $id_attribute,
											'id_product_attribute' => $id_product_attribute
										));
		}
		public function insertIntoPsAttrImpact($id_product, $id_attribute, $weight, $price)
		{
			return Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'attribute_impact` (`id_product`, `id_attribute`, `price`, `weight`)
						VALUES ('.$id_product.','.$id_attribute.','.$price.','.$weight.')
						ON DUPLICATE KEY UPDATE `price` = VALUES(price), `weight` = VALUES(weight)');

		}
		public function getDefaultOnValueForMpProduct($mp_id_product_attribute)
		{
			return Db::getInstance()->getValue('Select mp_default_on from'._DB_PREFIX_.'mp_product_attribute_shop WHERE mp_id_product_attribute = '.(int)$mp_id_product_attribute);
		}
		public function setMpImages($ids_image, $mp_id_product_attribute)
		{
			if (Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'mp_product_attribute_image`
				WHERE `mp_id_product_attribute` = '.(int)$mp_id_product_attribute) === false)
			return false;

			if (!empty($ids_image))
			{
				$sql_values = array();
				foreach ($ids_image as $value)
					$sql_values[] = '('.(int)$mp_id_product_attribute.', '.(int)$value.')';
				Db::getInstance()->execute('
					INSERT INTO `'._DB_PREFIX_.'mp_product_attribute_image` (`mp_id_product_attribute`, `mp_id_image`)
					VALUES '.implode(',', $sql_values)
				);
			}
			
			return true;
		}
		public function getAttributeImages($mp_id_product_attribute)
		{
			return Db::getInstance()->executeS('Select mp_id_image from `'._DB_PREFIX_.'mp_product_attribute_image` where mp_id_product_attribute  = '.$mp_id_product_attribute );
		}
		public function getPsAttributeImages($id_product_attribute)
		{
			return Db::getInstance()->executeS('Select id_image from `'._DB_PREFIX_.'product_attribute_image` where id_product_attribute  = '.$id_product_attribute );
		}
		public function getPsAttriIdFromMpAtrriId($mp_id_product_attribute)
		{
			return Db::getInstance()->getValue('Select id_ps_product_attribute from `'._DB_PREFIX_.'mp_combination_map` WHERE mp_id_product_attribute = '.(int)$mp_id_product_attribute);
		}
		public static function ifColorAttributegroup($group_id)
		{
			$obj_attr_group = new AttributeGroup($group_id);
			$flag = $obj_attr_group->is_color_group;
			if ($flag == 1)
				return true;
			return false;
		}

		public function getMpAttributesGroups($id_lang, $ps_id_shop, $mp_id_product)
		{
			$sql = 'SELECT ag.`id_attribute_group`, ag.`is_color_group`, agl.`name` AS group_name, agl.`public_name` AS public_group_name, a.`id_attribute`, al.`name` AS attribute_name, a.`color` AS attribute_color, mp_product_attribute_shop.`mp_id_product_attribute`, IFNULL(stock.quantity, 0) as quantity, mp_product_attribute_shop.`mp_price`, mp_product_attribute_shop.`mp_ecotax`, mp_product_attribute_shop.`mp_weight`, mp_product_attribute_shop.`mp_default_on`, pa.`mp_reference` as reference, mp_product_attribute_shop.`mp_minimal_quantity` as minimal_quantity, mp_product_attribute_shop.`mp_minimal_quantity` as minimal_quantity, mp_product_attribute_shop.`mp_available_date` as available_date, ag.`group_type` FROM `'._DB_PREFIX_.'mp_product_attribute` pa INNER JOIN `'._DB_PREFIX_.'mp_product_attribute_shop` mp_product_attribute_shop ON (mp_product_attribute_shop.mp_id_product_attribute = pa.mp_id_product_attribute AND mp_product_attribute_shop.id_shop ='.$ps_id_shop.') LEFT JOIN `'._DB_PREFIX_.'mp_stock_available` stock ON (stock.mp_id_product = pa.mp_id_product AND stock.mp_id_product_attribute = IFNULL(`pa`.mp_id_product_attribute, 0) AND stock.id_shop ='.$ps_id_shop.') LEFT JOIN `'._DB_PREFIX_.'mp_product_attribute_combination` pac ON (pac.`mp_id_product_attribute` = pa.`mp_id_product_attribute`) LEFT JOIN `'._DB_PREFIX_.'attribute` a ON (a.`id_attribute` = pac.`id_ps_attribute`) LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag ON (ag.`id_attribute_group` = a.`id_attribute_group`) LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`) LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl ON (ag.`id_attribute_group` = agl.`id_attribute_group`) INNER JOIN `'._DB_PREFIX_.'attribute_shop` attribute_shop ON (attribute_shop.id_attribute = a.id_attribute AND attribute_shop.id_shop ='.$ps_id_shop.') WHERE pa.`mp_id_product` ='.$mp_id_product.' AND al.`id_lang` ='.$id_lang.' AND agl.`id_lang`='.$id_lang.' GROUP BY id_attribute_group, mp_id_product_attribute ORDER BY ag.`position` ASC, a.`position` ASC, agl.`name` ASC';
			
			return Db::getInstance()->executeS($sql);
		}

		public function getAttributesImpacts($mp_id_product)
		{
			$tab = array();
			$result = Db::getInstance()->executeS(
				'SELECT ai.`id_attribute`, ai.`mp_price`, ai.`mp_weight`
				FROM `'._DB_PREFIX_.'mp_attribute_impact` ai
				WHERE ai.`mp_id_product` = '.(int)$mp_id_product);

			if (!$result)
				return array();
			foreach ($result as $impact)
			{
				$tab[$impact['id_attribute']]['price'] = (float)$impact['mp_price'];
				$tab[$impact['id_attribute']]['weight'] = (float)$impact['mp_weight'];
			}
			return $tab;
		}

	}
?>