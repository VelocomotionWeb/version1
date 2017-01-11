<?php
class MarketplaceSellerPricesModuleFrontController extends ModuleFrontController 
{
	public function initContent() 
	{
		parent::initContent();
		$link = new Link();
		$obj_mp_customer = new MarketplaceCustomer();
		$obj_mp_seller = new SellerInfoDetail();
		$obj_mp_shop = new MarketplaceShop();

		if ($this->context->customer->isLogged())
		{
			$id_customer = $this->context->cookie->id_customer;
			$smarty_var = array();
			$mp_customer = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
			if ($mp_customer)
			{
				if ($mp_customer['is_seller'] == 1)
				{
					$smarty_var['logic'] = 2;
					$mp_seller_id   = $mp_customer['marketplace_seller_id'];
					$mp_seller_info = $obj_mp_seller->sellerDetail($mp_seller_id);
					$mp_shop = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
					
					//If seller image error
					if ($seller_img_error = Tools::getValue('seller_img_error'))
						$smarty_var['seller_img_error'] = $seller_img_error;

					//If seller shop error
					if ($shop_img_error = Tools::getValue('shop_img_error'))
						$smarty_var['shop_img_error'] = $shop_img_error;

					$mp_error = Tools::getValue('mp_error');
					if ($mp_error)
                 		$smarty_var['mp_error'] = $mp_error;

					if (Tools::getValue('updated'))
						$smarty_var['updated'] = 1;

					//delete seller images
					if ($target = Tools::getvalue('delete_img'))
						if($id_seller = Tools::getValue('id_seller'))
							$this->deleteSellerImages($id_seller, $target);

			        //Check if shop image exist
			        if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$mp_seller_id.'-'.$mp_seller_info['shop_name'].'.jpg'))
			        {
			        	$shop_img_path = _MODULE_DIR_.'marketplace/views/img/shop_img/'.$mp_seller_id.'-'.$mp_seller_info['shop_name'].'.jpg';
			        	$smarty_var['shop_img_path'] = $shop_img_path;
			        }

			        //Check if seller image exist
			        if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$mp_seller_id.'.jpg'))
			        {
			        	$seller_img_path = _MODULE_DIR_.'marketplace/views/img/seller_img/'.$mp_seller_id.'.jpg';
			        	$smarty_var['seller_img_path'] = $seller_img_path;
			        }

			        $smarty_var['marketplace_address'] = trim($mp_seller_info['address']);
			        $smarty_var['mp_seller_info'] = $mp_seller_info;
			        $smarty_var['market_place_shop'] = $mp_shop;
			        $smarty_var['max_phone_digit'] = Configuration::get('MP_PHONE_DIGIT');
			        $smarty_var['title_text_color'] = Configuration::get('MP_TITLE_TEXT_COLOR');
			        $smarty_var['title_bg_color'] = Configuration::get('MP_TITLE_BG_COLOR');

			        $smarty_var['is_seller'] = $mp_customer['is_seller'];
			        $smarty_var['seller_default_img_path'] = _MODULE_DIR_.'marketplace/views/img/seller_img/defaultimage.jpg';
			        $smarty_var['shop_default_img_path'] = _MODULE_DIR_.'marketplace/views/img/shop_img/defaultshopimage.jpg';
					$this->context->smarty->assign($smarty_var);
					/*
					// Recup de la liste des produits
					$obj_mp_product = new SellerProductDetail();
					$obj_mp_shop = new MarketplaceShop();
					
					$shop_link_rewrite = Tools::getValue('mp_shop_name');
					$id_shop = $obj_mp_shop->getIdShopByName($shop_link_rewrite); //if direct from menu
					$id_seller = MarketplaceShop::findMpSellerIdByShopId($id_shop);
					$store = StoreLocator::getSellerStore($id_seller);
					$id_store = $store[0]['id'];
					$all_active_products = SellerProductDetail::getMpSellerProductDetails($id_seller, true);
					echo "ishop=$id_shop/isel=$id_seller/s=$store/istore=$id_store//";
					
					$all_active_products = $obj_mp_product->findAllActiveProductInMarketPlaceShop($id_shop, 0, 99);
					var_dump($all_active_products);
					
					$this->context->smarty->assign('products', $all_active_products);
					*/
					
					$id_lang = $this->context->cookie->id_lang;
					

					$obj_mp_product = new SellerProductDetail();
					$obj_mp_shop = new MarketplaceShop();
			
