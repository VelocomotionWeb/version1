<?php
class MarketplaceSellerOrders extends ObjectModel
{
	public $id;

	/** @var id_customer of marketplace seller */
	public $id_customer_seller;

	public $seller_shop;
	public $total_earn;
	public $total_admin_commission;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'marketplace_seller_orders',
		'primary' => 'id',
		'fields' => array(
			'seller_shop' => array('type' => self::TYPE_STRING),
			'id_customer_seller' => array('type' => self::TYPE_INT),
			'total_earn' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'total_admin_commission' => array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		),
	);

	public static function updateMarketplaceSellerOrder($product, $commission)
	{
		$id_customer_seller = $product['id_customer'];
		if ($id = MarketplaceSellerOrders::isSellerOrderExist($id_customer_seller))
		{
			$obj_mpsellerorder = new MarketplaceSellerOrders($id);
			$obj_mpsellerorder->total_earn = MarketplaceSellerOrders::getSellerTotalEarn($id_customer_seller) + $product['total_price_tax_incl'];
			$obj_mpsellerorder->total_admin_commission = MarketplaceSellerOrders::getAdminTotalCommissionBySeller($id_customer_seller) + $commission;
			$obj_mpsellerorder->save();
			if ($id_seller_order = $obj_mpsellerorder->id)
				return $id_seller_order;
		}
		else
		{
			$obj_mp_shop = new MarketplaceShop();
			$mp_shop_info = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer_seller);
			$obj_mpsellerorder = new MarketplaceSellerOrders();
			$obj_mpsellerorder->seller_shop = $mp_shop_info['shop_name'];
			$obj_mpsellerorder->id_customer_seller = $id_customer_seller;
			$obj_mpsellerorder->total_earn = $product['total_price_tax_incl'];
			$obj_mpsellerorder->total_admin_commission = $commission;
			$obj_mpsellerorder->save();
			if ($id_seller_order = $obj_mpsellerorder->id)
				return $id_seller_order;
		}

		return false;
	}

	public static function getSellerTotalEarn($id_customer_seller)
	{
		return Db::getInstance()->getValue('SELECT `total_earn` FROM `'._DB_PREFIX_.'marketplace_seller_orders`
																	WHERE `id_customer_seller`='.(int)$id_customer_seller);
	}

	public static function getAdminTotalCommissionBySeller($id_customer_seller)
	{
		return Db::getInstance()->getValue('SELECT `total_admin_commission` FROM `'._DB_PREFIX_.'marketplace_seller_orders`
																	WHERE `id_customer_seller`='.(int)$id_customer_seller);
	}

	public static function isSellerOrderExist($id_customer_seller)
	{
		return Db::getInstance()->getValue('SELECT `id` FROM `'._DB_PREFIX_.'marketplace_seller_orders`
																WHERE `id_customer_seller` = '.(int)$id_customer_seller);
	}
}