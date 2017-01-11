<?php
class SellerProductDetail extends ObjectModel
{
	public $id;
	public $id_seller;
	public $price;
	public $quantity;
	public $product_name;
	public $id_category;
	public $short_description;
	public $description;
	public $active;
	public $id_shop;
	public $ps_id_shop;
	public $condition;
	public $admin_assigned;  // if product assigned by admin to seller this will be 1
	public $date_add;
	public $date_upd;

	
	public static $definition = array(
		'table' => 'marketplace_seller_product',
		'primary' => 'id',
		'fields' => array(
			'id_seller' => array('type' => self::TYPE_INT,'required' => true),
			'price' => array('type' => self::TYPE_FLOAT,'validate' => 'isPrice', 'required' => true),
			'quantity' => array('type' => self::TYPE_INT),
			'product_name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true),
			'id_category' => array('type' => self::TYPE_INT),
			'short_description' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'description' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml'),
			'active' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
			'id_shop' => array('type' => self::TYPE_INT),			
			'ps_id_shop' => array('type' => self::TYPE_INT),
			'condition' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'values' => array('new', 'used', 'refurbished'), 'default' => 'new'),
			'admin_assigned' => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),		
			'date_add' => 	array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
			'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => false),
		),
	);

	public function delete()
	{
		if (!$this->deleteMpProduct($this->id) || !parent::delete())
			return false;
		return true;
	}

	/**
	 * deleting from all tables if product is activated
	 * @param  [int] $mp_id_product
	 * @return [boolean]
	 */
	public function deleteMpProduct($mp_id_product)
	{
		$obj_mpshopproduct = new MarketplaceShopProduct();
		$ps_delete = true;
		$mp_prod_details = $obj_mpshopproduct->findMainProductIdByMppId($mp_id_product);
		Hook::exec('actionMpProductDelete', array('mp_id_product' => $mp_id_product));
		if ($mp_prod_details) //if activated
		{
			$id_product = $mp_prod_details['id_product'];
			$delete_mpproduct = Db::getInstance()->delete('marketplace_shop_product',
										'id_product = '.(int)$id_product);
			
			if (!$delete_mpproduct)
				$ps_delete = false;
			else
			{
				$obj_mpproduct = new SellerProductDetail($mp_id_product);
				if (!$obj_mpproduct->admin_assigned) //delete only seller created products not the admin assigned products from catalog list
				{
					$obj_product = new Product($id_product);
					$obj_product->delete();
				}
			}
		}

		$delete_mpcatg = Db::getInstance()->delete('marketplace_seller_product_category',
									'id_seller_product = '.(int)$mp_id_product);

		if (!$ps_delete
				|| !$delete_mpcatg
				|| !$this->deleteMpProductImage($mp_id_product))
				return false;
			return true;
	}

	public function deleteMpProductImage($mp_id_product, $mp_id_image = false)
	{
		if ($mp_id_image)
		{
			if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$mp_id_image.'.jpg'))
				if (unlink(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$mp_id_image.'.jpg'))
					if (!Db::getInstance()->delete('marketplace_product_image', 'seller_product_image_id = '.$mp_id_image))
						return false;
		}
		else
		{
			$product_images = $this->getMpProductImages($mp_id_product);
			if ($product_images)
				foreach ($product_images as $image)
				{
					if (file_exists(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$image['seller_product_image_id'].'.jpg'))
						if (unlink(_PS_MODULE_DIR_.'marketplace/views/img/product_img/'.$image['seller_product_image_id'].'.jpg'))
							if (!Db::getInstance()->delete('marketplace_product_image', 'seller_product_id = '.(int)$mp_id_product))
								return false;
				}
		}

		return true;
	}

	/**
	 * [get mp seller active product with ps image details and link used for product slider on seller profile and shop page]
	 * @param  [int] $mp_id_shop [mp id shop]
	 * @param  [int] $ps_id_shop [ps id shop]
	 * @param  [int] $id_lang    [language id]
	 * @return [array/bool]      [array/false]
	 */
	public function getActiveMpProductWithImage($mp_id_shop, $ps_id_shop, $id_lang)
	{
		$product_detail = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_shop_product` mpsp
									JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = mpsp.`id_product`)
									JOIN `'._DB_PREFIX_.'product_lang` pl ON (p.`id_product` = pl.`id_product`)
									WHERE mpsp.`id_shop` = '.$mp_id_shop.' AND pl.`id_shop` = '.$ps_id_shop.'
									AND pl.`id_lang` = '.$id_lang.' AND p.active = 1 ORDER BY p.`date_add` LIMIT 10');

		if ($product_detail)
		{
			foreach  ($product_detail as $key => $product)
			{
				$obj_product = new Product($product['id_product'], false, $id_lang);
				$product_detail[$key]['product'] = $obj_product;
				$product_detail[$key]['lang_iso'] = Context::getContext()->language->iso_code;
				$cover = Product::getCover($product['id_product']);
				if ($cover)
					$product_detail[$key]['image'] = $product['id_product'].'-'.$cover['id_image'];
				else
					$product_detail[$key]['image'] = 0;
			}
			return $product_detail;
		}

		return false;
	}

	/**
	 * [getMarketPlaceIdShopByIdProduct description]
	 * @param  [int] $id_product [from ps_product table]
	 * @return [int]             [mp_shop_id]
	 */
	public function getMarketPlaceIdShopByIdProduct($id_product)
	{
		$mp_shop_id = Db::getInstance()->getValue('SELECT `id_shop` FROM `'._DB_PREFIX_.'marketplace_seller_product`
											WHERE `id` = '.(int)$id_product);
		if ($mp_shop_id)
			return $mp_shop_id;
		return false;
	}

	/**
	 * [getPriceByIdCurrency convert the price by currency rate]
	 * @param  [float] $price       [price]
	 * @param  [int] $id_currency [currency id]
	 * @return [float/false]
	 */
	public static function getPriceByIdCurrency($price, $id_currency = false)
	{
		if (!$id_currency)
			$id_currency = Context::getContext()->currency->id;

		if ($price != '')
		{
			$obj_curreny = Currency::getCurrency($id_currency);
			$price_conversion_rate = $obj_curreny['conversion_rate'];
			return ($price * $price_conversion_rate);
		}

		return false;
	}

	public static function getMpProductImages($mp_id_product)
	{
		$product_images = Db::getInstance()->executeS('SELECT * FROM `'._DB_PREFIX_.'marketplace_product_image`
											WHERE `seller_product_id` = '.(int)$mp_id_product);

		if ($product_images && !empty($product_images))
			return $product_images;

		return false;
	}

	public function changeSellerProductStatus($mp_id_shop, $status)
	{
		return Db::getInstance()->update('marketplace_seller_product', array('active' => $status),'id_shop='.$mp_id_shop);
	}

	public static function getMpSellerProductDetails($id_seller, $active = false)
	{
		$sql = 'SELECT * FROM `'._DB_PREFIX_.'marketplace_seller_product` WHERE `id_seller` = '.$id_seller;

		if ($active)
			$sql .= ' AND `active` = 1';

		$sql .= ' ORDER BY name ';
		$mp_products = Db::getInstance()->executeS($sql);

		if ($mp_products && !empty($mp_products))
			return $mp_products;

		return false;
	}
	
	/**
	 * [create marketplace product in prestashop while activating]
	 * @param  [int] $mp_product_id [id product marketplace]
	 * @param  [string] $image_dir  [image upload directory]
	 * @param  [int] $active        [status]
	 * @return [int/array]          [id_product added to prestashop OR false]
	 */
	public function createPsProductByMarketplaceProduct($mp_product_id, $image_dir, $active) 
	{
		$count = 0;
		$default_tax_rule_group = 1;
		$product_info = $this->getMarketPlaceProductInfo($mp_product_id);
		$quantity = (int)$product_info['quantity'];
		$category_id = (int)$product_info['id_category'];

		// Add Product
		$product = new Product();
		$product->name = array();
		$product->description = array();
		$product->description_short = array();
		$product->link_rewrite = array();
		foreach (Language::getLanguages(true) as $lang)
		{
			$product->name[$lang['id_lang']] = $product_info['product_name'];
			$product->description[$lang['id_lang']] = $product_info['description'];
			$product->description_short[$lang['id_lang']] = $product_info['short_description'];
			$product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($product_info['product_name']);
		}

		$product->id_shop_default = Context::getContext()->shop->id;
		$product->id_category_default = $category_id;
		$product->price = $product_info['price'];
		$product->active = $active;
		$product->indexed = 1;
		$product->condition = $product_info['condition'];
		
		$obj_tax = new TaxRulesGroup($default_tax_rule_group);
		if($obj_tax->active == 0)
			$product->id_tax_rules_group = 0;
		else	
			$product->id_tax_rules_group = 1;
			
		$product->save();
		$ps_product_id = $product->id;
		Search::indexation(Tools::link_rewrite($product_info['product_name']),$ps_product_id);
		if ($ps_product_id > 0)
		{
			if ($category_id > 0)
			{
				$category_ids = $this->getMultipleCategories($mp_product_id);
				$product->addToCategories($category_ids);
			}

			if ($quantity >= 0)
				StockAvailable::updateQuantity($ps_product_id, null, $quantity);

			$image_list = $this->unactiveImage($mp_product_id);
			if ($image_list)
			{
				foreach ($image_list as $image)
				{
					$old_path = $image_dir.'/'.$image['seller_product_image_id'].'.jpg';
					$position = $count + 1;
					$image_obj = new Image();
					$image_obj->id_product = $ps_product_id;
					$image_obj->position = $position;

					if ($count == 0)
						$image_obj->cover = 1;
					else
						$image_obj->cover = 0;

					$image_obj->add();
					$image_id = $image_obj->id;				
					$new_path = $image_obj->getPathForCreation();
					$imagesTypes = ImageType::getImagesTypes('products');
					
					foreach ($imagesTypes as $image_type)
						ImageManager::resize($old_path, $new_path.'-'.$image_type['name'].'.jpg', $image_type['width'], $image_type['height']);
					
					ImageManager::resize($old_path,$new_path.'.jpg');
					Hook::exec('actionWatermark', array('id_image' => $image_id, 'id_product' => $ps_product_id));
					Hook::exec('actionPsMpImageMap', array('mp_product_id' => $mp_product_id, 'mp_id_image' => $image['id'],'ps_id_product' => $ps_product_id, 'ps_id_image' => $image_id));
					//updating mp_product_image status
					Db::getInstance()->update('marketplace_product_image', array('active' => 1),'seller_product_image_id ="'.$image['seller_product_image_id'].'" ');
					$count = $count + 1;
				}
			}
			return $ps_product_id;
		}
		return false;
	}
	
	public function getMultipleCategories($mp_product_id)
	{
		$mcategory = Db::getInstance()->executeS("SELECT `id_category` FROM `"._DB_PREFIX_."marketplace_seller_product_category`
										WHERE `id_seller_product` = ".(int)$mp_product_id);
		 
		if(empty($mcategory))
			return false;

		$mcat = array();
		foreach ($mcategory as $cat)
			$mcat[] = $cat['id_category'];

		return 	$mcat;
	}
	
	/**
	 * [update marketplace product in prestashop]
	 * @param  [int] $mp_product_id [id product marketplace]
	 * @param  [string] $image_dir  [image upload directory]
	 * @param  [int] $active        [status]
	 * @param  [int] $main_product_id        [id_product prestashop product id]
	 * @return [int/array]          [id_product added to prestashop OR false]
	 */
	public function updatePsProductByMarketplaceProduct($mp_product_id, $image_dir, $active, $main_product_id) 
	{
		$count = 0;
		$default_tax_rule_group = 1;
		$id_lang = Context::getContext()->language->id;
		$product_info = $this->getMarketPlaceProductInfo($mp_product_id);
		$quantity = (int)$product_info['quantity'];
		$category_id = (int)$product_info['id_category'];
		$ps_id_shop = (int)$product_info['ps_id_shop'];

		// Add Product
		$product = new Product($main_product_id);
		$product->name = array();
		$product->description = array();
		$product->description_short = array();
		$product->link_rewrite = array();
		foreach (Language::getLanguages(true) as $lang)
		{
			$product->name[$lang['id_lang']] = $product_info['product_name'];
			$product->description[$lang['id_lang']] = $product_info['description'];
			$product->description_short[$lang['id_lang']] = $product_info['short_description'];
			$product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($product_info['product_name']);
		}

		$product->id_shop_default = Context::getContext()->shop->id;
		$product->id_category_default = $category_id;
		$product->price = $product_info['price'];
		$product->active = $active;
		$product->indexed = 1;
		$product->condition = $product_info['condition'];
		
		$obj_tax = new TaxRulesGroup($default_tax_rule_group);
		if($obj_tax->active == 0)
			$product->id_tax_rules_group = 0;
		else	
			$product->id_tax_rules_group = 1;
		
		$product->save();
		$ps_product_id = $product->id;
		Search::indexation(Tools::link_rewrite($product_info['product_name']),$ps_product_id);
		if ($ps_product_id > 0)
		{
			if ($category_id > 0)
			{
				$category_ids = $this->getMultipleCategories($mp_product_id);
				$product->updateCategories($category_ids);
			}

			if ($quantity >= 0)
				StockAvailable::setQuantity($ps_product_id, 0, $quantity, $ps_id_shop);

			$image_list = $this->unactiveImage($mp_product_id);
			if ($image_list)
			{
				$have_cover = false;
				// if one of the other image is already have cover
				$images = Image::getImages($id_lang, $main_product_id);
				if ($images)
				{
					foreach ($images as $img)
						if ($img['cover'] == 1)
							$have_cover = true;
				}

				foreach ($image_list as $image)
				{
					$old_path = $image_dir.'/'.$image['seller_product_image_id'].'.jpg';
					//$position = $count + 1;
					$image_obj = new Image();
					$image_obj->id_product = $ps_product_id;
					$image_obj->position = Image::getHighestPosition($main_product_id) + 1;

					if ($count == 0)
					{
						if (!$have_cover)
							$image_obj->cover = 1;
					}
					else
						$image_obj->cover = 0;

					$image_obj->add();
					$image_id = $image_obj->id;				
					$new_path = $image_obj->getPathForCreation();
					$imagesTypes = ImageType::getImagesTypes('products');
					
					foreach ($imagesTypes as $image_type)
						ImageManager::resize($old_path, $new_path.'-'.$image_type['name'].'.jpg', $image_type['width'],$image_type['height']);
					
					ImageManager::resize($old_path,$new_path.'.jpg');
					Hook::exec('actionWatermark', array('id_image' => $image_id, 'id_product' => $ps_product_id));
					Hook::exec('actionPsMpImageMap', array('mp_product_id' => $mp_product_id, 'mp_id_image' => $image['id'],'ps_id_product' => $ps_product_id, 'ps_id_image' => $image_id));
					//updating mp_product_image status ...
					Db::getInstance()->update('marketplace_product_image', array('active' =>1),'seller_product_image_id ="'.$image['seller_product_image_id'].'" ');
					$count = $count + 1;
				}
			}
			return $ps_product_id;
		}
		return false;
	}

	/**
	 * [assignProductToSeller assign prestashop product to marketplace seller]
	 * @param  [int] $id_product  [prestashop id product]
	 * @param  [int] $id_customer [prestashop id customer]
	 * @return [mp_id_product/false]
	 */
	public function assignProductToSeller($id_product, $id_customer)
	{
		$id_lang = Context::getContext()->language->id;

		$obj_mpcustomer = new MarketplaceCustomer();
		$obj_mpshop = new MarketplaceShop();
		$obj_seller_product_category = new SellerProductCategory();
		$obj_mpshop_product = new MarketplaceShopProduct();
		$obj_mp_product_img = new MarketplaceProductImage();

		$mp_img_path = _PS_MODULE_DIR_.'marketplace/views/img/product_img/';

		$mp_seller = $obj_mpcustomer->findMarketPlaceCustomer($id_customer);
		$mp_shop = $obj_mpshop->getMarketPlaceShopInfoByCustomerId($id_customer);

		if (!$mp_seller)
			return false;

		if (!$mp_shop)
			return false;

		$id_seller = $mp_seller['marketplace_seller_id'];
		$id_mp_shop = $mp_shop['id'];

		//get product details
		$obj_product = new Product($id_product, false, $id_lang);

		//insert into marketplace_seller_product table
		$obj_seller_product = new SellerProductDetail();
		$obj_seller_product->id_seller = $id_seller;
		$obj_seller_product->price = $obj_product->price;
		$obj_seller_product->quantity = StockAvailable::getQuantityAvailableByProduct($id_product);
		$obj_seller_product->product_name = $obj_product->name;
		$obj_seller_product->id_category = $obj_product->id_category_default;
		$obj_seller_product->short_description = $obj_product->description_short;
		$obj_seller_product->description = $obj_product->description;
		$obj_seller_product->active = 1;
		$obj_seller_product->id_shop = $id_mp_shop;
		$obj_seller_product->admin_assigned = 1;  // if product assigned by admin to seller
		$obj_seller_product->ps_id_shop = Context::getContext()->shop->id;
		$obj_seller_product->save();
		$id_mp_product = $obj_seller_product->id;

		if ($id_mp_product)
		{
			//get prestashop product categories
			$categories = $obj_product->getCategories();

			if (!$categories)
				return false;

			//save product categories in marketplace
			$obj_seller_product_category->id_seller_product = $id_mp_product;
			foreach ($categories as $category)
			{
				$obj_seller_product_category->id_category = $category;
				if ($category == $obj_product->id_category_default)
					$obj_seller_product_category->is_default = 1;
				else
					$obj_seller_product_category->is_default = 0;

				$obj_seller_product_category->add();
			}

			//upload prestashop product images to marketplace
			$images = $obj_product->getImages($id_lang);
			if ($images)
			{
				foreach ($images as $image)
				{
					$rand_name = MpHelper::randomImageName();
					
					// save to marketplace image table
					$obj_mp_product_img->seller_product_id = (int)$id_mp_product;
					$obj_mp_product_img->seller_product_image_id = pSQL($rand_name);
					$obj_mp_product_img->active = 1;
					$obj_mp_product_img->add();

					$obj_image = new Image($image['id_image']);
					$ps_img_path = _PS_PROD_IMG_DIR_.$obj_image->getImgPath().'.jpg';
					ImageManager::resize($ps_img_path, $mp_img_path.$rand_name.'.jpg');
				}
			}

			//map in mp shop product table
			$obj_mpshop_product->id_shop = $id_mp_shop;
			$obj_mpshop_product->marketplace_seller_id_product = $id_mp_product;
			$obj_mpshop_product->id_product = $id_product;
			$obj_mpshop_product->save();
			$id_mp_product_mapped = $obj_mpshop_product->id;

			if ($id_mp_product_mapped)
			{
				Hook::exec('actionAfterAssignProduct', array('id_seller' => $id_seller,
															'id_product' => $id_product,
															'mp_id_product' => $id_mp_product));
				return $id_mp_product;
			}
		}

		return false;
	}
	
	//@id_order get marketplace product details of seller shop
	public function getMarketPlaceProductDetailOfSellerShop($id_order)
	{
		$order_info = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT * FROM `'._DB_PREFIX_.'order_detail` od
										JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = od.product_id)
										JOIN `'._DB_PREFIX_.'product_shop` ps
										ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
										JOIN `'._DB_PREFIX_.'marketplace_shop_product` msp ON (msp.`id_product` = p.`id_product`)
										WHERE od.`id_order` = '.$id_order.' ORDER BY p.`id_product`');
		if (!empty($order_info))
			return $order_info;

		return false;

	}
	
	//@id_product is the id_product from ps_product table
	public function getMarketPlaceShopProductDetail($id_product)
	{
		$marketplaceshopdetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_shop_product` WHERE id_product = ".(int)$id_product);
		if (!empty($marketplaceshopdetail))
			return $marketplaceshopdetail;
			
		return false;
	}
	
	/**
	 * [getMarketPlaceShopProductDetailBYmspid return if product is activated at least one time]
	 * @param  [int] $id [mp product id]
	 * @return [array/false]
	 */
	public function getMarketPlaceShopProductDetailBYmspid($id_mp_product) 
	{
		$mp_shopproduct = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("SELECT * FROM `"._DB_PREFIX_."marketplace_shop_product`
							WHERE marketplace_seller_id_product = ".$id_mp_product);
		if (!empty($mp_shopproduct)) 
			return $mp_shopproduct;
		return false;
	}
	
	//@id is marketplace product id
	public function getMarketPlaceProductInfo($id) {
		$marketplaceproductinfo = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("select * from `" . _DB_PREFIX_ ."marketplace_seller_product` where id =".$id);
		
		if(!empty($marketplaceproductinfo)) {
			return $marketplaceproductinfo;
		} else {
			return false;
		}
	}
	
	//@id is marketplace product id
	public function getMarketPlaceProductCategories($id){
		$seller_product_categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("SELECT `id_category` FROM `" . _DB_PREFIX_ ."marketplace_seller_product_category` where id_seller_product =".$id);
		
		if(!empty($seller_product_categories)) {
			return $seller_product_categories;
		} else {
			return false;
		}
	}
	
	/**
	 * where $id_shop is marketplace shop id
	 * @param  [type] $id_shop [description]
	 * @return [type]          [description]
	 */
	public function getMarketPlaceShopDetail($id_shop) {
		$marketplaceshopdetail = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow("select * from `" . _DB_PREFIX_ . "marketplace_shop` where id =".$id_shop);
		
		if(!empty($marketplaceshopdetail)) {
			return $marketplaceshopdetail;
		} else {
			return false;
		}
	}
	
	public function unactiveImage($id) 
	{
		$unactive_image = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."marketplace_product_image`
											WHERE seller_product_id = ".$id." AND active = 0");
		if (!empty($unactive_image))
			return $unactive_image;
		return false;
	}
	
	public function getProductsByOrderId($id_order)
	{
	   $product_list = Db::getInstance()->ExecuteS("select `product_id`,`product_quantity`  from `"._DB_PREFIX_."order_detail` where id_order=".$id_order."");
	   
	   if($product_list)
	    return $product_list;
	   else
         return false;		   
	}
	
	public function checkProduct($id_product)
	{
		$mp_seller_id_product = Db::getInstance()->getValue("SELECT `marketplace_seller_id_product`
						FROM `"._DB_PREFIX_."marketplace_shop_product` WHERE id_product = ".(int)$id_product);

		if ($mp_seller_id_product)
			return $mp_seller_id_product;

		return false;		  
	}
	
	public function getSellerIdByProduct($mp_id_product)
	{
		$id_seller = Db::getInstance()->getValue("SELECT `id_seller` FROM `"._DB_PREFIX_."marketplace_seller_product`
										WHERE id = ".$mp_id_product);
		if ($id_seller)
			return $id_seller;

		return false;
	}
	
	
	public function getCustomerIdBySellerId($id)
	{
	  $customer_id = Db::getInstance()->getRow("select `id_customer`  from `"._DB_PREFIX_."marketplace_customer` where `marketplace_seller_id`=".$id."");
	  if($customer_id)
	   return $customer_id['id_customer'];
      else
       return false;		  
	}
	
	public function getSellerInfo($id)
	{
	  $customer_info = Db::getInstance()->getRow("select `firstname`,`lastname`,`email`  from `"._DB_PREFIX_."customer` where `id_customer`=".$id."");
	  if($customer_info)
	   return $customer_info;
	  else
       return false;		  
	  
	}
	public function getProductInfo($id)
	{
	 $product_info = Db::getInstance()->getRow("select `name`  from `"._DB_PREFIX_."product_lang` where `id_product`=".$id." and `id_lang`=1");
	 if($product_info)
	  return $product_info;
	 else
      return false;		 
	}
	
	public function getCustomerInfo($id)
	{
	  $customer_info = Db::getInstance()->getRow("select *  from `"._DB_PREFIX_."customer` where `id_customer`=".$id."");
	  return $customer_info;
	}
	
	public function getDeliverAddress($id)
    {
	  $delivery_address = Db::getInstance()->getRow("select `id_address_delivery`  from `"._DB_PREFIX_."orders` where `id_order`=".$id."");
	  return $delivery_address['id_address_delivery'];
    }	

   public function getShippingInfo($id)
   {
     $address = Db::getInstance()->getRow(" select * from `" . _DB_PREFIX_ . "address` where `id_address`=".$id."");
	 return $address;
   }	
   public function getState($id) 
   {
     $state = Db::getInstance()->getRow(" select `name` from `" . _DB_PREFIX_ . "state` where `id_state`=".$id."");
	 return $state['name'];
   }
   public function getCountry($id) 
   {
     $country = Db::getInstance()->getRow(" select `name` from `" . _DB_PREFIX_ . "country_lang` where `id_country`=".$id." and `id_lang`=1 ");
	 return $country['name'];
   }
	
	public function deleteMarketPlaceSellerProduct($id) {
		$is_delete = Db::getInstance()->Execute("DELETE from`"._DB_PREFIX_."marketplace_seller_product` where id=".$id);
		
		if($is_delete) {
			return true;
		} else {
			return false;
		}
	}

	public function findAllActiveSellerProductByLimit($start_point=0,$limit_point=8,$order_by='desc') {
		$seller_product = Db::getInstance()->executeS("select mpsp.*,msp.`id_product` as main_id_product from `". _DB_PREFIX_."marketplace_seller_product` mpsp join `". _DB_PREFIX_."marketplace_shop_product` msp on (mpsp.`id`=msp.`marketplace_seller_id_product`) join `" . _DB_PREFIX_ . "product` p on (msp.`id_product`=p.`id_product`) where mpsp.`active`=1 order by mpsp.`id` ".$order_by." limit ".$start_point.",".$limit_point);
			if(empty($seller_product)) {
				return false;
			} else {
				return $seller_product;
			}
	}

	/**
	 * [callMailFunction : mail when render action perform for product]
	 * @param  [type]  $mp_product_id [mp product id]
	 * @param  [type]  $subject       [mail subject]
	 * @param  boolean $mail_for      [1 active product, 2 deactive product, 3 delete product]
	 * @return [bolean]               [description]
	 */
	public function callMailFunction($mp_product_id, $subject, $mail_for = false)
	{	
		$id_lang = Configuration::get('PS_LANG_DEFAULT');

		$obj_mp_customer = new MarketplaceCustomer();
		$obj_seller_product = new SellerProductDetail($mp_product_id);

		if ($mail_for == 1)
			$mail_reason = 'activated';
		elseif ($mail_for == 2)
			$mail_reason = 'deactivated';
		elseif ($mail_for == 3)
			$mail_reason = 'deleted';
		else
			$mail_reason = 'activated';

		$product_name = $obj_seller_product->product_name;
		$id_category = $obj_seller_product->id_category;
		$ps_id_shop	= $obj_seller_product->ps_id_shop;
		$mp_id_shop	= $obj_seller_product->id_shop;
		$mp_id_seller = $obj_seller_product->id_seller;
		$quantity = $obj_seller_product->quantity;
		
		$obj_category = new Category($id_category, $id_lang);
		$category_name = $obj_category->name;

		$obj_seller = new SellerInfoDetail($mp_id_seller);
		$mp_seller_name = $obj_seller->seller_name;
		$business_email = $obj_seller->business_email;
		if ($business_email == '') 
		{
			$id_customer = $obj_mp_customer->getCustomerId($mp_id_seller);
			$obj_customer = new Customer($id_customer);
			$business_email = $obj_customer->email;
		}
		$obj_mp_shop = new MarketplaceShop($mp_id_shop);
		$mp_shop_name = $obj_mp_shop->shop_name;
		
		$obj_shop = new Shop($ps_id_shop);
		$ps_shop_name = $obj_shop->name;
		
		$temp_path = _PS_MODULE_DIR_.'marketplace/mails/';
		$templateVars = array(
							'{seller_name}' => $mp_seller_name,
							'{product_name}' => $product_name,
							'{mp_shop_name}' => $mp_shop_name,
							'{mail_reason}' => $mail_reason,
							'{category_name}' => $category_name,
							'{quantity}' => $quantity,
							'{ps_shop_name}' => $ps_shop_name
						);
		
		if($subject == 1) //Product Activated
		{
			Mail::Send($id_lang,
				'product_active',
				Mail::l('Product Activated', $id_lang),
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
		else if($subject == 2) //Product Deactivated
		{
			Mail::Send($id_lang,
				'product_active',
				Mail::l('Product Deactivated', $id_lang),
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

	public static function getProductList($id_seller, $orderby = false, $orderway = false, $p, $n)
	{
		if (!$orderby)
			$orderby = 'product_name';

		if (!$orderway)
			$orderway = 'asc';

		$product_list = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."marketplace_seller_product` mslp
							WHERE mslp.id_seller = ".$id_seller." ORDER BY mslp.`".$orderby."` ".$orderway."
							LIMIT ".(((int)$p - 1)*(int)$n).",".(int)$n);
		   
		if ($product_list)
			return $product_list;

		return false;
	}

	/**
	 * [findAllProductInMarketPlaceShop product with pagination]
	 * @param  [int]  $id_shop  [description]
	 * @param  [int]  $p        [page number]
	 * @param  [int]  $n        [Number of products per page]
	 * @param  boolean $orderby 
	 * @param  boolean $orderway
	 * @return [array]
	 */
	public function findAllActiveProductInMarketPlaceShop($id_shop, $p, $n, $orderby = false, $orderway = false)
	{
		if (!$orderby)
            $orderby = 'id';
        elseif ($orderby == 'name')
            $orderby = 'product_name';
//$orderby = 'product_name';
        if (!$orderway)
            $orderway = 'desc';

		$mp_shop_product = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."marketplace_shop_product` msp
							JOIN `"._DB_PREFIX_."marketplace_seller_product` mslp
							ON (msp.`marketplace_seller_id_product` = mslp.`id`)
							WHERE msp.`id_shop` =".$id_shop." AND  mslp.`active` = 1
							ORDER BY mslp.`".$orderby."` ".$orderway." LIMIT ".(((int)$p - 1)*(int)$n).",".(int)$n);
		if (!empty($mp_shop_product))
			return $mp_shop_product;
		return false;
	}

	public function findAllProductInMarketPlaceShop($id_shop,$orderby=false,$orderway=false)
	{
		if (!$orderby)
			$orderby = 'product_name';

		if (!$orderway)
			$orderway = 'ASC';

		$mp_shop_product = Db::getInstance()->executeS("SELECT * FROM `"._DB_PREFIX_."marketplace_shop_product` msp
			JOIN `"._DB_PREFIX_."marketplace_seller_product` mslp ON (msp.`marketplace_seller_id_product` = mslp.`id`)
			WHERE msp.`id_shop` =" . $id_shop . " order by mslp.`".$orderby."` ".$orderway);
			
		if( !empty($mp_shop_product))
				return $mp_shop_product;
		return false;
	}

	public function getCustomerIdByMpIdProduct($mp_product_id)
	{
		$result = false;
		if ($mp_product_id)
		{
			$customer_id = DB::getInstance()->getValue('SELECT `id_customer` FROM `'._DB_PREFIX_.'marketplace_seller_product` AS mpsp JOIN `'._DB_PREFIX_.'marketplace_customer` AS mc WHERE mpsp.`id_seller` = mc.`marketplace_seller_id` AND mpsp.`id` = '.(int)$mp_product_id);
			if ($customer_id)
				$result = $customer_id;
		}
		return $result;
	}

	public function changeSellerProductStatusBySellerProductId($seller_id_product, $status)
	{
		return Db::getInstance()->update('marketplace_seller_product', array('active' => $status),'id='.$seller_id_product);
	}
}
?>