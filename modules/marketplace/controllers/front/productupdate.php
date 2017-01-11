<?php
class MarketplaceProductUpdateModuleFrontController extends ModuleFrontController	
{
	public function initContent() 
	{
		parent::initContent();

		$id_lang = $this->context->cookie->id_lang;

		$id_product = Tools::getValue('id');
		$delete_product = Tools::getValue('deleteproduct');
		$edited = Tools::getValue('edited');

		$obj_mp_product = new SellerProductDetail();
		$obj_mp_shop_product = new MarketplaceShopProduct();
		$obj_mp_catg = new SellerProductCategory();
		$obj_mpimage = new MarketplaceProductImage();

		$link = new Link();
		if ($this->context->cookie->id_customer)
		{
			$id_customer = $this->context->cookie->id_customer;
			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$product_info = $obj_mp_product->getMarketPlaceProductInfo($id_product);

				$checked_product_cat = $obj_mp_product->getMarketPlaceProductCategories($id_product);

				//if product uploaded successfully
				if($mp_success = Tools::getValue('mp_success'))
					$this->context->smarty->assign('product_upload', $mp_success);
				
				$defaultcatid = $obj_mp_catg->getMpDefaultCategory($id_product);

				if ($delete_product) // if seller delete product
				{
					$obj_new_mp_seller_product = new SellerProductDetail($id_product);
					Hook::exec('actionBeforeMpProductDelete', array('marketplace_product_id' => $id_product));
					$is_delete = $obj_new_mp_seller_product->delete();

					if($is_delete)
						Tools::redirect($link->getModuleLink('marketplace','productlist', array('deleted' => 1)));
				}
				else if ($edited) // if seller updated the product, update process
				{
					$id = Tools::getValue('id');
					$product_name = Tools::getValue('product_name');
					$short_description = Tools::getValue('short_description');
					$product_description = Tools::getValue('product_description');
					$product_price = Tools::getValue('product_price');
					$product_quantity = Tools::getValue('product_quantity');
					$product_category = Tools::getValue('product_category');
					$product_condition = Tools::getValue('product_condition');

					if ($product_name)
					{
						//Validate data
						if(trim($product_name) == '')
							$this->errors[] = Tools::displayError('Product name is required field.');
						else
						{
							if (!Validate::isGenericName($product_name))
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
							if (!Validate::isCleanHtml($product_description))
								$this->errors[] = Tools::displayError('Product description have not valid data');
						}

						if ($product_price != '')
						{
							if (!Validate::isPrice($product_price))
								$this->errors[] = Tools::displayError('product price should be numeric');
						}
						else
							$this->errors[] = Tools::displayError('Product price is required field.');
						
						if ($product_quantity != '')
						{
							if (!Validate::isUnsignedInt($product_quantity))
								$this->errors[] = Tools::displayError('product quantity should be numeric');
						}
						else
							$this->errors[] = Tools::displayError('product quantity is required field');
						
						if(!$product_category)
							$this->errors[] = Tools::displayError('You have not selected any category');

						$obj_seller_product = new SellerProductDetail($id);

						//validate product main image
						if(isset($_FILES['product_image']))
							$this->validAddProductMainImage($_FILES['product_image']);							
						
						//validate product other images						
						if (isset($_FILES['images']))
							$this->validAddProductOtherImage($_FILES['images']);

						Hook::exec('actionBeforeUpdateproduct');

						if (!count($this->errors))
						{
							$obj_seller_product->price = $product_price;
							$obj_seller_product->quantity = $product_quantity;
							$obj_seller_product->product_name = $product_name;
							$obj_seller_product->description = $product_description;
							$obj_seller_product->short_description = $short_description;
							$obj_seller_product->id_category = $product_category[0];
							$obj_seller_product->condition = $product_condition;
							$obj_seller_product->save();

							//upload product main image
							if(isset($_FILES['product_image']))
								$obj_mpimage->uploadProductMainImage($_FILES['product_image'], $id);
								
							//upload product other images
							if (isset($_FILES['images']))
								$obj_mpimage->uploadProductOtherImage($_FILES['images']['tmp_name'], $id);

							//Update new categories
							Db::getInstance()->delete('marketplace_seller_product_category', 'id_seller_product = '.$id);  //Delete previous

							//Add new category into table
							$obj_mp_catg->id_seller_product = $id;
							$obj_mp_catg->is_default = 1;

							//set if more than one category selected
							$i = 0;
							foreach ($product_category as $p_category)
							{
								$obj_mp_catg->id_category = $p_category;
								if ($i != 0)
									$obj_mp_catg->is_default = 0;
								$obj_mp_catg->add();
								$i++;
							}
							
							$product_active = $obj_seller_product->active;
							if ($product_active) //update also in prestashop if product is active
							{
								$product_deatil = $obj_mp_shop_product->findMainProductIdByMppId($id);
								$main_product_id = $product_deatil['id_product'];
								$image_dir = _PS_MODULE_DIR_.'marketplace/views/img/product_img';
								$obj_seller_product->updatePsProductByMarketplaceProduct($id, $image_dir, 1 , $main_product_id);
							}
							Hook::exec('actionUpdateproductExtrafield', array('marketplace_product_id' => $id));
							//$this->context->smarty->assign('edited_conf', 1);
							$redirect_link = $this->context->link->getModuleLink('marketplace', 'productupdate', array('id' => $id,'edited_conf'=>1));
                  			Tools::redirect($redirect_link);
						}
					}
					else
						$this->errors[] = Tools::displayError('Product name is required fields.');
				}

				$id = Tools::getValue('id');
				$added = Tools::getValue('added');
				if ($added)
					$this->context->smarty->assign('added', 1);
				
				$this->context->smarty->assign('id', $id_product);
				Hook::exec('actionBeforeShowUpdatedProduct', array('marketplace_product_details' => $product_info));

				// category tree
				$tree = $obj_mp_catg->getCategoryTree($id_lang, $checked_product_cat, $defaultcatid);
				$this->context->smarty->assign('categoryTree', $tree);
				$this->context->smarty->assign('pro_info', $product_info);
				$this->context->smarty->assign('is_seller', 1);			
				$this->context->smarty->assign('logic', 'update_product');
				$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
				$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
				$this->setTemplate('productupdate.tpl');
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}

	public function validAddProductMainImage($image)
	{		
		if ($image['size'] > 0)
		{			
			if ($image['tmp_name'] != "")
			{
				if(!ImageManager::isCorrectImageFileExt($image['name']))
				{
				  	$this->errors[] = Tools::displayError('<strong>'.$_FILES['product_image']['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
				  	return false;
				}
			}
		}
		else
			return true;
	}

	public function validAddProductOtherImage($image)
	{
		//if any one is invalid extension redirect
		foreach ($image['name'] as $img_name)
		{
			if ($img_name != "")
			{
				if(!ImageManager::isCorrectImageFileExt($img_name))
				{
					$this->errors[] = Tools::displayError('Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
					return false;
				}
			}
		}
	}
		
	public function setMedia() 
	{
		parent::setMedia();
		$this->addCSS(array(
				_MODULE_DIR_.'marketplace/views/css/add_product.css',
				_MODULE_DIR_.'marketplace/views/css/marketplace_account.css'
			));

		//tinymce
		if(Configuration::get('PS_JS_THEME_CACHE') == 0)
			$this->addJS(array(
                    _MODULE_DIR_ .'marketplace/views/js/tinymce/tinymce.min.js',
                    _MODULE_DIR_ .'marketplace/views/js/tinymce/tinymce_wk_setup.js'
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