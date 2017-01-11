<?php
class MarketplaceSellerOrderDetails extends ObjectModel
{
	public $id;

	/** @var id of marketplace_seller_orders*/
	public $id_seller_order;

	public $product_id;

	/** @var id_customer of marketplace seller */
	public $customer_id;

	public $customer_name;
	public $product_name;
	public $price;
	public $quantity;
	public $commision;	
	public $id_order;
	public $date_add;

	public static $definition = array(
		'table' => 'marketplace_commision_calc',
		'primary' => 'id',
		'fields' => array(
			'product_id' => array('type' => self::TYPE_INT, 'required' => true),
			'id_seller_order' => array('type' => self::TYPE_INT, 'required' => true),
			'customer_id' => array('type' => self::TYPE_INT, 'required' => true),
			'customer_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'product_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'price' => 	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'quantity' => array('type' => self::TYPE_INT, 'required' => true),
			'commision' => 	array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
			'id_order' => array('type' => self::TYPE_INT, 'required' => true),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
		),
	);

	public function getOrderCommissionDetails($id_order)
	{
		$details = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."marketplace_commision_calc` WHERE `id_order` = ".(int)$id_order);

		if ($details && !empty($details))
			return $details;

		return false;
	}

	public function getSellerOrderedProductDetails($id_order)
	{
		$details = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."marketplace_shop_product` msp
                        JOIN `"._DB_PREFIX_."order_detail` ordd ON (ordd.`product_id` = msp.`id_product`)
                        JOIN `"._DB_PREFIX_."marketplace_seller_product` mssp ON (mssp.`id` = msp.`marketplace_seller_id_product`)
                        JOIN `"._DB_PREFIX_."marketplace_customer` mc ON (mc.`marketplace_seller_id` = mssp.`id_seller`)
                        JOIN `"._DB_PREFIX_."customer` c ON (c.`id_customer` = mc.`id_customer`)
                        AND ordd.`id_order`= ".(int)$id_order);

		if ($details && !empty($details))
			return $details;

		return false;
	}
}