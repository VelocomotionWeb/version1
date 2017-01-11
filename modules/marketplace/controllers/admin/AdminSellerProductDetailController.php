<?php
class AdminSellerProductDetailController extends ModuleAdminController 
{
	public function __construct() 
	{
		$this->table = 'marketplace_seller_product';
		$this->className = 'SellerProductDetail';
		$this->bootstrap = true;
		
		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'marketplace_shop` mps ON (mps.`id` = a.`id_shop`)';
		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'marketplace_shop_product` mpsp ON (mpsp.`marketplace_seller_id_product` = a.`id`)';
		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info` mpsin ON (mpsin.`id` = a.`id_seller`)';
		$this->_select = 'mps.`shop_name`, mpsin.`seller_name`, mpsp.`id_product`';

		if ($id_seller = Tools::getValue('id_seller')) //if filter only seller products by seller view page
			$this->_where = 'AND a.`id_seller` = '.(int)$id_seller;
		
		$this->fields_list = array();
		$this->fields_list['id'] = array(
			'title' => $this->l('ID'),
			'align' => 'center',
			'class' => 'fixed-width-xs'
		);

		$this->fields_list['id_product'] = array(
			'title' => $this->l('Prestashop Product ID'),
			'align' => 'center',
			'hint' => $this->l('Generated Prestashop ID in Catalog')
		);

		$this->fields_list['product_name'] = array(
			'title' => $this->l('Product Name')
		);
		
		$this->fields_list['seller_name'] = array(
			'title' => $this->l('Seller Name')
		);

		$this->fields_list['shop_name'] = array(
			'title' => $this->l('Shop Name'),
			'havingFilter' => true
		);

		$hook_column = Hook::exec('addColumnInSellerProductTable', array('flag' => 1));
		
		$i = 0;
		if ($hook_column)
		{
			$column = explode('-',$hook_column);
			$num_colums = count($column);
			for ($i = 0; $i < $num_colums; $i = $i+2)
			{
				$this->fields_list[$column[$i]] = array(
					'title' => $this->l($column[$i+1]),
					'align' => 'center'
				);
			}
		}
			
		$this->fields_list['active'] = array(
			'title' => $this->l('Status'),
			'active' => 'status',
			'type' => 'bool',
			'orderby' => false
		);
		
		$this->identifier  = 'id';
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
											  'icon' => 'icon-trash',
											  'confirm' => $this->l('Delete selected items?'))
									);
		if ($wk_error_code = Tools::getValue('wk_error_code'))
		{
			if ($wk_error_code == 1)
				$this->errors[] = Tools::displayError('Their is some error to map marketplace product.');
			else if ($wk_error_code == 2)
				$this->errors[] = Tools::displayError('Can not able to create product in prestashop catalog.');
		}
		parent::__construct();
	}

	public function initToolbar() 
	{
		$obj_mpcutomer = new MarketplaceCustomer();
		$all_customer_is_seller = $obj_mpcutomer->findIsallCustomerSeller();
		
		if ($all_customer_is_seller)
		{
			parent::initToolbar();
			$this->page_header_toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
				'desc' => $this->l('Add new product')
			);
			$this->page_header_toolbar_btn['assignproducts'] = array(
				'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'&assignmpproduct=1',
				'desc' => $this->l('Assign product to seller'),
				'imgclass' => 'new'
			);
		}

		unset($obj_mpcutomer);
		unset($all_customer_is_seller);
	}
	
	public function renderList() 
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		return parent::renderList();
	}

	public function postProcess()
	{
		if (!$this->loadObject(true))
			return;
		
		$this->addJqueryPlugin(array('fancybox','tablednd'));
		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/add_product.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/mp_form_validation.js');

		//tinymce
		$this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
		if (version_compare(_PS_VERSION_, '1.6.0.11', '>'))
			$this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
		else
			$this->addJS(_PS_JS_DIR_.'tinymce.inc.js');

		//For Category tree
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/categorytree/jquery-ui-1.8.12.custom/js/jquery-ui-1.8.12.custom.min.js');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/js/categorytree/jquery-ui-1.8.12.custom/css/smoothness/jquery-ui-1.8.12.custom.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/categorytree/jquery.checkboxtree.js');
		$this->addCSS(_MODULE_DIR_.'marketplace/views/js/categorytree/wk.checkboxtree.css');
		
		if (Tools::isSubmit('statusmarketplace_seller_product'))
			$this->activeSellerProduct();

		parent::postProcess();	
	}
		
	public function renderForm() 
	{
		//tinymce setup
		$this->context->smarty->assign('path_css',_THEME_CSS_DIR_);
		$this->context->smarty->assign('ad',__PS_BASE_URI__.basename(_PS_ADMIN_DIR_));
		$this->context->smarty->assign('autoload_rte',true);
        $this->context->smarty->assign('lang',true);
        $this->context->smarty->assign('iso', $this->context->language->iso_code);
		$id_lang = $this->context->cookie->id_lang;

		$obj_mp_product = new SellerProductDetail();
		$obj_mp_product_category = new SellerProductCategory();
		$obj_mp_customer = new MarketplaceCustomer();
		$obj_mpshop_products = new MarketplaceShopProduct();

		if (Tools::getValue('assignmpproduct'))
		{
			$mp_sellers = $obj_mp_customer->getAllSellerInfo();
			if ($mp_sellers)
			{
				$start = 0;
				$limit = 0;
				$order_by = 'id_product';
				$order_way = 'ASC';
				$ps_products = Product::getProducts($id_lang, $start, $limit, $order_by, $order_way, false, true);
				if ($ps_products)
				{
					foreach ($ps_products as $key => $product)
					{
						$mp_product = $obj_mpshop_products->getMpProductIdByPsProductId($product['id_product']);
						if ($mp_product) //remove marketplace products from list
							unset($ps_products[$key]);
					}
					$this->context->smarty->assign('ps_products', $ps_products);
				}
				//foreach ($mp_sellers as $key => $seller)
				//	$mp_sellers[$key]['id_customer'] = $obj_mp_customer->getCustomerId($seller['id']);
				$this->context->smarty->assign('mp_sellers', $mp_sellers);
			}

			$this->context->smarty->assign('assignmpproduct', 1);
		}

		if ($this->display == 'add')
		{
			//Prepair Category Tree
			$category_tree = $obj_mp_product_category->getCategoryTree($id_lang);
			$this->context->smarty->assign('categoryTree', $category_tree);

			$customer_info = $obj_mp_customer->getAllSellerInfo();
			if ($customer_info)
				$this->context->smarty->assign('customer_info', $customer_info);
		}
		elseif ($this->display == 'edit') 
		{
			$id = Tools::getValue('id');
			$checked_product_cat = $obj_mp_product->getMarketPlaceProductCategories($id);
			$defaultcatid = $obj_mp_product_category->getMpDefaultCategory($id);
			
			//Prepair Category Tree
			$category_tree = $obj_mp_product_category->getCategoryTree($id_lang, $checked_product_cat, $defaultcatid);
			$this->context->smarty->assign('categoryTree', $category_tree);
			$this->context->smarty->assign('edit', 1);
			
			//Image information if product is active
			$active_product = $obj_mp_product->getMarketPlaceShopProductDetailBYmspid($id);
			if ($active_product)
			{
				$id_product = $active_product['id_product'];
				$obj_product = new Product($id_product, false, $id_lang);
				$image_detail = $obj_product->getImages($id_lang);
				if ($image_detail)
				{
					foreach($image_detail as $key => $image)
					{
						$obj_image = new Image($image['id_image']);
						$image_detail[$key]['image'] = $id_product.'-'.$image['id_image'];
						$image_detail[$key]['image_path'] = _THEME_PROD_DIR_.$obj_image->getExistingImgPath().'.jpg';
						$image_detail[$key]['link_rewrite'] = $obj_product->link_rewrite;
					}
					$this->context->smarty->assign('image_detail', $image_detail);
					$this->context->smarty->assign('id_product',$id_product);
				}
				$this->context->smarty->assign('active_product', 1);
			}

			//Image information if product is not activated yet
			$product = $obj_mp_product->getMarketPlaceProductInfo($id);
			if ($product)
			{
				$unactive_image = $obj_mp_product->unactiveImage($product['id']);
				if ($unactive_image)
					$this->context->smarty->assign('unactive_image', $unactive_image);

				$this->context->smarty->assign('product', $product);
			}
		}

		$this->context->smarty->assign('modules_dir', _MODULE_DIR_);
		$this->fields_form = array(
				'submit' => array(
					'title' => $this->l('Save')
				)
			);
		return parent::renderForm();
	}
		
	public function processSave() 
	{
		if (Tools::getValue('assignmpproduct')) //Process of assignig products
		{
			$id_product = Tools::getValue('id_product');
			$id_customer = Tools::getValue('id_customer');
			Hook::exec('actionBeforeAssignMpProduct', array('id_product' => $id_product, 'id_customer' => $id_customer));
			if (empty($this->errors))
			{
				$obj_seller_product = new SellerProductDetail();
				$assigned = $obj_seller_product->assignProductToSeller($id_product, $id_customer);

				if (!$assigned)
				{
					$this->errors[] = Tools::displayError('Some problem occure while assigning product.');
					$redirect = self::$currentIndex.'&add'.$this->table.'&token='.$this->token.'&assignmpproduct=1';
					$this->redirect_after = $redirect;
				}
			}

			if (empty($this->errors))
				Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
		}
		else
		{
			$product_name = Tools::getValue('product_name');
			$product_price = Tools::getValue('product_price');
			$product_quantity = Tools::getValue('product_quantity');
			$product_description = Tools::getValue('product_description');
			$product_category = Tools::getValue('product_category');
			$short_description = Tools::getValue('short_description');
			$product_condition = Tools::getValue('product_condition');
			$id_mp_product = Tools::getValue('id'); //if edit
			
			$obj_mp_shop = new MarketplaceShop();
			$obj_mp_shop_prod = new MarketplaceShopProduct();
			
			$product_img_path = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';
			
			if($product_name == '')
				$this->errors[] = Tools::displayError('Product name is required field.');
			else if (!Validate::isGenericName($product_name))
				$this->errors[] = Tools::displayError($this->l('Product name must not have Invalid characters <>;=#{}'));

			if ($short_description)
			{
				$limit = (int)Configuration::get('PS_PRODUCT_SHORT_DESC_LIMIT');
				if ($limit <= 0) $limit = 400;
				if(!Validate::isCleanHtml($short_description))
					$this->errors[] = Tools::displayError($this->l('Invalid short description'));

				if (Tools::strlen(strip_tags($short_description)) > $limit)
				{
					$this->errors[] = sprintf(
						Tools::displayError('Short description field is too long: %1$d chars max (current count %2$d).'),
						$limit,
						Tools::strlen(strip_tags($short_description))
					);
				}
			}

			if ($product_description)
			{
				if(!Validate::isCleanHtml($product_description, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
					$this->errors[] = Tools::displayError($this->l('Invalid product description'));
			}

			if($product_price == '')
				$this->errors[] = Tools::displayError('Product price is required field.');
			else if (!Validate::isPrice($product_price))
				$this->errors[] = Tools::displayError('Invalid product price');

			if($product_quantity == '')
				$this->errors[] = Tools::displayError('Product quantity required field.');
			else if (!Validate::isInt($product_quantity))
				$this->errors[] = Tools::displayError('Invalid product quantity.');
			
			if(!$product_category)
				$this->errors[] = Tools::displayError('Please select at least one category.');
				
			//validate product main image
			if(isset($_FILES['product_image']))
				$this->validAddProductMainImage($_FILES['product_image']);							
			
			//validate product other images
			if (isset($_FILES['images']))
				$this->validAddProductOtherImage($_FILES['images']);

			if ($id_mp_product)
				Hook::exec('actionBeforeUpdateproduct');
			else
			{
				$id_customer = Tools::getValue('shop_customer');
				$mp_shop_info = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
				$id_shop  = $mp_shop_info['id'];
				$id_seller = MarketplaceShop::findMpSellerIdByShopId($id_shop);
				
				Hook::exec('actionBeforeAddproduct', array('mp_seller_id' => $id_seller));
			}

			if (empty($this->errors))
			{
				if ($id_mp_product) //if edit
				{
					$obj_seller_product = new SellerProductDetail($id_mp_product);
					$obj_seller_product->price = $product_price;
					$obj_seller_product->quantity = $product_quantity;
					$obj_seller_product->product_name = $product_name;
					$obj_seller_product->description = $product_description;
					$obj_seller_product->short_description = $short_description;
					$obj_seller_product->id_category = $product_category[0];
					$obj_seller_product->condition = $product_condition;
					$obj_seller_product->save();

					//Update new categories
					Db::getInstance()->delete('marketplace_seller_product_category', 'id_seller_product = '.$id_mp_product);//Delete previous

					//Add new category into table
					$this->assignMpProductCategory($product_category, $id_mp_product);

					// upload main image
					if (isset($_FILES['product_image']))
					    $this->uploadMainImage($_FILES['product_image'], $id_mp_product, $product_img_path, 0);

					//upload product other images
					if (isset($_FILES['images']))
					    $this->uploadOtherImages($_FILES['images'], $id_mp_product, $product_img_path);
					
					if ($obj_seller_product->active)
					{
						$product_deatil = $obj_mp_shop_prod->findMainProductIdByMppId($id_mp_product);
						if ($product_deatil)
							$obj_seller_product->updatePsProductByMarketplaceProduct($id_mp_product, $product_img_path, 1, $product_deatil['id_product']);
					}
					Hook::exec('actionUpdateproductExtrafield', array('marketplace_product_id' => $id_mp_product));
				}
				else //if add new
				{					
					$obj_seller_product = new SellerProductDetail();
					$obj_seller_product->price = $product_price;
					$obj_seller_product->quantity = $product_quantity;
					$obj_seller_product->product_name = $product_name;
					$obj_seller_product->description = $product_description;
					$obj_seller_product->short_description = $short_description;
					$obj_seller_product->id_category = $product_category[0];
					$obj_seller_product->id_seller = $id_seller;
					$obj_seller_product->ps_id_shop = $this->context->shop->id;
					$obj_seller_product->condition = $product_condition;
					$obj_seller_product->id_shop = $id_shop;

					if (Configuration::get('MP_PRODUCT_ADMIN_APPROVE'))
						$obj_seller_product->active = 0;
					else
						$obj_seller_product->active = 1;

					$obj_seller_product->save();					 
					$seller_product_id = $obj_seller_product->id;

					//Add into category table
					$this->assignMpProductCategory($product_category, $seller_product_id);

					//upload product image
					if(isset($_FILES['product_image']))
						$this->uploadMainImage($_FILES['product_image'], $seller_product_id, $product_img_path);
					
					//upload product other images
					if (isset($_FILES['images']))
						$this->uploadOtherImages($_FILES['images'], $seller_product_id, $product_img_path);

					//if default approval on, then entry of a product in ps_product table
					if (!Configuration::get('MP_PRODUCT_ADMIN_APPROVE'))
					{
						// creating ps_product when admin setting is default
						$ps_product_id = $obj_seller_product->createPsProductByMarketplaceProduct($seller_product_id, $product_img_path, 1);
						if ($ps_product_id)
						{
							// mapping of ps_product and mp_product id
							$mps_product_obj = new MarketplaceShopProduct();
							$mps_product_obj->id_shop = $id_shop;
							$mps_product_obj->marketplace_seller_id_product = $seller_product_id;
							$mps_product_obj->id_product = $ps_product_id;
							$mps_product_obj->add();
						}
					}
					Hook::exec('actionAddproductExtrafield', array('marketplace_product_id' => $seller_product_id));
				}

				if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
				{
					if ($id_mp_product)
						Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$id_mp_product.'&update'.$this->table.'&conf=4&token='.$this->token);
					else
						Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$seller_product_id.'&update'.$this->table.'&conf=3&token='.$this->token);
				}
				else
				{
					if ($id_mp_product)
						Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
					else
						Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
				}
			}
			else
			{
				if ($id_mp_product)
					$this->display = 'edit';
				else
					$this->display = 'add';
			}
		}
	}

	public function assignMpProductCategory($product_category, $mp_id_product)
	{
		if (!is_array($product_category))
			return false;

		$obj_seller_product_category = new SellerProductCategory();
		$obj_seller_product_category->id_seller_product = $mp_id_product;
		$i = 0;
		foreach($product_category as $p_category)
		{
			$obj_seller_product_category->id_category = $p_category;
			if ($i != 0)
				$obj_seller_product_category->is_default = 0;
			$obj_seller_product_category->add();
			$i++;
		}
	}

	public function processStatus()
	{
		if (empty($this->errors))
			parent::processStatus();
	}

	public function activeSellerProduct($mp_product_id = false) 
	{
		$main_product_id = 0;
		if (!$mp_product_id)
			$mp_product_id = Tools::getValue('id');
		Hook::exec('actionBeforeToggleProductStatus', array('mp_product_id' => $mp_product_id));
		if (!count($this->errors))
		{

			$obj_mpshop_product = new MarketplaceShopProduct();
			
			$obj_mp_product = new SellerProductDetail($mp_product_id);
			$mp_id_shop = $obj_mp_product->id_shop;
			if ($obj_mp_product->active) // going to be deactive
			{
				//product created but deactive now
				$obj_mp_product->active = 0;
				$obj_mp_product->save();
				
				$shop_product = $obj_mpshop_product->findMainProductIdByMppId($mp_product_id);
				if ($shop_product)
				{
					$main_product_id = $shop_product['id_product'];
					$product = new Product($shop_product['id_product']);
					$product->active = 0;
					$product->save();
				}
				$obj_mp_product->callMailFunction($mp_product_id, 2, 2);
			} 
			else // going to be active
			{
				$shop_product = $obj_mpshop_product->findMainProductIdByMppId($mp_product_id);
				$image_dir = _PS_MODULE_DIR_.'marketplace/views/img/product_img';
				if ($shop_product) //product created but dactivated right now, need to active 
				{
					$obj_mp_product->active = 1;
					$obj_mp_product->save();
					$obj_mp_product->updatePsProductByMarketplaceProduct($mp_product_id, $image_dir, 1, $shop_product['id_product']);
					$main_product_id = $shop_product['id_product'];
				}
				else //not yet product created
				{
					$id_product = $obj_mp_product->createPsProductByMarketplaceProduct($mp_product_id, $image_dir, 1);
					if ($id_product)
					{
						$main_product_id = $id_product;
						$mps_product_obj = new MarketplaceShopProduct();
						$mps_product_obj->id_shop = $mp_id_shop;
						$mps_product_obj->marketplace_seller_id_product = $mp_product_id;
						$mps_product_obj->id_product = $id_product;
						$mps_product_obj->add();
						$mps_product_id = $mps_product_obj->id;
						if (!$mps_product_id)
							Tools::redirectAdmin(self::$currentIndex.'&wk_error_code=1&token='.$this->token);
						else
						{
							$obj_mp_product->active = 1;
							$obj_mp_product->save();
							Hook::exec('actionToogleProductStatus', array('main_product_id' => $id_product, 'active' => 1));
						}
					}
					else
						Tools::redirectAdmin(self::$currentIndex.'&wk_error_code=2&token='.$this->token);
				}
				Hook::exec('actionToogleProductStatusGlobal', array('mp_product_id' => $mp_product_id, 'active' => $obj_mp_product->active));
				$obj_mp_product->callMailFunction($mp_product_id, 1, 1);
			}
			Hook::exec('actionToogleProductStatusNew', array('main_product_id' => $main_product_id, 'active' => $obj_mp_product->active));
		}
	}

	public function validAddProductMainImage($image)
	{
		if ($image['size'] > 0)
		{			
			if ($image['tmp_name'] != "")
			{
				if(!ImageManager::isCorrectImageFileExt($image['name']))
				  	$this->errors[] = Tools::displayError('<strong>'.$_FILES['product_image']['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
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

	public function uploadMainImage($image, $seller_product_id, $product_img_path, $active = false)
	{
		if (isset($image))
		{
			if ($image['size'] > 0)
			{
				$rand_name = MpHelper::randomImageName();
				Db::getInstance()->insert('marketplace_product_image',
								array('seller_product_id' => (int) $seller_product_id,
									'seller_product_image_id' => pSQL($rand_name),
									'active' => $active ? $active : 0,
							));
				$image_name = $rand_name.'.jpg';
				ImageManager::resize($image['tmp_name'], $product_img_path.$image_name);
			}
		}
	}
	
	public function uploadOtherImages($image, $seller_product_id, $product_img_path)
	{
		//Upload More Images
		if(isset($image))
		{
			$other_images  = $image['tmp_name'];
			$count = count($other_images);
		}
		else
			$count = 0;	
		
		for ($i = 0; $i < $count; $i++)
		{
			$rand_name = MpHelper::randomImageName();
			Db::getInstance()->insert('marketplace_product_image', array(
										'seller_product_id' => (int) $seller_product_id,
										'seller_product_image_id' => pSQL($rand_name)
									));
			$image_name = $rand_name.'.jpg';
			ImageManager::resize($other_images[$i], $product_img_path.$image_name);
		}
	}

	public function ajaxProcessDeleteUnactiveImage()
	{
		$id_image = Tools::getValue('id_image');
		$img_name = Tools::getValue('img_name');

		$delete = Db::getInstance()->delete("marketplace_product_image","id=".$id_image." AND seller_product_image_id ='".$img_name."'");
		$dir = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';
		
		if ($delete)
		{
			@unlink($dir.$img_name.'.jpg');
			die(Tools::jsonEncode(array('status' => 1)));
		}
		else
			die(Tools::jsonEncode(array('status' => 2)));
	}


	public function ajaxProcessDeleteActiveImage()
	{
		$id_lang = $this->context->language->id;
		$id_image = Tools::getValue('id_image');
		$id_product = Tools::getValue('id_pro');
		$is_cover = Tools::getValue('is_cover');
		$obj_image = new Image($id_image);
		if ($obj_image->delete())
		{
			Product::cleanPositions($id_product);
			// if cover image deleting, make first image as a cover
			if ($is_cover)
			{
				$images = Image::getImages($id_lang, $id_product);
				if ($images)
				{
					$obj_image = new Image($images[0]['id_image']);
					$obj_image->cover = 1;
					$obj_image->save();	
				}
				die(Tools::jsonEncode(array('status' => 2)));
			}
			else
				die(Tools::jsonEncode(array('status' => 1)));
		}
		else
			die(Tools::jsonEncode(array('status' => 3)));
	}

	public function ajaxProcessChangeImageCover()
	{
		$id_image = Tools::getValue('id_image');
		$id_product = Tools::getValue('id_pro');
		if (isset($id_image))
		{
			$product = new Product($id_product);
			$product->setCoverWs($id_image);
			die(Tools::jsonEncode(array('status' => 1)));
		}
		else
			die(Tools::jsonEncode(array('status' => 2)));
	}

	protected function processBulkEnableSelection()
	{
		return $this->processBulkStatusSelection(1);
	}
	
	protected function processBulkDisableSelection()
	{
		 return $this->processBulkStatusSelection(0);
	}

	protected function processBulkStatusSelection($status)
	{
		if ($status == 1)
		{
			if (is_array($this->boxes) && !empty($this->boxes))
			{
				foreach ($this->boxes as $id)
				{
					$obj_seller_product = new SellerProductDetail($id);
					if ($obj_seller_product->active == 0)
						$this->activeSellerProduct($id);
				}
			}
		}
		else if ($status == 0)
		{
			if (is_array($this->boxes) && !empty($this->boxes))
			{
				foreach ($this->boxes as $id)
				{
					$obj_seller_product = new SellerProductDetail($id);
					if ($obj_seller_product->active == 1)
						$this->activeSellerProduct($id);
				}
			}
		}
	}
}
?>