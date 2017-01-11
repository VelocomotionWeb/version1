<?php
class MarketplaceAddProductModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		if (Tools::isSubmit('SubmitProduct'))
		{
			$id_customer = $this->context->cookie->id_customer;
			$obj_seller_product = new SellerProductDetail();
			$obj_mp_customer = new MarketplaceCustomer();
	        $obj_mpshop = new MarketplaceShop();
	        $obj_mpimage = new MarketplaceProductImage();
			
			$mp_shop = $obj_mpshop->getMarketPlaceShopInfoByCustomerId($id_customer);
	      	$mp_id_shop = $mp_shop['id'];
			
			$mp_customer = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
	        $id_seller = $mp_customer['marketplace_seller_id'];
	        
			//get data from add product form
			$product_name = Tools::getValue('product_name');
			$short_description = Tools::getValue('short_description');
			$product_description = Tools::getValue('product_description');
			$product_price = Tools::getValue('product_price');
			$product_quantity = Tools::getValue('product_quantity');
			$product_category = Tools::getValue('product_category');
			$product_condition = Tools::getValue('product_condition');

			if($product_name)
			{
				//Validate data
				if(trim($product_name) == '')
					$this->errors[] = Tools::displayError('Product name is required fields.');
				else
				{
					if (!Validate::isCatalogName($product_name))
						$this->errors[] = Tools::displayError('Product name must not have Invalid characters <>;=#{}');
				}
				
				if ($short_description)
				{
					$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
					if ($limit <= 0) $limit = 400;
					if (!Validate::isCleanHtml($short_description))
						$this->errors[] = Tools::displayError('Short description have not valid data.');
					if (Tools::strlen(strip_tags($short_description)) > $limit)
						$this->errors[] = Tools::displayError('This short description field is too long: '.$limit.' chars max.');
				} 
				
				if($product_description)
				{
					if (!Validate::isCleanHtml($product_description, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
						$this->errors[] = Tools::displayError('Product description have not valid data');
				}
				
				if ($product_price != '')
				{
					if (!Validate::isPrice($product_price))
						$this->errors[] = Tools::displayError('Product price should be numeric');
				}
				else
					$this->errors[] = Tools::displayError('Product price is required field.');
				
				if ($product_quantity != '')
				{
					if (!Validate::isUnsignedInt($product_quantity))
						$this->errors[] = Tools::displayError('Product quantity should be numeric');
				}
				else
					$this->errors[] = Tools::displayError('Product quantity is required field');
				
				if (!$product_category)
					$this->errors[] = Tools::displayError('You have not selected any category');

				//validate product main image
				if(isset($_FILES['product_image']))
					$this->validAddProductMainImage($_FILES['product_image']);							
				
				//validate product other images
				if (isset($_FILES['images']))
					$this->validAddProductOtherImage($_FILES['images']);
				
				Hook::exec('actionBeforeAddproduct', array('mp_seller_id' => $id_seller));
				
				if (!count($this->errors))
				{
					$obj_seller_product->id_seller = $id_seller;
					$obj_seller_product->price = $product_price;
					$obj_seller_product->quantity = $product_quantity;
					$obj_seller_product->product_name = $product_name;
					$obj_seller_product->description = $product_description;
					$obj_seller_product->short_description = $short_description;
					$obj_seller_product->id_category = $product_category[0];
					$obj_seller_product->ps_id_shop = $this->context->shop->id;
					$obj_seller_product->id_shop = $mp_id_shop;
					$obj_seller_product->condition = $product_condition;
					
					//control product approval setting
					if (Configuration::get('MP_PRODUCT_ADMIN_APPROVE'))
						$obj_seller_product->active = 0;
					else
						$obj_seller_product->active = 1;
					//---
				
					$obj_seller_product->save();
					$seller_product_id = $obj_seller_product->id;

					if ($seller_product_id)
					{
						//Add into category table
						$obj_seller_product_category = new SellerProductCategory();
						$obj_seller_product_category->id_seller_product = $seller_product_id;
						$obj_seller_product_category->is_default = 1;
					
						//set if more than one category selected
						$i = 0;
						foreach($product_category as $p_category)
						{
							$obj_seller_product_category->id_category = $p_category;
							if($i != 0)
								$obj_seller_product_category->is_default = 0;

							$obj_seller_product_category->add();
							$i++;
						}
						//---

						//upload product main image
						if(isset($_FILES['product_image']))
							$obj_mpimage->uploadProductMainImage($_FILES['product_image'], $seller_product_id);
							
						//upload product other images
						if (isset($_FILES['images']))
							$obj_mpimage->uploadProductOtherImage($_FILES['images']['tmp_name'], $seller_product_id);
						
						// if default approve on, then entry product details in ps_product table
						if (!Configuration::get('MP_PRODUCT_ADMIN_APPROVE'))
						{
							$obj_seller_product = new SellerProductDetail();
							$image_dir = _PS_MODULE_DIR_.'marketplace/views/img/product_img';
							// creating ps_product when admin setting is default
							$ps_product_id = $obj_seller_product->createPsProductByMarketplaceProduct($seller_product_id, $image_dir, 1);
							if($ps_product_id)
							{
								//mapping of ps_product and mp_product id
								$mps_product_obj = new MarketplaceShopProduct();
								$mps_product_obj->id_shop = $mp_id_shop;
								$mps_product_obj->marketplace_seller_id_product = $seller_product_id;
								$mps_product_obj->id_product = $ps_product_id;
								$mps_product_obj->add();
							}
						}
					}

					Hook::exec('actionAddproductExtrafield', array('marketplace_product_id' => $seller_product_id));

					// redirect to update product page
					$params = array('mp_success' => 1, 'id' => $seller_product_id, 'editproduct' => 1);
					Tools::redirect($this->context->link->getModuleLink('marketplace', 'productupdate', $params));
				}
			}
			else
				$this->errors[] = Tools::displayError('Product name is required fields.');
		}
	}

	public function initContent() 
	{
		parent::initContent();
		$id_lang = $this->context->language->id;
		$link = new Link();

		if (isset($this->context->customer->id))
		{
			$id_customer = $this->context->customer->id;
			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$obj_mpshop = new MarketplaceShop();
				$obj_mp_catg = new SellerProductCategory();

				//if error message by error number
				if ($mp_error = Tools::getValue('error'))
				{
					if ($mp_error == 9)
					{
						$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
						if ($limit <= 0) $limit = 400;
						$this->context->smarty->assign('max_sort_desc', $limit);
					}
					$this->context->smarty->assign('error', $mp_error);
				}

				$mp_shop_details  = $obj_mpshop->getMarketPlaceShopInfoByCustomerId($id_customer);
				if ($mp_shop_details)
				{
					$id_shop = $mp_shop_details['id'];
					$name_shop = $mp_shop_details['link_rewrite'];
					$this->context->smarty->assign('id_shop', $id_shop);
					$this->context->smarty->assign('name_shop', $name_shop);
				}

				$tree = $obj_mp_catg->getCategoryTree($id_lang);
				$this->context->smarty->assign('categoryTree', $tree);

				// show admin commission on product base price for seller
				if (Configuration::get('MP_SHOW_ADMIN_COMMISSION'))
					if ($admin_commission = $this->getCommissionBySeller($id_customer))
						$this->context->smarty->assign('admin_commission', $admin_commission);

				$this->context->smarty->assign('id_customer', $id_customer);
				$this->context->smarty->assign('logic', 'add_product');
				$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
				$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
				$this->setTemplate('addproduct.tpl');
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		} 
		else 
			Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('marketplace', 'addproduct')));	
	}

	public function validAddProductMainImage($image)
	{
		if ($image['size'] > 0)
		{			
			if ($image['tmp_name'] != "")
			{
				if(!ImageManager::isCorrectImageFileExt($image['name']))
				  	$this->errors[] = $_FILES['product_image']['name'].$this->module->l(' : Image format not recognized, allowed formats are: .gif, .jpg, .png');
			}
		}
		else
			return true;
	}

	public function validAddProductOtherImage($image)
	{
		//$link = new Link();
		if (empty($image['name']))
			return;

		//if any one is invalid extension redirect
		foreach ($image['name'] as $img_name)
		{
			if ($img_name != "")
			{
				if(!ImageManager::isCorrectImageFileExt($img_name))
					$this->errors[] = Tools::displayError('Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
			}
		}
	}

	public function getCommissionBySeller($id_customer)
	{
		$obj_mpcommission = new MarketplaceCommision();
		$obj_mpcommission->customer_id = $id_customer;
		if ($commission = $obj_mpcommission->getCommissionRateBySeller())
			return $commission;
		else
			return Configuration::get('MP_GLOBAL_COMMISSION');

		return false;
	}
	
	public function setMedia() 
	{
		parent::setMedia();
		$this->addCSS(array(
				_MODULE_DIR_.'marketplace/views/css/add_product.css',
				_MODULE_DIR_.'marketplace/views/css/marketplace_account.css'
			));
		
		//tinyMCE
		if (Configuration::get('PS_JS_THEME_CACHE') == 0)
			$this->addJS(array(
	                _MODULE_DIR_.'marketplace/views/js/tinymce/tinymce.min.js',
	                _MODULE_DIR_.'marketplace/views/js/tinymce/tinymce_wk_setup.js'
	            ));

		//for tiny mce field
        Media::addJsDef(array('iso' => $this->context->language->iso_code,
                            'mp_tinymce_path' => _MODULE_DIR_.'marketplace/libs'));

		$this->addJS(_MODULE_DIR_ .'marketplace/views/js/mp_form_validation.js');
		
		//Category tree
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/categorytree/jquery-ui-1.8.12.custom/js/jquery-ui-1.8.12.custom.min.js');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/js/categorytree/jquery-ui-1.8.12.custom/css/smoothness/jquery-ui-1.8.12.custom.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/categorytree/jquery.checkboxtree.js');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/js/categorytree/wk.checkboxtree.css');
	}
}

?>