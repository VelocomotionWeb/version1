<?php
class StoreProduct extends ObjectModel
{
	public $id_product;
	public $id_store;

	public static $definition = array(
		'table' => 'store_products',
		'primary' => 'id',
		'fields' => array(
		         'id_product' => array('type' => self::TYPE_INT),
				 'id_store' => array('type' => self::TYPE_INT)
				)
		);

	/**
	 * couldn't found any function in marketplace
	 * @param  [int] $id_seller
	 * @return [array/false]
	 */
	public static function getMpSellerActiveProducts($id_seller)
	{
		$obj_mpshopproduct = new MarketplaceShopProduct();
		$mp_products = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product`
															WHERE id_seller = '.$id_seller.' AND `active` = 1');
		if ($mp_products && !empty($mp_products))
		{
			foreach ($mp_products as $key => $product)
			{
				$mp_shopproduct = $obj_mpshopproduct->findMainProductIdByMppId($product['id']);
				if ($mp_shopproduct)
					$mp_products[$key]['id_product'] = $mp_shopproduct['id_product'];
			}
			return $mp_products;
		}
		return false;
	}

	/**
	 * [getProductStore get only active store products]
	 * @param  [int] $id_product [prestashop product id]
	 * @return [array/false] [array]
	 */
	public static function getProductStore($id_product, $active = false)
	{
		if ($active)
			$mp_stores = Db::getInstance()->executeS('SELECT sp.`id_store`
												FROM `'._DB_PREFIX_.'store_products` AS sp
												LEFT JOIN `'._DB_PREFIX_.'store_locator` AS sl ON (sp.`id_store` = sl.`id`)
												WHERE sp.`id_product` = '.(int)$id_product.' AND sl.`active` = 1');
		else
			$mp_stores = Db::getInstance()->executeS('SELECT sp.`id_store`
												FROM `'._DB_PREFIX_.'store_products` AS sp
												LEFT JOIN `'._DB_PREFIX_.'store_locator` AS sl ON (sp.`id_store` = sl.`id`)
												WHERE sp.`id_product` = '.(int)$id_product);

		if ($mp_stores && !empty($mp_stores))
			return $mp_stores;

		return false;
	}

	public static function getSellerProducts($id_store)
	{
		$store_products = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'store_products`
															WHERE id_store = '.(int)$id_store);
		if ($store_products && !empty($store_products))
			return $store_products;

		return false;
	}
}

?>