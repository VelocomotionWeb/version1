<?php
class MarketplaceContactSellerProcessModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		$this->display_header = false;
		$this->display_footer = false;
	}

	public function initContent()
	{
		$result = array();
		$id_customer = Tools::getValue('id_customer');
		$customer_email = Tools::getValue('customer_email');
		$query_subject = Tools::getValue('query_subject');
		$query_description = Tools::getValue('query_description');

		$id_seller = Tools::getValue('id_seller');
		$id_product = Tools::getValue('id_product');

		$obj_mpseller = new SellerInfoDetail($id_seller);
		$seller_email = $obj_mpseller->business_email;
		$seller_name = $obj_mpseller->seller_name;

		$obj_mpcustomerquery = new MarketplaceCustomerQuery();
		$obj_mpcustomerquery->id_product = $id_product;
		$obj_mpcustomerquery->id_customer = $id_customer;
		$obj_mpcustomerquery->id_seller = $id_seller;
		$obj_mpcustomerquery->subject = $query_subject;
		$obj_mpcustomerquery->description = $query_description;
		$obj_mpcustomerquery->customer_email = $customer_email;
		$obj_mpcustomerquery->active = 1;
		$obj_mpcustomerquery->save();
		$id_query = $obj_mpcustomerquery->id;

		if ($id_query)
		{
			$templateVars = array('{customer_email}' => $customer_email, 
								'{query_subject}' => $query_subject,
								'{seller_name}' => $seller_name,
								'{query_description}' => $query_description);

			$temp_path = _PS_MODULE_DIR_.'marketplace/mails/';
			
			$query_mailed = Mail::Send(
								(int)$this->context->language->id,
								'contact_seller_mail',
								$query_subject,
								$templateVars,
								$seller_email,
								null,
								$customer_email,
								null,
								null,
								null,
								$temp_path,
								false,
								null,
								null);

			if ($query_mailed)
			{
				$result['status'] = 'ok';
				$result['msg'] = 'Mail successfully sent.';
			}
			else
			{
				$result['status'] = 'ko';
				$result['msg'] = 'Some error while sending mail';
			}
		}
		else
		{
			$result['status'] = 'ko';
			$result['msg'] = 'Some error sending message to seller.';
		}
		die(Tools::jsonEncode($result));
	}
}