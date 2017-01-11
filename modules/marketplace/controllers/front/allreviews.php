<?php
class MarketplaceAllReviewsModuleFrontController extends ModuleFrontController	
{
	public function initContent() 
	{
		$id_seller = Tools::getValue('seller');
		$obj_review = new Reviews();
		$obj_mpshop = new MarketplaceShop();
		$obj_mpseller = new SellerInfoDetail();

		$reviews_info = $obj_review->getSellerReviewByIdSeller($id_seller);

		//Review details
		if($reviews_info)
		{
			foreach($reviews_info as $key => $reviews)
			{
				//get name from prestashop customer table
				$obj_customer = new Customer($reviews['id_customer']);
				$reviews_info[$key]['customer_name'] = $obj_customer->firstname." ".$obj_customer->lastname;
			}
			$this->context->smarty->assign("reviews_info", $reviews_info);
		}
	


		//if user is logged in show left marketplace menu
		$id_customer = $this->context->cookie->id_customer;
		if($id_customer)
		{
			$mp_shop = $obj_mpshop->getMarketPlaceShopInfoByCustomerId($id_customer);
			$seller_info = $obj_mpseller->sellerDetail($id_seller);
			if($mp_shop)
			{
				if($mp_shop['shop_name'] == $seller_info['shop_name'])
				{
					$id_shop = $mp_shop['id'];
					$this->context->smarty->assign("id_shop", $id_shop);
					$this->context->smarty->assign("id_customer", $id_customer);
					$this->context->smarty->assign("logic", "all_reviews");
					$this->context->smarty->assign("is_seller", 1);
					$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
					$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
				}
			}
		}
		
		$this->setTemplate('allreviews.tpl');
		parent::initContent();
	}

	public function setMedia() 
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/shop_store.css');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/store_profile.css');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/all_reviews.css');
	}
}
?>