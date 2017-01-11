<?php
class MarketplaceShippingInfo extends ObjectModel
{
	public $id;
	public $order_id;
	public $shipping_description;
	public $shipping_date;

	public static $definition = array(
		'table' => 'marketplace_shipping',
		'primary' => 'id',
		'fields' => array(
			'order_id' => array('type' => self::TYPE_INT,'required' => true),
			'shipping_description' => array('type' => self::TYPE_STRING),
			'shipping_date' => array('type' => self::TYPE_DATE)
		)
	);

	public function getShippingDetailsByOrderId ($id_order)
	{
		if (isset($id_order))
		{
			$shipping_info = Db::getInstance()->getRow('SELECT * FROM `'._DB_PREFIX_.'marketplace_shipping`
																	WHERE `order_id`='.$id_order);
			if ($shipping_info)
				return $shipping_info;
		}
		return false;
	}
}
?>