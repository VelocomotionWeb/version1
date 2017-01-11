<?php
class MarketplaceShop extends ObjectModel 
{
	public $id;
	public $id_customer;
	public $shop_name;
	public $link_rewrite;
	public $about_us;
	public $is_active;
	
	public static $definition = array(
		'table' => 'marketplace_shop',
		'primary' => 'id',
		'fields' => array(
			'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'shop_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
			'link_rewrite' => array('type' => self::TYPE_STRING, 'validate' => 'isLinkRewrite', 'required' => true, 'size' => 128),
			'about_us' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'is_active' => array('type' => self::TYPE_BOOL,'validate' => 'isBool')
		),
	);
	
	public static function findMpSellerIdByShopId($id)
	{
		$mp_seller_id = Db::getInstance()->getvalue("SELECT  mc.`marketplace_seller_id`
								FROM "._DB_PREFIX_."marketplace_shop AS ms
								LEFT JOIN "._DB_PREFIX_."marketplace_customer AS mc
								ON (ms.`id_customer` = mc.`id_customer`) WHERE ms.`id`=".$id);
		
		if ($mp_seller_id)
			return $mp_seller_id;
		
		return false;
	}
	
	public function getMarketPlaceShopDetail($id_shop)
	{
		$marketplaceshopdetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_shop`
										WHERE id =".$id_shop);
		
		if (!empty($marketplaceshopdetail))
			return $marketplaceshopdetail;

		return false;
	}
	
	public function getMarketPlaceShopInfoByCustomerId($id_customer)
	{
		$shop_info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_shop`
								WHERE id_customer =".$id_customer);

		if (!empty($shop_info))
			return $shop_info;

		return false;
	}
	
	public static function getIdShopByName($shop_link_rewrite)
	{
		$id_shop = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue("SELECT `id` FROM `"._DB_PREFIX_."marketplace_shop`
								WHERE link_rewrite = '".$shop_link_rewrite."'");
		
		if ($id_shop)
			return $id_shop;

		return false;
	}

	public static function getMpShopIdByCustomerId($id_customer)
	{
		$id_shop = Db::getInstance()->getValue("SELECT `id` FROM `"._DB_PREFIX_."marketplace_shop`
								WHERE id_customer = ".(int)$id_customer);

		if ($id_shop)
			return $id_shop;

		return false;
	}

	public function changeMpShopStatus($id_customer, $status)
	{
		return Db::getInstance()->update('marketplace_shop', array('is_active' => $status),'id_customer='.$id_customer);
	}
}
?>