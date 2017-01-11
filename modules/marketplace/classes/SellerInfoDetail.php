<?php
class SellerInfoDetail extends ObjectModel
{
	public $id;
	public $business_email;
	public $seller_name;
	public $shop_name;
	public $phone;
	public $fax;
	public $address;
	public $about_shop;
	public $facebook_id;
	public $twitter_id;
	public $active;
	public $date_add;
	public $date_upd;
	
	public static $definition = array(
		'table' => 'marketplace_seller_info',
		'primary' => 'id',
		'fields' => array(
			'business_email' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail'),
			'seller_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'shop_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'phone' =>  array('type' => self::TYPE_STRING, 'required' => true, 'validate' => 'isPhoneNumber', 'size' => 32),
			'fax' => 		array('type' => self::TYPE_STRING),
			'address' => array('type' => self::TYPE_STRING),
			'about_shop' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'facebook_id' => array('type' => self::TYPE_STRING),
			'twitter_id' => array('type' => self::TYPE_STRING),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
		),
	);

	public function delete()
	{
		if (!$this->mpSellerDelete($this->id) || !parent::delete())
			return false;

		return true;
	}

	public function mpSellerDelete($id_seller)
	{
		Hook::exec('actionMpSellerDelete', array('id_seller' => $id_seller));
		// delete from mp customer
		$obj_mpcustomer = new MarketplaceCustomer();
		$id_customer = $obj_mpcustomer->getCustomerId($id_seller);
		$active_customer = true;
		if ($id_customer)
		{
			$delete_mpshop = Db::getInstance()->delete('marketplace_shop', 'id_customer = '.$id_customer);
			$delete_payment = Db::getInstance()->delete('marketplace_customer_payment_detail', 'id_customer = '.$id_customer);
			$delete_commission = Db::getInstance()->delete('marketplace_commision', 'customer_id = '.$id_customer);
			//$delete_order = Db::getInstance()->delete('marketplace_commision_calc', 'customer_id = '.$id_customer);
			if (!$delete_mpshop
				|| !$delete_payment
				|| !$delete_commission
				//|| !$delete_order
				)
				$active_customer = false;
		}

		$delete_mpcustomer = Db::getInstance()->delete('marketplace_customer', 'marketplace_seller_id = '.$id_seller);

		// delete mp products
		$product_delete = true;
		$mp_products = SellerProductDetail::getMpSellerProductDetails($id_seller);
		if ($mp_products)
			foreach ($mp_products as $product)
			{
				$obj_mpproduct = new SellerProductDetail($product['id']);
				if (!$obj_mpproduct->delete())
					$product_delete = false;
			}

		// deleting reviews
		$delete_review = Db::getInstance()->delete('marketplace_seller_reviews', 'id_seller = '.$id_seller);

		// delete seller image
		$this->deleteMpSellerImage($id_seller);

		// delete seller shop image
		$this->deleteMpShopImage($id_seller);

		if (!$active_customer
			|| !$delete_mpcustomer
			|| !$product_delete
			|| !$delete_review)
			return false;
		return true;
	}

	public function sellerDetail($seller_id)
	{
		$seller_info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_seller_info`
																	WHERE `id` = ".(int)$seller_id);

		if ($seller_info)
			return $seller_info;

		return false;
	}

	public static function getSellerImageLink($id_mp_seller)
	{
		if (!$id_mp_seller)
			return false;

		if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg'))
			return _MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg';
		else
			return _MODULE_DIR_.'marketplace/views/img/seller_img/defaultimage.jpg';
	}

	public static function getShopImageLink($id_mp_seller)
	{
		if (!$id_mp_seller)
			return false;

		$obj_mpseller = new SellerInfoDetail($id_mp_seller);
		$shopimage = $id_mp_seller."-".$obj_mpseller->shop_name.'.jpg';
		if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage))
			return _MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage;
		else
			return _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
	}

	public function deleteMpSellerImage($id_mp_seller)
	{
		if (!$id_mp_seller)
			return false;

		if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg'))
			unlink(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_mp_seller.'.jpg');
		else
			return false;

		return true;
	}

	public function deleteMpShopImage($id_mp_seller)
	{
		if (!$id_mp_seller)
			return false;

		$obj_mpseller = new SellerInfoDetail($id_mp_seller);
		$shopimage = $id_mp_seller.'-'.$obj_mpseller->shop_name.'.jpg';
		if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage))
			unlink(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$shopimage);
		else
			return false;

		return true;
	}

	public function getMarketPlaceSellerIdByCustomerId($id_customer)
	{
		$isseller = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_customer`
										WHERE id_customer =".$id_customer);
		if (!empty($isseller))
			return $isseller;

		return false;
	}
	
