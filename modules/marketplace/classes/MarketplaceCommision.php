<?php
class MarketplaceCommision extends ObjectModel
{
	public $id;
	public $commision;
	public $customer_id;
	public $customer_name;

	public static $definition = array(
		'table' => 'marketplace_commision',
		'primary' => 'id',
		'fields' => array(
			'commision' => array('type' => self::TYPE_FLOAT, 'required' => true),
			'customer_id' => array('type' => self::TYPE_INT, 'required' => true),
			'customer_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
		),
	);
	
	/**
	 * [getSellerCommission get applied commission by particular seller]
	 * @return [type] [description]
	 */
	public function getCommissionRateBySeller()
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `commision` FROM `'._DB_PREFIX_.'marketplace_commision`
																	WHERE customer_id = '.(int)$this->customer_id);
	}
	
	public function findAllCustomerInfo()
	{
		$customer_info  = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'customer`
														WHERE `id_customer` = '.(int)$this->customer_id);
		if(empty($customer_info))
			return false;

		return $customer_info;
	}

	/**
	 * get the customer for whose commission is not set 
	 * @return [array or false] [customer array]
	 */
	public function getSellerNotHaveCommissionSet()
	{
		$mp_seller_info = Db::getInstance()->executeS('SELECT c.`id_customer`,c.`email`
												FROM `'._DB_PREFIX_.'marketplace_customer` mc
												JOIN `'._DB_PREFIX_.'customer` c ON (mc.id_customer = c.id_customer)
												WHERE mc.is_seller = 1 AND mc.id_customer NOT IN (SELECT customer_id
												FROM `'._DB_PREFIX_.'marketplace_commision`)');
		if(empty($mp_seller_info))
			return false;

		return $mp_seller_info;
	}

	public function getCommissionById($id)
	{
		$commission = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_commision`
																	WHERE id = ".(int)$id);

		if ($commission)
			return $commission;

		return false;
	}

	/**
	 * Could not fine any function in prestashop
	 * [getTaxByIdOrderDetail get tax amout by id_order_details]
	 * @param  [int] $id_order_detail
	 * @return [float]
	 */
	public function getTaxByIdOrderDetail($id_order_detail)
	{
		$tax_amt = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT `total_amount` FROM `'._DB_PREFIX_.'order_detail_tax`
																	WHERE `id_order_detail` = '.(int)$id_order_detail);

		if ($tax_amt)
			return $tax_amt;

		return 0;
	}
}