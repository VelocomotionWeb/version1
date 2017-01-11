<?php
class PaymentDetails extends ObjectModel
{
	public $id;
	public $id_customer;
	public $payment_mode_id;
	public $payment_detail;
	
 	 	 	 	 	 	 
	public static $definition = array(
		'table' => 'marketplace_customer_payment_detail',
		'primary' => 'id',
		'fields' => array(
			'id_customer' => array('type' => self::TYPE_INT),
			'payment_mode_id' =>	array('type' => self::TYPE_INT,'required' => true),
			'payment_detail' => array('type' => self::TYPE_STRING)			
		),
	);

	public function getSellerPaymentDetails($id_customer)
	{
		$seller_payment = Db::getInstance()->getRow(
							'SELECT mcpd.`id`, mcpd.`id_customer`, mcpd.`payment_mode_id`, mcpd.`payment_detail`, mpm.`payment_mode`
							FROM `'._DB_PREFIX_.'marketplace_customer_payment_detail` mcpd
							JOIN `'._DB_PREFIX_.'marketplace_payment_mode` mpm ON (`mcpd`.payment_mode_id = `mpm`.id)
							WHERE `id_customer`='.(int)$id_customer);

		if ($seller_payment)
			return $seller_payment;

		return false;
	}

	public function getSellerPaymentMode($id_payment_mode)
	{
		$payment_mode = Db::getInstance()->getValue('SELECT `payment_mode`
									FROM `'._DB_PREFIX_.'marketplace_payment_mode`
									WHERE `id` = '.$id_payment_mode);
		if ($payment_mode)
			return $payment_mode;

		return false;
	}

	public function getAdminPaymentOption()
	{
		$payment_option = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_payment_mode`');

		if (!empty($payment_option))
			return $payment_option;

		return false;
	}

	/**
	 * delete sellet payment details
	 * @param  [int] $id [auto increamented]
	 * @return [bool]
	 */
	public function deleteSellerPayment($id)
	{
		$delete = Db::getInstance()->delete('marketplace_customer_payment_detail', 'id = '.$id);

		if($delete)
			return true;

		return false;
	}
}
?>