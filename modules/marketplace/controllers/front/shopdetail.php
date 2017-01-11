<?php
class MarketplaceShopDetailModuleFrontController extends ModuleFrontController	
{
	public function initContent()
	{
		parent::initContent();

        $id_lang = $this->context->cookie->id_lang;
		$obj_mpshop = new MarketplaceShop();
		$obj_mpsuctomer = new MarketplaceCustomer();
		$obj_sellerinfo = new SellerInfoDetail();
		$obj_mpproduct = new SellerProductDetail();

		$shop_link_rewrite = Tools::getValue('mp_shop_name');

		$id_shop = $obj_mpshop->getIdShopByName($shop_link_rewrite);
		if ($id_shop) 
		{
			$mp_shop_details = $obj_mpshop->getMarketPlaceShopDetail($id_shop);
			if ($mp_shop_details['is_active'])
			{
				if ($mp_shop_details) 
				{
					$mp_customer_info = $obj_mpsuctomer->findMarketPlaceCustomer($mp_shop_details['id_customer']);
					if ($mp_customer_info) 
					{
						$mp_seller_id = $mp_customer_info['marketplace_seller_id'];
						$name_shop = $mp_shop_details['shop_name'];
						$shop_link_rewrite = $mp_shop_details['link_rewrite'];
						$ps_id_shop = $this->context->shop->id;

						if ($mp_customer_info['is_seller'])
						{
							//Rating Information
							$this->getRatingDetails($mp_seller_id);
						
							//Get Seller Details
							$mp_seller_info = $obj_sellerinfo->sellerDetail($mp_seller_id);

							if ($mp_seller_info) 
							{
								//Product details
								$product_detail = $obj_mpproduct->getActiveMpProductWithImage($id_shop, $ps_id_shop, $id_lang);
								if ($product_detail)
									$this->context->smarty->assign('mp_shop_product', $product_detail);

								$this->context->smarty->assign('id_shop', $id_shop);
								$this->context->smarty->assign('name_shop', $name_shop);
								$this->context->smarty->assign('shop_link_rewrite', $shop_link_rewrite);
								$this->context->smarty->assign('seller_id', $mp_seller_id);
								$this->context->smarty->assign('mp_shop_details', $mp_shop_details);
								$this->context->smarty->assign('mp_seller_info', $mp_seller_info);
								$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
								$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
							}
							else
								Tools::redirect(__PS_BASE_URI__.'pagenotfound');
						}
					} 
					else
						Tools::redirect(__PS_BASE_URI__.'pagenotfound');
				}
				else
					Tools::redirect(__PS_BASE_URI__.'pagenotfound');
			}
			else
				Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		} 
		else
			Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		

		if (!file_exists(_PS_MODULE_DIR_."marketplace/views/img/shop_img/".$mp_seller_id."-".$name_shop.".jpg"))
			$this->context->smarty->assign("no_shop_img", 1);
		else
			$this->context->smarty->assign("no_shop_img", 0);
		//assign the seller details view vars
		SellerInfoDetail::assignSellerDetailsView();
		$this->context->smarty->assign('MP_SHOW_SELLER_DETAILS', Configuration::get('MP_SHOW_SELLER_DETAILS'));
		$this->setTemplate('shopdetail.tpl');									
	}

	public function getRatingDetails($mp_seller_id)
	{
		$reviews_info = Reviews::getSellerReviewByIdSeller($mp_seller_id, true);

		if ($reviews_info)
		{
			$rating = 0;
			$i = 0;
			foreach($reviews_info as $reviews)
			{
				$rating = $rating + $reviews['rating'];
				$i++;
			}

			if($rating != 0)
				$avg_rating = (double)($rating/$i);
			else
				$avg_rating = 0;
			
			$this->context->smarty->assign("avg_rating", $avg_rating);
		}
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/store_profile.css');
		$this->addJS(_MODULE_DIR_.'marketplace/libs/rateit/lib/jquery.raty.min.js');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/shop_collection.css');

		// mp product slider
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/product_slider_pager/ps_gray.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/mp_product_slider.js');
	}

}
?>