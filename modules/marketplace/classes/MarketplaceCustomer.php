<?php
class MarketplaceCustomer extends ObjectModel
{
	public $id;	
	public $marketplace_seller_id;
	public $id_customer;
	public $is_seller;

	public static $definition = array(
		'table' => 'marketplace_customer',
		'primary' => 'id',
		'fields' => array(
			'marketplace_seller_id' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'id_customer' => array('type' => self::TYPE_INT, 'validate' => 'isInt'),
			'is_seller' => array('type' => self::TYPE_INT, 'validate' => 'isInt')
		),
	);
	
	public function insertMarketplaceCustomer($id_seller, $id_customer, $active = false)
	{
		$obj_mpcustomer = new MarketplaceCustomer();
		$obj_mpcustomer->marketplace_seller_id = (int)$id_seller;
		$obj_mpcustomer->id_customer = (int)$id_customer;
		$obj_mpcustomer->is_seller = ($active ? 1 : 0);
		$obj_mpcustomer->save();
		$id_insert = $obj_mpcustomer->id;
		if ($id_insert)
			return true;
		return false;
	}

	public function insertActiveMarketplaceCustomer($marketplace_seller_id,$id_customer)
	{
		$is_insert = Db::getInstance()->insert('marketplace_customer', array(
													'marketplace_seller_id' => (int) $marketplace_seller_id,
													'id_customer' => (int) $id_customer,
													'is_seller' => 1)
											);
		if($is_insert)
			return true;
		else
			return false;
	}

	public function findMarketPlaceCustomer($id_customer)
	{
		$mp_customer_info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_customer`
																WHERE id_customer ='.$id_customer);
		if (empty($mp_customer_info))
			return false;
		else
			return $mp_customer_info;
	}

	public function findIsallCustomerSeller()
	{
		$customer_info = Db::getInstance()->executeS("SELECT cus.`id_customer`,cus.`email`
			FROM `"._DB_PREFIX_."customer` cus
			INNER JOIN `"._DB_PREFIX_."marketplace_customer` mcus
			ON ( cus.`id_customer` = mcus.`id_customer`) WHERE mcus.`is_seller` = 1");
		if (empty($customer_info))
			return false;

		return $customer_info;
	}

	public function getAllSellerInfo()
	{
		$customer_info = Db::getInstance()->executeS("SELECT cus.`id_customer`,cus.`email`, mpsi.* FROM `"._DB_PREFIX_."customer` cus JOIN `"._DB_PREFIX_."marketplace_customer` mcus ON ( cus.`id_customer` = mcus.`id_customer`) JOIN `"._DB_PREFIX_."marketplace_seller_info` mpsi ON (mpsi.`id` = mcus.`marketplace_seller_id`) WHERE mcus.`is_seller` = 1");
		if (empty($customer_info))
			return false;

		return $customer_info;
	}

	public function getCustomerId($id_seller)
	{
		$id_customer = Db::getInstance()->getValue("SELECT `id_customer` FROM `"._DB_PREFIX_."marketplace_customer`
											WHERE marketplace_seller_id = ".(int)$id_seller);
		
		if ($id_customer)
			return $id_customer;
			
		return false;
	}

	public function getCustomeInfoByID($id)
	{
		$info  = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_seller_info`
											WHERE id =".$id);
		if ($info)
			return $info;
		return false;
	}

	public function getMpCustomer($id_mp_seller)
	{
		$mp_seller = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_customer`
															WHERE `marketplace_seller_id`=".$id_mp_seller);

		if ($mp_seller)
			return $mp_seller;

		return false;
	}

	public function getPsCustomerWhoseNotSeller()
	{
		$ps_customer = Db::getInstance()->executeS("SELECT cus.`id_customer`,cus.`email` FROM `"._DB_PREFIX_."customer` cus
												LEFT OUTER JOIN `"._DB_PREFIX_."marketplace_customer` mcus
												ON (cus.id_customer = mcus.id_customer)
												WHERE mcus.id_customer IS NULL");
		if ($ps_customer)
			return $ps_customer;

		return false;
	}

	public function changeMpSellerStatus($id_seller, $status)
	{
		return Db::getInstance()->update('marketplace_customer', array('is_seller' => $status), 'marketplace_seller_id = '.$id_seller);
	}

	public static function isCustomerActiveSeller($id_customer)
	{
		return Db::getInstance()->getValue('SELECT `marketplace_seller_id` FROM `'._DB_PREFIX_.'marketplace_customer`
											WHERE `id_customer`='.(int)$id_customer.' AND `is_seller` = 1');
	}
}
?>