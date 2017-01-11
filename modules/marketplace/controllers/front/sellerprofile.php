<?php
class MarketplaceSellerProfileModuleFrontController extends ModuleFrontController	
{
	public function initContent() 
	{
		parent::initContent();
		$id_customer = $this->context->cookie->id_customer;
		$id_lang = $this->context->cookie->id_lang;
		$link = new Link();

		$obj_mpshop = new MarketplaceShop();
		$obj_review = new Reviews();
		$obj_mpsuctomer = new MarketplaceCustomer();
		$obj_sellerinfo = new SellerInfoDetail();
		$obj_mpproduct = new SellerProductDetail();

		// review submit process
		if (Tools::isSubmit('submit_feedback'))
		{
			$seller_id = Tools::getValue('seller_id');
			$feedback = Tools::getValue('feedback');
			$rating = Tools::getValue('rating_image');
			$id_shop = Tools::getValue('shop');
			$obj_customer = new Customer($id_customer);
			$obj_mpshop = new MarketplaceShop($id_shop);
			$name_shop = $obj_mpshop->link_rewrite;

			//Save data in table
			$obj_review->id_seller = $seller_id;
			$obj_review->id_customer = $id_customer;
			$obj_review->customer_email = $obj_customer->email;
			$obj_review->rating = $rating;
			$obj_review->review = $feedback;
			if (Configuration::get('MP_REVIEWS_ADMIN_APPROVE'))
				$obj_review->active = 0;
			else
				$obj_review->active = 1;
			$obj_review->save();
			$review = $obj_review->id;

			if ($review)
			{
				if (Configuration::get('MP_REVIEWS_ADMIN_APPROVE'))
					Tools::redirect($link->getModuleLink('marketplace', 'sellerprofile',
												array('shop' => $id_shop, 'mp_shop_name' => $name_shop, 'review_submitted' => 1)));
				else
					Tools::redirect($link->getModuleLink('marketplace', 'sellerprofile',
												array('shop' => $id_shop, 'mp_shop_name' => $name_shop, 'review_submit_default' => 1)));
			}
		}

		// on review submit success
		if (Tools::getValue('review_submitted'))
			$this->context->smarty->assign('review_submitted', 1);

		if (Tools::getValue('review_submit_default'))
			$this->context->smarty->assign('review_submit_default', 1);


		$shop_link_rewrite = Tools::getValue('mp_shop_name');
		$id_shop = $obj_mpshop->getIdShopByName($shop_link_rewrite);

		if ($id_shop)
		{
			$mp_shop_details = $obj_mpshop->getMarketPlaceShopDetail($id_shop);
			if ($mp_shop_details)
			{
				$shop_name = $mp_shop_details['shop_name'];
				$id_customer = $mp_shop_details['id_customer'];
				$mp_customer_info = $obj_mpsuctomer->findMarketPlaceCustomer($id_customer);
				if ($mp_customer_info)
				{
					$is_seller_active = $mp_customer_info['is_seller'];
					$mp_seller_id = $mp_customer_info['marketplace_seller_id'];
					if ($is_seller_active == 1)
					{
						$mp_seller_info = $obj_sellerinfo->sellerDetail($mp_seller_id);
						if($mp_seller_info)
						{
							$ps_id_shop = $this->context->shop->id;
							$product_detail = $obj_mpproduct->getActiveMpProductWithImage($id_shop, $ps_id_shop, $id_lang);
							if ($product_detail)
								$this->context->smarty->assign('product_detail', $product_detail);
	
							$reviews = $this->getMostTwoReviewDetails($mp_seller_id);
							if ($reviews)
								$this->context->smarty->assign(array('reviews_info' => $reviews['reviews_info'],
																	 'avg_rating' => $reviews['avg_rating'],
																	 'reviews_details' => $reviews['reviews_details']));

		
							if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$mp_customer_info['marketplace_seller_id'].'.jpg'))
							{
								$seller_img_path = _MODULE_DIR_.'marketplace/views/img/seller_img/'.$mp_customer_info['marketplace_seller_id'].'.jpg';
								$this->context->smarty->assign('seller_img_path', $seller_img_path);
							}

							//get login user marketplace shop details if exist for seller can't review yourself
							if (isset($this->context->customer->id))
							{
								$login_customer_details = $obj_mpshop->getMarketPlaceShopInfoByCustomerId($this->context->customer->id);
								if ($login_customer_details)
									$this->context->smarty->assign('login_mp_shop_name', $login_customer_details['link_rewrite']);
								else
									$this->context->smarty->assign('login_mp_shop_name', ''); // required to set for js query
							}
							else
								$this->context->smarty->assign('login_mp_shop_name', '');
	
							$this->context->smarty->assign(array('mp_seller_info' => $mp_seller_info,
																 'id_customer' => $id_customer,
																 'shop_name' => $shop_name,
																 'name_shop_rewrite' => Tools::link_rewrite($shop_name),
																 'id_seller' => $mp_seller_id,
																 'id_shop' => $id_shop,
																 'title_text_color' => Configuration::get('MP_TITLE_TEXT_COLOR'),
																 'title_bg_color' => Configuration::get('MP_TITLE_BG_COLOR')
																 ));
							//assign the seller details view vars
                    		SellerInfoDetail::assignSellerDetailsView();
                    		$this->context->smarty->assign('MP_SHOW_SELLER_DETAILS', Configuration::get('MP_SHOW_SELLER_DETAILS'));
							$this->setTemplate('sellerprofile.tpl');
						}
						else
							Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
					}
					else
						Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));// seller is deactivated by admin
				}
				else
					Tools::redirect(__PS_BASE_URI__.'pagenotfound');
			}
			else 
				Tools::redirect(__PS_BASE_URI__.'pagenotfound');
		}
	}

	/**
	 * Get top two reviews to show on seller profile page
	 * @param  [int] $mp_seller_id marketplace seller id 
	 * @return [array or false]               [details in array]
	 */
	public function getMostTwoReviewDetails($mp_seller_id)
	{
		$obj_review = new Reviews();
		$reviews_info = $obj_review->getSellerReviewByIdSeller($mp_seller_id);
		if ($reviews_info)
		{
			$reviews_details = array();
			$i = 0;
			$rating = 0;
			foreach($reviews_info as $reviews)
			{
				if ($i < 2)
				{
					$obj_customer = new Customer($reviews['id_customer']);
					$reviews_details[$i]['customer_name'] = $obj_customer->firstname." ".$obj_customer->lastname;
					$reviews_details[$i]['customer_email'] = $obj_customer->email;
					$reviews_details[$i]['rating'] = $reviews['rating'];
					$reviews_details[$i]['review'] = $reviews['review'];
					$reviews_details[$i]['time'] = $reviews['date_add'];
				}
				$rating = $rating + $reviews['rating'];
				$i++;
			}
			$avg_rating = (double)($rating/$i);
			return array('reviews_info' => $reviews_info,
						 'avg_rating' => $avg_rating,
						 'reviews_details' => $reviews_details);
		}
		else
			return false;
	}

	public function setMedia() 
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/store_profile.css');
		$this->addJS(_MODULE_DIR_.'marketplace/libs/rateit/lib/jquery.raty.min.js');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/seller_review.js');

		// mp product slider
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/product_slider_pager/ps_gray.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/mp_product_slider.js');
	}
}
?>