					$shop_link_rewrite = Tools::getValue('mp_shop_name');
			
			
					if (Tools::getValue('shop') && $id_category) //if from category
						$id_shop = Tools::getValue('shop');
					else
						$id_shop = $obj_mp_shop->getIdShopByName($shop_link_rewrite); //if direct from menu
			
					if ($id_shop)
					{
						$shop = @MarketplaceShop::getMarketPlaceShopDetail($id_shop);
						$name_shop = $shop["shop_name"];
			
						//default orderby and orderway
						if (!@$orderby)
							$orderby = 'id';
						elseif (@$orderby == 'name')
							$orderby = 'product_name';
			
						if (!@$orderway)
							$orderway = 'desc';
			
						// for creating pagination
						$id_seller = MarketplaceShop::findMpSellerIdByShopId($id_shop);
						$store = StoreLocator::getSellerStore($id_seller);
						$id_store = $store[0]['id'];
						$all_active_products = SellerProductDetail::getMpSellerProductDetails($id_seller, true);
						if ($all_active_products)
							$this->pagination(count($all_active_products));
			
						//get all marketplace product
						$id_category = false;
						$this->n = 99; $this->p = 1;
						if ($id_category)
							$mp_shop_product = $this->getMpProductByCategory($id_category, $id_shop, $this->p, $this->n, $orderby, $orderway);
						else
							$mp_shop_product = $obj_mp_product->findAllActiveProductInMarketPlaceShop($id_shop, $this->p, $this->n, $orderby, $orderway);
			
//echo "n=".$this->n."p=".$this->p." $id_seller/$id_customer  is=". $mp_customer['is_seller'] ;
//var_dump($mp_shop_product);
						if ($mp_shop_product)
						{
							//get category details
							$mp_product = $obj_mp_product->findAllActiveProductInMarketPlaceShop($id_shop, $this->p, $this->n, $orderby, $orderway);
			
							//Product array by category or by default
						}
						$this->context->smarty->assign('products', $mp_shop_product);
						
						$colonnes = array
						  (
						  array(2, "2 jours","2 days"),
						  array(3, "3 jours","2 days"),
						  array(4, "4 jours","2 days"),
						  array(5, "5 jours","2 days"),
						  array(6, "6 jours","2 days"),
						  array(7, "1 semaine","2 days"),
						  array(14,"2 semaines","2 days"),
						  array(31,"1 mois", "1 month")
						  );  
						$this->context->smarty->assign('colonnes', $colonnes);
						
						// recupere la liste des taxes
						$sql = "SELECT * FROM "._DB_PREFIX_."tax_lang tl INNER JOIN "._DB_PREFIX_."tax t ON tl.id_tax = t.id_tax
								WHERE id_lang = $id_lang";
						$taxs = Db::getInstance()->ExecuteS($sql);
						$this->context->smarty->assign('id_lang', $id_lang);
						   
						$this->context->smarty->assign('taxs', $taxs);
						
					}
					
