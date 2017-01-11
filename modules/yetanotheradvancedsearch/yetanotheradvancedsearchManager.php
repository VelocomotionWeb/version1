<?php
/**
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*/

if (!defined('_PS_VERSION_') || !defined('_CAN_LOAD_FILES_'))
	exit;

include_once(dirname(__FILE__).'/yetanotheradvancedsearchConfig.php');

class YetAnotherAdvancedSearchManager {

	/**
	 * Update the menu with new quantities values..
	 * @param type $products
	 * @return type
	 */
	public static function updateMenu($products)
	{
		$menu = array();
		if (YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::DISPLAY_COUNT) == 'visible')
		{
			$criteria = YetAnotherAdvancedSearchModel::getAllCriteria(null);
			$ids = self::getProductIds($products);

			foreach (array_values($criteria) as $criterion)
			{
				$field_values = YetAnotherAdvancedSearchModel::getFieldValues($criterion['id_criteria_field']);
				foreach ($field_values as $field_value)
				{
					if ($field_value['count'] > 0)
					{
						$crit = $criterion['id_criteria_type'].'-'.$field_value['id_criteria_field'].'v'.$field_value['id_internal'];
						$count = self::countCriterionForProducts($criterion, $field_value, $ids);
						$menu[] = array('name' => $crit, 'count' => $count);
					}
				}
			}
		}
		return $menu;
	}

	/**
	 * Request SQL Body
	 *
	 * @param type $default_filter
	 * @param type $criteria
	 * @param type $leftjoin
	 * @return string
	 */
	public static function requestBody($default_filter, $criteria, $leftjoin)
	{
		$sql = ' FROM `'._DB_PREFIX_.'product` p
			'.Shop::addSqlAssociation('product', 'p');

		// used by where clause
		$sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product` '.Shop::addSqlRestrictionOnLang('pl').')
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)';

		// add joins (not necessary for count)
		if ($leftjoin)
		{
			$sql .= ' LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
			LEFT JOIN `'._DB_PREFIX_.'supplier` s ON (s.`id_supplier` = p.`id_supplier`)';
		}

		// add tax if any
		if (YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_VAT) == 'true')
			$sql .= ' LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.id_tax_rules_group = product_shop.id_tax_rules_group AND trg.active = 1)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1)';

		// (i.cover=1 OR i.cover IS NULL)  => to not have ALL the pictures in result
		$sql .= ' WHERE (i.cover=1 OR i.cover IS NULL) AND pl.`id_lang` = '.(int)Context::getContext()->language->id;
		$sql .= ' AND product_shop.`id_shop` = '.(int)Context::getContext()->shop->id;

		// product must be "active"
		// not need to add to "count" requests, as we have a range of ids of active products
		$sql .= ' AND product_shop.active = 1';

		// default filter
		if ($default_filter !== null)
		{
			$elements = explode('v', $default_filter);
			if (count($elements) == 2)
			{
					$key = $elements[0];
					$values = explode('-', $key);
					$id_criteria_type = $values[0];
					$id_criteria_value = $elements[1];
					$chunk = self::generateRequestChunkByType($id_criteria_type, $id_criteria_value);
					$sql .= ' AND '.$chunk;
			}
		}

		// apply criteria
		$grouped_criteria = self::groupCriteria($criteria);
		foreach ($grouped_criteria as $id_criteria_fields => $id_criteria_values)
		{
			// AND between criteriaFields..
			// OR between criteriaValues..
			$values = explode('-', $id_criteria_fields);
			$id_criteria_type = $values[0];
			// $id_criteria_field = $values[1]; //unused..
			$allchunks = '(';
			$first = true;
			$validate_chunk = false;

			foreach ($id_criteria_values as $id_criteria_value)
			{
				// we could do this for all criteria
				// for example, instead of p.id_product in (SELECT `id_product` FROM `ps_category_product` WHERE id_category = xxx)
				// we should join the category and request by the join
				$chunk = self::generateRequestChunkByType($id_criteria_type, $id_criteria_value);

				if (Tools::strlen($chunk) > 0)
				{
					$validate_chunk = true;
					if (!$first)
					{
						$operand = ' OR ';

						// for sliders
						if (strpos($id_criteria_value, 'p') !== false) $operand = ' AND ';
						$allchunks .= $operand;
					}
					else $first = false;
				}
				$allchunks .= $chunk;
			}
			$allchunks .= ')';

			if ($validate_chunk) $sql .= ' AND '.$allchunks;
		}

		return $sql;
	}

	/**
	 * Do simplified request, to get the ids
	 * @param type $criteria
	 * @return type
	 */
	public static function doSimplifiedRequest($df, $criteria = array())
	{
		$sql = 'SELECT DISTINCT(p.`id_product`)';
		$sql .= self::requestBody($df, $criteria, false);
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Do main SQL Request
	 * @param type $criteria
	 * @param type $orderby
	 * @param type $orderway
	 * @param type $p
	 * @param type $n
	 * @return string
	 */
	public static function doRequest($df, $criteria, $orderby, $orderway, $p, $n)
	{
		$sql = 'SELECT DISTINCT(p.`id_product`) as id, p.*, i.*, product_shop.*, pl.* , m.`name` AS manufacturer_name, s.`name` AS supplier_name ';
		$sql .= self::requestBody($df, $criteria, true);

		// ordering
		$order_by_prefix = 'p.';
		if ($orderby == 'name') $order_by_prefix = 'pl.';
		if (Tools::strlen($orderby) > 0 && Tools::strlen($orderway) > 0)
			$sql .= ' ORDER BY '.$order_by_prefix.'`'.$orderby.'` '.$orderway;

		// pagination
		$start = ($p - 1) * $n;
		$sql .= ' LIMIT '.$start.', '.$n;

		// request
		$products = Db::getInstance()->executeS($sql);
		$products = Product::getProductsProperties((int)Context::getContext()->language->id, $products);
		$new_roducts = array();
		foreach ($products as $product)
		{
			if (strpos($product['link'], '?') !== false)
				$product['link'] .= '&yaas=1';
			else
				$product['link'] .= '?yaas=1';
			$new_roducts[] = $product;
		}
		return $new_roducts;
	}

	/**
	 * Get price/weight min/max.
	 *
	 * @param type $products
	 * @return type
	 */
	public static function getPriceAndWeightMinMax($products)
	{
		if (count($products) == 0)
			return array(
				'min_weight' => 0,
				'max_weight' => 0,
				'min_price' => 0,
				'max_price' => 0
			);

		$ids = self::getProductIds($products);
		$id_list = join(',', $ids);
		if (YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_VAT) == 'true')
			$sql = 'SELECT MAX(p.`weight`) as max_weight, MIN(p.`weight`) as min_weight,
			    MAX(ps.`price` + ps.`price` * t.`rate` / 100) as max_price, MIN(ps.`price` + ps.`price` * t.`rate` / 100) as min_price
			    FROM `'._DB_PREFIX_.'product` p
			    LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ps.id_product = p.id_product
			    LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.id_tax_rules_group = ps.id_tax_rules_group AND trg.active = 1)
			    LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
			    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1)
			    WHERE ps.`id_shop` = '.(int)Context::getContext()->shop->id.' AND ps.active=1
							AND p.`id_product` in ('.pSQL($id_list).') ';
		else
			$sql = 'SELECT MAX(p.`weight`) as max_weight, MIN(p.`weight`) as min_weight,
			    MAX(ps.`price`) as max_price, MIN(ps.`price`) as min_price
			    FROM `'._DB_PREFIX_.'product` p
			    LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ps.id_product = p.id_product
			    WHERE ps.`id_shop` = '.(int)Context::getContext()->shop->id.' AND ps.active=1
			    AND ps.`id_product` in ('.pSQL($id_list).') ';

		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Get Product Ids
	 * @param type $products
	 * @return type
	 */
	private static function getProductIds($products)
	{
		$ids = array();
		foreach ($products as $product) $ids[] = $product['id_product'];
		return $ids;
	}

	/**
	 * Count criterion for products
	 * @param type $criterion
	 * @param type $field_value
	 * @param type $ids
	 * @return int
	 */
	private static function countCriterionForProducts($criterion, $field_value, $ids)
	{
		$type = $criterion['id_criteria_type'];
		$value = $field_value['id_internal'];
		switch ($type)
		{
		case CriteriaTypeEnum::FEATURE:
			return self::countCriterionFeature($ids, $value);
		case CriteriaTypeEnum::ATTRIBUTE:
			return self::countCriterionAttribute($ids, $value);
		case CriteriaTypeEnum::CATEGORY:
			return self::countCriterionCategory($ids, $value);
		case CriteriaTypeEnum::AVAILABILITY:
			return self::countCriterionAvailability($ids);
		case CriteriaTypeEnum::MANUFACTURER:
			return self::countCriterionManufacturer($ids, $value);
		case CriteriaTypeEnum::CONDITION:
			return self::countCriterionCondition($ids, $value);
				case CriteriaTypeEnum::SUPPLIER:
			return self::countCriterionSupplier($ids, $value);
		}
		return 0;
	}

	/**
	 * Count criterion availability
	 * @param type $ids
	 * @return int
	 */
	private static function countCriterionAvailability($ids)
	{
		if (count($ids) == 0) return 0;
		else
		{
			$shop = Context::getContext()->shop;
			$id_list = join(',', $ids);
			$sql = 'SELECT COUNT(DISTINCT(p.id_product))
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
			ON (p.`id_product` = ps.`id_product`)
			WHERE p.`id_product` in ('.pSQL($id_list).') AND p.`available_for_order` = 1 AND ps.`id_shop` = '.(int)$shop->id.'
			AND ps.`active` = 1';

			// by IDs, so we don't have to check for active=1

			return Db::getInstance()->getValue($sql);
		}
	}

	/**
	 * Count criterion manufacturer
	 * @param type $ids
	 * @param type $value
	 * @return int
	 */
	private static function countCriterionManufacturer($ids, $value)
	{
		if (count($ids) == 0) return 0;
		else
		{
			$shop = Context::getContext()->shop;
			$id_list = join(',', $ids);
			$sql = 'SELECT COUNT(DISTINCT(p.`id_product`)) FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
				ON (p.`id_product` = ps.`id_product`)
				WHERE p.`id_product` in ('.pSQL($id_list).')
				AND p.`id_manufacturer` = '.(int)$value.' AND ps.`id_shop` = '.(int)$shop->id;

			// by IDs, so we don't have to check for active=1
			// for the same reason, we could remove the shop_id check..

			return Db::getInstance()->getValue($sql);
		}
	}

	/**
	 * Count criterion supplier
	 * @param type $ids
	 * @param type $value
	 * @return int
	 */
	private static function countCriterionSupplier($ids, $value)
	{
		if (count($ids) == 0) return 0;
		else
		{
			$shop = Context::getContext()->shop;
			$id_list = join(',', $ids);
			$sql = 'SELECT COUNT(DISTINCT(p.`id_product`)) FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
				ON (p.`id_product` = ps.`id_product`)
				WHERE p.`id_product` in ('.pSQL($id_list).')
				AND p.`id_supplier` = '.(int)$value.' AND ps.`id_shop` = '.(int)$shop->id;

			// by IDs, so we don't have to check for active=1
			// for the same reason, we could remove the shop_id check..

			return Db::getInstance()->getValue($sql);
		}
	}

	/**
	 * Count criterion condition
	 * @param type $ids
	 * @param type $value
	 * @return int
	 */
	private static function countCriterionCondition($ids, $value)
	{
		if (count($ids) == 0) return 0;
		else
		{
			$shop = Context::getContext()->shop;
			$id_list = join(',', $ids);
			$conditions = YetAnotherAdvancedSearch::$condition_types;
			$sql = 'SELECT COUNT(DISTINCT(p.`id_product`)) FROM `'._DB_PREFIX_.'product` p
				LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
				ON (p.`id_product` = ps.`id_product`)
				WHERE p.`id_product` in ('.pSQL($id_list).')
				AND p.`condition` = \''.pSQL($conditions[$value]).'\' AND ps.`id_shop` = '.(int)$shop->id;

			// by IDs, so we don't have to check for active=1
			// for the same reason, we could remove the shop_id check..

			return Db::getInstance()->getValue($sql);
		}
	}

	/**
	 * Count criterion category
	 * @param type $ids
	 * @param type $value
	 * @return int
	 */
	private static function countCriterionCategory($ids, $value)
	{
		if (count($ids) == 0) return 0;
		else
		{
			$id_list = join(',', $ids);
			$sql = 'SELECT COUNT(DISTINCT(cp.`id_product`)) FROM `'._DB_PREFIX_.'category_product` cp ';
			$sql .= 'INNER JOIN `'._DB_PREFIX_.'yaas_categories` yc ON (yc.id_category = cp.id_category) ';
			$sql .= ' WHERE cp.`id_product` in ('.pSQL($id_list).') AND (cp.`id_category` = '.(int)$value;
			$sql .= '  OR cp.id_category in (yc.id_children) )';

			// by IDs, so we don't have to check for active=1
			// NO NEED OF SHOP HERE
			return Db::getInstance()->getValue($sql);
		}
	}

	/**
	 * Count criterion feature
	 * @param type $ids
	 * @param type $value
	 * @return int
	 */
	private static function countCriterionFeature($ids, $value)
	{
		if (count($ids) == 0) return 0;
		else
		{
			$id_list = join(',', $ids);
			$sql = 'SELECT COUNT(DISTINCT(fp.`id_product`)) FROM `'._DB_PREFIX_.'feature_product` fp';
			$sql .= ' WHERE fp.`id_product` in ('.pSQL($id_list).') AND fp.`id_feature_value` = '.(int)$value;

			// by IDs, so we don't have to check for active=1
			// NO NEED OF SHOP HERE
			return Db::getInstance()->getValue($sql);
		}
	}

	/**
	 * Count criterion attribute
	 * @param type $ids
	 * @param type $value
	 * @return int
	 */
	private static function countCriterionAttribute($ids, $value)
	{
		if (count($ids) == 0) return 0;
		else
		{
			$id_list = join(',', $ids);
			$sql = 'SELECT COUNT(DISTINCT(pa.`id_product`)) FROM `'._DB_PREFIX_.'product_attribute` pa';
			$sql .= ' INNER JOIN `'._DB_PREFIX_.'product_attribute_combination` pac ON (pac.`id_product_attribute` = pa.`id_product_attribute`)';
			$sql .= ' WHERE pa.`id_product` in ('.pSQL($id_list).') AND pac.`id_attribute` = '.(int)$value;
			$count = Db::getInstance()->getValue($sql);

			// by IDs, so we don't have to check for active=1
			// NO NEED OF SHOP HERE
			return $count;
		}
	}

	/**
	 * Generate request chunk by type
	 * @param type $type
	 * @param type $value
	 * @return string
	 */
	private static function generateRequestChunkByType($type, $value)
	{
		switch ($type)
		{
		case CriteriaTypeEnum::FEATURE:
			return self::generateRequestChunkForFeature($value);
		case CriteriaTypeEnum::ATTRIBUTE:
			return self::generateRequestChunkForAttribute($value);
		case CriteriaTypeEnum::CATEGORY:
			return self::generateRequestChunkForCategory($value);
		case CriteriaTypeEnum::PRICE:
			return self::generateRequestChunkForPrice($value);
		case CriteriaTypeEnum::AVAILABILITY:
			return self::generateRequestChunkForAvailability();
		case CriteriaTypeEnum::MANUFACTURER:
			return self::generateRequestChunkForManufacturer($value);
		case CriteriaTypeEnum::CONDITION:
			return self::generateRequestChunkForCondition($value);
		case CriteriaTypeEnum::WEIGHT:
			return self::generateRequestChunkForWeight($value);
				case CriteriaTypeEnum::SUPPLIER:
			return self::generateRequestChunkForSupplier($value);
		default:
			return '';
		}
	}

	/**
	 * Generate request chunk for category
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForCategory($value)
	{
		return 'p.id_product in (SELECT cp.`id_product` FROM `'._DB_PREFIX_.'category_product` cp
			INNER JOIN `'._DB_PREFIX_.'yaas_categories` yc ON (yc.id_category = cp.id_category)
			WHERE cp.id_category = '.(int)$value.' OR cp.id_category in (yc.id_children) )';
		// NO NEED OF SHOP HERE
	}

	/**
	 * Generate request chunk for price
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForPrice($value)
	{
		// two components for prices
		$elements = explode('p', $value);
		if (count($elements) == 2)
		{
			$id_internal = $elements[0];
			$value = $elements[1];
		}
		switch ($id_internal)
		{
		case CriteriaSubTypeEnum::PRICE_MIN:
			if ($value > 0)
				if (YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_VAT) == 'true')
					return '(product_shop.`price` + product_shop.`price` * t.`rate` / 100) >= \''.(double)$value.'\'';
				else
					return 'product_shop.`price` >= \''.(double)$value.'\''; // not p.'price'..
			break;
		case CriteriaSubTypeEnum::PRICE_MAX:
			if ($value > 0)
				if (YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_VAT) == 'true')
					return '(product_shop.`price` + product_shop.`price` * t.`rate` / 100) <= \''.(double)$value.'\'';
				else
					return 'product_shop.`price` <= \''.(double)$value.'\''; // not p.'price'..
			break;
		}
		return '';
	}

	/**
	 * Generate request chunk for availability
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForAvailability()
	{
		// no need CriteriaSubTypeEnum::AVAILABILITY_AVAILABLE:
		$shop = Context::getContext()->shop;
		return 'p.`id_product` IN (SELECT DISTINCT(p.id_product)
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
			ON (p.`id_product` = ps.`id_product`)
			WHERE p.`available_for_order` = 1 AND ps.`id_shop` = '.(int)$shop->id.'
			AND ps.`active` = 1)';
	}

	/**
	 * Generate request chunk for manufacturer
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForManufacturer($value)
	{
		return 'p.`id_manufacturer` = '.(int)$value;
	}

	/**
	 * Generate request chunk for supplier
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForSupplier($value)
	{
		return 'p.`id_supplier` = '.(int)$value;
	}

	/**
	 * Generate request chunk for condition
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForCondition($value)
	{
		$conditions = YetAnotherAdvancedSearch::$condition_types;
		return 'p.`condition` = \''.pSQL($conditions[$value]).'\'';
	}

	/**
	 * Generate request chunk for feature
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForFeature($value)
	{
		return 'p.id_product in (SELECT `id_product` FROM `'._DB_PREFIX_.'feature_product`
			WHERE id_feature_value = '.(int)$value.')';

		// NO NEED OF SHOP HERE
	}

	/**
	 * Generate request chunk for attribute
	 * @param type $value
	 * @return type
	 */
	private static function generateRequestChunkForAttribute($value)
	{
		return 'p.id_product in (SELECT pa.`id_product` FROM `'._DB_PREFIX_.'product_attribute` pa
			INNER JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
			ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
			WHERE pac.`id_attribute` = '.(int)$value.')';

		// NO NEED OF SHOP HERE
	}

	/**
	 * Generate Request Chunk for Weight Criteria
	 * @param type $value
	 * @return string
	 */
	private static function generateRequestChunkForWeight($value)
	{
		// two components for weights
		$elements = explode('p', $value);
		if (count($elements) == 2)
		{
			$id_internal = $elements[0];
			$value = $elements[1];
		}
		switch ($id_internal)
		{
		case CriteriaSubTypeEnum::WEIGHT_MIN:
			if ($value > 0)
				return 'p.`weight` >= \''.(double)$value.'\'';
			break;
		case CriteriaSubTypeEnum::WEIGHT_MAX:
			if ($value > 0)
				return 'p.`weight` <= \''.(double)$value.'\'';
			break;
		}
		return '';
	}

	/**
	 * Group Criteria
	 * @param type $criteria
	 * @return type
	 */
	private static function groupCriteria($criteria)
	{
		$grouped_criteria = array();
		foreach ($criteria as $criterion)
		{

			// regexp check could be useful here
			$elements = explode('v', $criterion);
			if (count($elements) == 2)
			{
				$key = $elements[0];
				if (!array_key_exists($key, $grouped_criteria))
					$grouped_criteria[$key] = array();
				$grouped_criteria[$key][] = $elements[1];
			}
		}
		return $grouped_criteria;
	}
}