	public static function isShopNameExist($name, $id_seller = false)
	{
		$shop_name = pSQL($name);
		$mp_id_seller = Db::getInstance()->getValue("SELECT `id` FROM `"._DB_PREFIX_."marketplace_seller_info`
												WHERE shop_name ='$shop_name'");

		if ($id_seller)
		{
			if ($mp_id_seller)
			{
				if ($mp_id_seller == $id_seller)
					return false;
				return true;
			}
		}
		else
		{
			if ($mp_id_seller)
				return true;
		}
		return false;	
	}

	public static function isSellerEmailExist($seller_email, $id_seller = false)
	{
		$seller_email = pSQL($seller_email);
		$mp_id_seller = Db::getInstance()->getValue("SELECT `id` FROM `"._DB_PREFIX_."marketplace_seller_info`
												WHERE business_email ='$seller_email'");

		if ($id_seller)
		{
			if ($mp_id_seller)
			{
				if ($mp_id_seller == $id_seller)
					return false;
				return true;
			}
		}
		else
		{
			if ($mp_id_seller)
				return true;
		}
		return false;	
	}
		
	public function getmarketPlaceSellerInfo($marketplace_sellerid)
	{
		$mp_seller_info = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `". _DB_PREFIX_."marketplace_seller_info`
											WHERE id = ".$marketplace_sellerid);
		
		if(!empty($mp_seller_info))
			return $mp_seller_info;

		return false;
	}

	public function findAllActiveSeller()
	{
		$seller_info = Db::getInstance()->executeS("SELECT mpsi.* FROM `"._DB_PREFIX_."marketplace_seller_info` mpsi
								LEFT JOIN `"._DB_PREFIX_."marketplace_customer` mpc
								ON (mpsi.`id` = mpc.`marketplace_seller_id`) WHERE mpc.`is_seller` = 1");
		if (empty($seller_info))
			return false;

		return $seller_info;
	}
		
	public function findAllActiveSellerInfoByLimit($start_point = 0, $limit_point = 7, $like = false, $all = false, $like_word = 'a')
	{
		if ($like == false && $all == false)
			$seller_info = Db::getInstance()->executeS("SELECT mpsi.*,mpc.`id_customer`,ms.`id` as mp_shop_id
				FROM `". _DB_PREFIX_."marketplace_seller_info` mpsi
				LEFT JOIN `". _DB_PREFIX_."marketplace_customer` mpc ON (mpsi.`id` = mpc.`marketplace_seller_id`)
				LEFT JOIN `". _DB_PREFIX_."marketplace_shop` ms ON (ms.`id_customer` = mpc.`id_customer`)
				where is_seller = 1 LIMIT ".$start_point.",".$limit_point);
		else if ($like == false && $all == true)
			$seller_info = Db::getInstance()->executeS("SELECT mpsi.*,mpc.`id_customer`,ms.`id` as mp_shop_id
				FROM `". _DB_PREFIX_."marketplace_seller_info` mpsi
				LEFT JOIN `". _DB_PREFIX_."marketplace_customer` mpc ON (mpsi.`id` = mpc.`marketplace_seller_id`)
				LEFT JOIN `". _DB_PREFIX_."marketplace_shop` ms ON (ms.`id_customer` = mpc.`id_customer`)
				where is_seller = 1");
		else if ($like == true && $all == false)//no limit
			$seller_info = Db::getInstance()->executeS("SELECT mpsi.*,mpc.`id_customer`,ms.`id` as mp_shop_id
				FROM `". _DB_PREFIX_."marketplace_seller_info` mpsi
				LEFT JOIN `". _DB_PREFIX_."marketplace_customer` mpc ON (mpsi.`id` = mpc.`marketplace_seller_id`)
				LEFT JOIN `". _DB_PREFIX_."marketplace_shop` ms ON (ms.`id_customer` = mpc.`id_customer`)
				where is_seller = 1 AND LOWER( ms.`shop_name`) LIKE '".$like_word."%'");
		else if ($like == true && $all == true)
			$seller_info = Db::getInstance()->executeS("SELECT mpsi.*,mpc.`id_customer`,ms.`id` as mp_shop_id
				FROM `". _DB_PREFIX_."marketplace_seller_info` mpsi
				LEFT JOIN `". _DB_PREFIX_."marketplace_customer` mpc ON (mpsi.`id` = mpc.`marketplace_seller_id`)
				LEFT JOIN `". _DB_PREFIX_."marketplace_shop` ms ON (ms.`id_customer` = mpc.`id_customer`)
				where is_seller = 1 AND LOWER( ms.`shop_name`) LIKE '%".$like_word."%'");

		if (empty($seller_info))
			return false;

		return $seller_info;
	}
	
	public function callMailFunction($mp_id_seller, $subject, $mail_for = false) 
	{
		$id_lang = Configuration::get('PS_LANG_DEFAULT');

		if ($mail_for == 1)
			$mail_reason = 'activated';
		else if ($mail_for == 2)
			$mail_reason = 'deactivated';
		else if ($mail_for == 3)
			$mail_reason = 'deleted';
		else
			$mail_reason = 'activated';
		
		$obj_seller = new SellerInfoDetail($mp_id_seller);
		$obj_mp_customer = new MarketplaceCustomer();
		$mp_seller_name = $obj_seller->seller_name;
		$business_email = $obj_seller->business_email;
		$mp_shop_name = $obj_seller->shop_name;
		$phone = $obj_seller->phone;
		if ($business_email == '')
		{
			$id_customer = $obj_mp_customer->getCustomerId($mp_id_seller);
			$obj_cus = new Customer($id_customer);
			$business_email = $obj_cus->email;
		}
				
		$templateVars = array(
						'{seller_name}' => $mp_seller_name,
						'{mp_shop_name}' => $mp_shop_name,
						'{mail_reason}' => $mail_reason,
						'{business_email}' => $business_email,
						'{phone}' => $phone
					);
		
		$temp_path = _PS_MODULE_DIR_.'marketplace/mails/';

		if($subject == 1) //Seller Request Approved
		{
			Mail::Send($id_lang,
				'seller_active',
				Mail::l('Seller Request Approved', $id_lang),
				$templateVars,
				$business_email,
				$mp_seller_name,
				null,
				'Marketplace',
				null,
				null,
				$temp_path,
				false,
				null,
				null);
		}
		else if($subject == 2) //Seller Request Disapproved
		{
			Mail::Send($id_lang,
				'seller_active',
				Mail::l('Seller Request Disapproved', $id_lang),
				$templateVars,
				$business_email,
				$mp_seller_name,
				null,
				'Marketplace',
				null,
				null,
				$temp_path,
				false,
				null,
				null);
		}

		return true;
	}

	/**
	 * [calling if default seller active on]
	 * @param  [int] $id [mp seller id]
	 * @return [no]     [no]
	 */
	public function makeDefaultSellerPartner($id_seller)
	{
		$obj_mpcustomer = new MarketplaceCustomer();
		$obj_mpshop = new MarketplaceShop();

		if ($id_seller)
		{
			// active marketplace_customer
			$obj_mpcustomer->changeMpSellerStatus($id_seller, 1);
			$id_customer = $obj_mpcustomer->getCustomerId($id_seller);

			// active marketplace_shop
			$obj_mpseller = new SellerInfoDetail($id_seller);
			$shop_name = $obj_mpseller->shop_name;
			$shop_rewrite = Tools::link_rewrite($shop_name);
			$obj_mpshop->shop_name = pSQL($shop_name);
			$obj_mpshop->link_rewrite = pSQL($shop_rewrite);
			$obj_mpshop->id_customer = (int)$id_customer;
			$obj_mpshop->about_us = pSQL(trim($obj_mpseller->about_shop));
			$obj_mpshop->is_active = 1;
			$mp_id_shop = $obj_mpshop->save();
			if ($mp_id_shop) // mail to seller of account activation
				$this->callMailFunction($id_seller, 1, 1);
		}
	}

	/**
	 * Fetch the content of $template_name inside the folder marketplace/mails/current_iso_lang/ if found
	 *
	 * @param string  $template_name template name with extension
	 * @param integer $mail_type     Mail::TYPE_HTML or Mail::TYPE_TXT
	 * @param array   $var           list send to smarty
	 *
	 * @return string
	 */
	public function getMpEmailTemplateContent($template_name, $mail_type, $var)
	{
		$email_configuration = Configuration::get('PS_MAIL_TYPE');
		if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH)
			return '';

		$default_mail_template_path = _PS_MODULE_DIR_.'marketplace/mails/'.DIRECTORY_SEPARATOR.Context::getContext()->language->iso_code.DIRECTORY_SEPARATOR.$template_name;

		if (Tools::file_exists_cache($default_mail_template_path))
		{
			Context::getContext()->smarty->assign('list', $var);
			return Context::getContext()->smarty->fetch($default_mail_template_path);
		}

		return '';
	}

	public static function assignSellerDetailsView()
	{
		$obj_mp = new Marketplace();
		foreach ($obj_mp->seller_details_view as $seller_view)
        {
            Context::getContext()->smarty->assign('MP_SELLER_DETAILS_ACCESS_'.$seller_view['id_group'],
                                Configuration::get('MP_SELLER_DETAILS_ACCESS_'.$seller_view['id_group']));
        }
	}
	public function getSellerInfoByProduct($id_search,$tp="product")
	{
		//return 42;
		if($tp=="cart"){
			
			$products = $id_search->getProducts(true);
			$id_product = $products[0]['id_product'];
		}
		if($tp=="order"){
			
			$order = new Order($id_search);
			$products = $order->getProducts(false);
			foreach($products as $product) $id_product  = $product["product_id"];
			
		}
		if($tp=="product") $id_product = $id_search;
		
		$obj_mp_product = new SellerProductDetail();
		$obj_mp_shop = new MarketplaceShop();
		$obj_mp_seller = new SellerInfoDetail();
		
		$seller_shop_detail = $obj_mp_product->getMarketPlaceShopProductDetail($id_product);
		$mp_id_shop = $seller_shop_detail['id_shop']; 
		$obj_ps_shop = new MarketplaceShop($mp_id_shop);
		$mp_customer = $obj_mp_seller->getMarketPlaceSellerIdByCustomerId($obj_ps_shop->id_customer);
		$seller_id = $mp_customer['marketplace_seller_id'];
		$mp_seller_info = $obj_mp_seller->getmarketPlaceSellerInfo((int)$seller_id);
		
		return $mp_seller_info;
		
	}
}