		    		$this->setTemplate('sellerprices.tpl');
	    		}
	    		else
	    			Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
					
    		}
    		else
    			$this->redirectMyAccount();
    	}
    	else
    		$this->redirectMyAccount();
	}

	public function postProcess()
	{
		if (Tools::isSubmit('updateProfile'))
		{
			$link = new Link();
			if (isset($this->context->cookie->id_customer)) 
	        {
	            $id_customer = $this->context->cookie->id_customer;
				
	            if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
	            {
	                $obj_mp_customer = new MarketplaceCustomer();
	                $obj_mp_shop = new MarketplaceShop();

	                $mp_seller_info = $obj_mp_customer->findMarketPlaceCustomer($id_customer);

	                $mp_shop = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
	                $id_shop = $mp_shop['id'];
	                
	                $mp_seller_id = $mp_seller_info['marketplace_seller_id'];

	                $seller_name = Tools::getValue('update_seller_name');
	                $shop_name = Tools::getValue('update_shop_name');
	                $business_email = Tools::getValue('update_business_email');
	                $phone = Tools::getValue('update_phone');
	                $fax = Tools::getValue('update_fax');
	                $address = Tools::getValue('update_address');
	                $about_us = trim(Tools::getValue('update_about_shop'));
	                $twitter_id = trim(Tools::getValue('update_twitter_id'));
	                $facebook_id = trim(Tools::getValue('update_facbook_id'));

	                if ($shop_name == '')
		                $this->errors[] = Tools::displayError('Shop name is required field.');
	                else if (!Validate::isCatalogName($shop_name))
		                $this->errors[] = Tools::displayError('Shop name is invalid');
	                else if (SellerInfoDetail::isShopNameExist($shop_name, $mp_seller_id))
		                $this->errors[] = Tools::displayError('Shop name is already taken. Try another.');

	                if ($seller_name == '')
		                $this->errors[] = Tools::displayError('Seller name is required field.');
	                else if (!Validate::isName($seller_name))
		                $this->errors[] = Tools::displayError('Invalid seller name.');

	                if ($phone == '')
		                $this->errors[] = Tools::displayError('Phone is required field.');
	                else if (!Validate::isPhoneNumber($phone))
		                $this->errors[] = Tools::displayError('Invalid phone number.');

	                if ($business_email == '')
		                $this->errors[] = Tools::displayError('Email ID is requird field.');
	                elseif (!Validate::isEmail($business_email))
		                $this->errors[] = Tools::displayError('Invalid Email ID.');
	                elseif (SellerInfoDetail::isSellerEmailExist($business_email, $mp_seller_id))
		                $this->errors[] = Tools::displayError('Email ID already exist.');


	                $mp_seller_info = $obj_mp_customer->getCustomeInfoByID($mp_seller_id);
	                $mp_shop_name   = $mp_seller_info['shop_name'];

	                //validate seller logo
					if(isset($_FILES['update_seller_logo']))
	                	$this->validateShopSellerLogoSize($_FILES['update_seller_logo']);

	                //validate seller logo
					if(isset($_FILES['update_shop_logo']))
	                	$this->validateShopSellerLogoSize($_FILES['update_shop_logo']);

		            Hook::exec('actionBeforeUpdateSeller');
		            
		            if (!count($this->errors))
		            {
		            	//upload seller logo
						if(isset($_FILES['update_seller_logo']))
	                		$this->uploadSellerLogo($_FILES['update_seller_logo'], $mp_seller_id);

		                //upload seller logo
						if(isset($_FILES['update_shop_logo']))
		                	$this->uploadShopLogo($_FILES['update_shop_logo'], $mp_shop_name, $shop_name, $mp_seller_id);

		                //update seller details
		                $obj_seller = new SellerInfoDetail($mp_seller_id);
		                $obj_seller->business_email = $business_email;
		                $obj_seller->seller_name = $seller_name;
		                $obj_seller->shop_name = $shop_name;
		                $obj_seller->phone = $phone;
		                $obj_seller->fax = $fax;
		                $obj_seller->address = $address;
		                $obj_seller->facebook_id = $facebook_id;
		                $obj_seller->twitter_id = $twitter_id;
		                $obj_seller->save();

		                //Add in marketplace_shop
		                $obj_shop = new MarketplaceShop($id_shop);
		                $shop_rewrite = Tools::link_rewrite($shop_name);
		                $obj_shop->shop_name = $shop_name;
		                $obj_shop->link_rewrite = $shop_rewrite;
		                $obj_shop->about_us = $about_us;
		                $obj_shop->save();

		                Hook::exec('actionUpdateshopExtrafield', array('marketplace_seller_id' => $mp_seller_id));
		                $params = array('shop' => $id_shop, 'updated' => 1);
		                $redirect_link = $link->getModuleLink('marketplace', 'editprofile', $params);
		                Tools::redirect($redirect_link);
	            	}
	            }
	            else
	                Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
	        }
	        else
	            Tools::redirect($link->getPageLink('my-account'));
		}
	}

	public function deleteSellerImages($id_seller, $target)
	{
		if ($target == 'seller_img')
		{
			$seller_img_path = _PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_seller.'.jpg';
			if (file_exists($seller_img_path))
			{
	        	if(unlink($seller_img_path))
	        		$msg = array('status' => 'ok', 'msg' => 'Seller image deleted successfully.');
	        	else
	        		$msg = array('status' => 'ko', 'msg' => 'Unexpected error while deleting image.');
			}
			else
				$msg = array('status' => 'ko', 'msg' => 'This image does not exist anymore.');
		}

		if ($target == 'shop_img')
		{
			$obj_mp_seller = new SellerInfoDetail();
			$mp_seller_info = $obj_mp_seller->sellerDetail($id_seller);
			$shop_img_path = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$id_seller.'-'.$mp_seller_info['shop_name'].'.jpg';
			if (file_exists($shop_img_path))
			{
	        	if(unlink($shop_img_path))
	        		$msg = array('status' => 'ok', 'msg' => 'Shop image deleted successfully.');
	        	else
	        		$msg = array('status' => 'ko', 'msg' => 'Unexpected error while deleting image.');
			}
			else
				$msg = array('status' => 'ko', 'msg' => 'This image does not exist anymore.');
		}

		if(isset($msg))
			die(Tools::jsonEncode($msg));
		else
			die(Tools::jsonEncode(array('status' => 'ko', 'msg' => 'Something wrong while deleting image.')));
	}

	public function validateShopSellerLogoSize($upload_logo)
    {
        if (!empty($upload_logo['name']))
        {
            if (!ImageManager::isCorrectImageFileExt($upload_logo['name']))
            {
                $this->errors[] = Tools::displayError('<strong>'.$upload_logo['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
                return false;
            }
            else 
            {
                list($width, $height) = getimagesize($upload_logo['tmp_name']);
                if ($width == 0 || $height == 0)
                {
                    $this->errors[] = Tools::displayError('Invalid image size. Minimum image size must be 200X200.');
                    return false;
                }
                else if ($width < 200 || $height < 200)
                {
                    $this->errors[] = Tools::displayError('Invalid image size. Minimum image size must be 200X200.');
                    return false;
                }
                else
                    return true;
            } 
        }
        else
            return true;
    }

	public function uploadSellerLogo($seller_logo, $mp_seller_id)
    {
        if ($seller_logo['size'] > 0)
        {            
            $newpath = _PS_MODULE_DIR_.'marketplace/views/img/seller_img/';
			$width = '200';
			$height = '200';
			ImageManager::resize($seller_logo['tmp_name'], $newpath.$mp_seller_id.'.jpg', $width, $height);
			return true;
            
        }
        else
            return false;
    }

    public function uploadShopLogo($shop_logo, $mp_shop_name, $shop_name, $mp_seller_id)
    {    	
		//if shop name update and shop logo image is not uploaded
        if ($shop_logo['size'] == 0) 
        {
            if ($mp_shop_name != $shop_name) 
            {
                $shop_prev_logo_name = $mp_seller_id."-".$mp_shop_name;
                $shop_prev_logo_name1 = glob('modules/marketplace/views/img/shop_img/'.$shop_prev_logo_name.'.*');
                $is_shop_image_exist = $shop_prev_logo_name1[0];
                if (file_exists($is_shop_image_exist)) 
                {
                    $shop_new_logo_name = $mp_seller_id."-".$shop_name.".jpg";
					$shop_image_path = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/';
                    rename($shop_image_path.$shop_prev_logo_name.'.jpg', $shop_image_path.$shop_new_logo_name);
                }
            }
        } 
        else 
        {            
            if ($shop_logo['error'] == 0)
            {
            	//Delete previous
	            $shop_image_path = 'modules/marketplace/views/img/shop_img/';
	            $shop_prev_logo_name = $mp_seller_id."-".$mp_shop_name;
	            $shop_prev_logo_name1 = glob($shop_image_path . $shop_prev_logo_name.'.*');
	            $is_shop_image_exist  = $shop_prev_logo_name1[0];
	            if (file_exists($is_shop_image_exist))
	                unlink($shop_prev_logo_name1[0]);

            	$newpath = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/';
                $width = '200';
                $height = '200';
                ImageManager::resize($shop_logo['tmp_name'], $newpath.$mp_seller_id.'-'.$shop_name.'.jpg', $width, $height);
                return true;               
            }
        }
    }

	public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
        $this->addJS(_PS_JS_DIR_.'validate.js');
        $this->addJS(_MODULE_DIR_.'marketplace/views/js/mp_form_validation.js');

        //tinyMCE
		/*
		if (Configuration::get('PS_JS_THEME_CACHE') == 0)
			$this->addJS(array(
	                _MODULE_DIR_.'marketplace/views/js/tinymce/tinymce.min.js',
	                _MODULE_DIR_.'marketplace/views/js/tinymce/tinymce_wk_setup.js'
	            ));
		*/
		//for tiny mce lang
		Media::addJsDef(array('iso' => $this->context->language->iso_code));
    }

    public function redirectMyAccount()
    {
    	$link = new Link();
    	Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('marketplace', 'sellerprices')));
    }
}
?>