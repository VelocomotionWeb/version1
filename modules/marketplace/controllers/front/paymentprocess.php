<?php
class MarketplacePaymentProcessModuleFrontController extends ModuleFrontController 
{
	public function initContent() 
	{
		parent::initContent();

		$link = new Link();
		if (isset($this->context->cookie->id_customer))
		{
			$id_customer = $this->context->cookie->id_customer;
			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$id = Tools::getValue('id');
				$payment_mode = Tools::getValue('payment_mode');
				$payment_detail = Tools::getValue('payment_detail');

				if ($id)
					$obj_payment = new PaymentDetails($id); // if update payment details
				else
					$obj_payment = new PaymentDetails();

				$obj_payment->id_customer = $id_customer;
				$obj_payment->payment_mode_id = $payment_mode;
				$obj_payment->payment_detail = $payment_detail;
				$obj_payment->save();
				if ($obj_payment->id)
				{
					if ($id)
						Tools::redirect($link->getModuleLink('marketplace', 'mppayment', array('edited' => 1)));
					else
						Tools::redirect($link->getModuleLink('marketplace', 'mppayment', array('created' => 1)));
				}
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}
}
?>