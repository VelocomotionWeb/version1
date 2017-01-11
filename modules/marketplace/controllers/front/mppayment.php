<?php
class MarketplaceMpPaymentModuleFrontController extends ModuleFrontController 
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
				$obj_mp_customer = new MarketplaceCustomer();
				$obj_mp_shop = new MarketplaceShop();
				$obj_pay_details = new PaymentDetails();

				$mp_customer = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
				if ($mp_customer && $mp_customer['is_seller'] == 1)
				{
					$mp_shop = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
					if ($mp_shop)
					{
						$id = Tools::getValue('id');
						$delete = Tools::getValue('delete');
						$edited = Tools::getValue('edited');
						$created = Tools::getValue('created');

						//if come after update payment
						if ($edited)
							$this->context->smarty->assign('edited', 1);

						//if come after payment creation
						if ($created)
							$this->context->smarty->assign('created', 1);

						//if seller edit or delete payment details
						if ($id)
						{
							if ($delete)
							{
								$delete = $obj_pay_details->deleteSellerPayment($id);
								if ($delete)
									$this->context->smarty->assign('deleted', 1);
							}
							else
								$this->context->smarty->assign('edit', 1);
						}


						//get all admin payment option
						$mp_payment_option = $obj_pay_details->getAdminPaymentOption();
						if ($mp_payment_option)
							$this->context->smarty->assign("mp_payment_option", $mp_payment_option);


						//get seller selected payment
						$seller_payment_detail = $obj_pay_details->getSellerPaymentDetails($id_customer);

						if($seller_payment_detail)
							$this->context->smarty->assign('seller_payment_details', $seller_payment_detail);

						$this->context->smarty->assign("customer_id",$id_customer);
						$this->context->smarty->assign('is_seller', $mp_customer['is_seller']);
						$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
						$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
						$this->context->smarty->assign("logic",1);
						$this->setTemplate('mppayment.tpl');
					}
				}
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('marketplace', 'mppayment')));
	}
	
	public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/imageedit.js');
    }
}
?>