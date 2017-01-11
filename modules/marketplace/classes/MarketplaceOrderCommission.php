<?php
class MarketplaceOrderCommission extends ObjectModel
{
	public $id;
	public $id_order;
	public $id_customer;
	public $tax;
	public $shipping;
	public $shipping_amt;
	public $admin_commission;


	public static $definition = array(
		'table' => 'marketplace_order_commision',
		'primary' => 'id',
		'fields' => array(
			'id_order' => array('type' => self::TYPE_INT, 'required' => true,'size' => 10,'validate' => 'isUnsignedId'),
			'id_customer' => array('type' => self::TYPE_INT, 'size' => 10,'required' => true,'validate' => 'isUnsignedId'),
			'tax' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName','size' => 100),
			'shipping' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName','size' => 100),
			'shipping_amt' => array('type' => self::TYPE_FLOAT, 'required' => true,'validate' => 'isPrice'),
			'admin_commission' => array('type' => self::TYPE_FLOAT, 'required' => true,'validate' => 'isPrice'),

		),
	);

	public function getIdOrderById($id)
	{
		$id_order = Db::getInstance()->getValue('SELECT `id_order` FROM `'._DB_PREFIX_.'marketplace_order_commision`
																	WHERE `id`='.(int)$id);
		if ($id_order)
			return $id_order;

		return false;
	}

	public function getDetailsList($id_order)
	{
		$detail = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_commision_calc`
																	WHERE `id_order`='.(int)$id_order);

		if ($detail)
			return $detail;
		return false;
	}
}
?>