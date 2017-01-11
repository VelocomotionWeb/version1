<?php
class MarketplaceShopProduct extends ObjectModel
{
	public $id;	
	public $id_shop;
	public $id_product;
	public $marketplace_seller_id_product;
			
	public static $definition = array(
		'table' => 'marketplace_shop_product',
		'primary' => 'id',
		'fields' => array(
			'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'marketplace_seller_id_product' => array('type' => self::TYPE_INT, 'validate' => 'isInt')
		),
	);
	
	public function delete()
	{
		if (!parent::delete())
			return false;
		return true;
	}

	public function findShopIdByMpsid($marketplace_seller_id_product)
	{
		$shop_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("SELECT `id_shop` FROM "._DB_PREFIX_."marketplace_shop_product
								WHERE marketplace_seller_id_product=".$marketplace_seller_id_product);
		if ($shop_id)
			return $shop_id;

		return false;
	}
	
	public function findMainProductIdByMppId($marketplace_seller_id_product)
	{
		$mp_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM "._DB_PREFIX_."marketplace_shop_product
								WHERE marketplace_seller_id_product=".$marketplace_seller_id_product);
		if($mp_product)
			return $mp_product;

		return false;
	}

	public function getSellerPsIdProduct($mp_id_shop)
	{
		$ids = Db::getInstance()->executeS("SELECT `id_product` FROM `"._DB_PREFIX_."marketplace_shop_product`
													WHERE `id_shop` = ".$mp_id_shop);

		if ($ids && !empty($ids))
			return $ids;

		return false;
	}

	public function getMpProductIdByPsProductId($id_product)
	{
		$mp_product_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("SELECT `marketplace_seller_id_product` FROM
					"._DB_PREFIX_."marketplace_shop_product WHERE id_product=".$id_product);

		if ($mp_product_id)
			return $mp_product_id;

		return false;
	}

	public function deleteProductBySellerProductId($seller_id_product)
	{
		$delete_shop_product = Db::getInstance()->delete('marketplace_shop_product', 'marketplace_seller_id_product='.$seller_id_product);

		if ($delete_shop_product)
			return true;

		return false;
	}
}
?>