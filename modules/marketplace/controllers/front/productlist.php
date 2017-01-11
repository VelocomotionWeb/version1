<?php
class MarketplaceProductListModuleFrontController extends ModuleFrontController 
{
	public function initContent() 
	{
		parent::initContent();
		$link = new Link();
		$id_lang = $this->context->cookie->id_lang;
		if (isset($this->context->cookie->id_customer))
		{
			$id_customer = $this->context->cookie->id_customer;
			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$obj_mp_customer = new MarketplaceCustomer();
				$obj_mp_shop = new MarketplaceShop();

				//delete selected checkbox process
				if ($selected_products = Tools::getValue('mp_product_selected'))
					$this->deleteSelectedProducts($selected_products);

				if (Tools::getValue('mp_product_status'))
					$this->changeProductStatus();

				$mp_customer = $obj_mp_customer->findMarketPlaceCustomer($id_customer);
				if ($mp_customer && $mp_customer['is_seller'] == 1)
				{
					$mp_seller_id   = $mp_customer['marketplace_seller_id'];
					$mp_shop = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
					if ($mp_shop)
					{
						//if product deleted completed by seller
						if(Tools::getIsset('deleted'))
							$this->context->smarty->assign("deleted", 1);

						//if product status updated by seller
						if(Tools::getIsset('status_updated'))
							$this->context->smarty->assign("status_updated", 1);
						
						$product_list = SellerProductDetail::getMpSellerProductDetails($mp_seller_id);
						if ($product_list)
							$product_list = $this->getProductDetails($product_list);


						$imageediturl = $link->getModuleLink('marketplace','productimageedit');
						$this->context->smarty->assign('products_status', Configuration::get('MP_SELLER_PRODUCTS_SETTINGS'));
						$this->context->smarty->assign('imageediturl',$imageediturl);
						$this->context->smarty->assign('product_lists', $product_list);
						$this->context->smarty->assign('is_seller', $mp_customer['is_seller']);
						$this->context->smarty->assign('logic', 3);
						$this->context->smarty->assign('id_lang', $id_lang);
						$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
						$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
						$this->setTemplate('productlist.tpl');
					}
				}
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('marketplace', 'productlist')));
	}

	public function changeProductStatus()
	{
		$id_product = Tools::getValue('id_product');
		$obj_mp_shop_product = new MarketplaceShopProduct();
		$mp_product_id = $obj_mp_shop_product->getMpProductIdByPsProductId($id_product);
		if ($mp_product_id)
		{
			Hook::exec('actionBeforeToggleProductStatus', array('mp_product_id' => $mp_product_id));
			if (!count($this->errors))
			{
				$obj_mp_product = new SellerProductDetail($mp_product_id);
				if ($obj_mp_product->active)
				{
					$obj_mp_product->active = 0;
					$obj_mp_product->save();
					$product = new Product($id_product);
					$product->active = 0;
					$product->save();
				}
				else
				{
					$obj_mp_product->active = 1;
					$obj_mp_product->save();
					$image_dir = _PS_MODULE_DIR_.'marketplace/views/img/product_img';
					$obj_mp_product->updatePsProductByMarketplaceProduct($mp_product_id, $image_dir, 1, $id_product);
					Hook::exec('actionToogleProductStatusGlobal', array('mp_product_id' => $mp_product_id, 'active' => $obj_mp_product->active));
				}
				Hook::exec('actionToogleProductStatusNew', array('main_product_id' => $id_product, 'active' => $obj_mp_product->active));
				Tools::redirect($this->context->link->getModuleLink('marketplace', 'productlist', array('status_updated' => 1)));
			}
		}
	}

	public function deleteSelectedProducts($id_products)
	{
		$mp_delete = true;
		$obj_mpproduct = new SellerProductDetail();
		foreach ($id_products as $mp_id_product)
		{
			if(!$obj_mpproduct->deleteMpProduct($mp_id_product))
				$mp_delete = false;
			else
			{
				$obj_mpproduct = new SellerProductDetail($mp_id_product);
				$obj_mpproduct->delete();
			}
		}

		if ($mp_delete)
			Tools::redirect($this->context->link->getModuleLink('marketplace', 'productlist', array('deleted' => 1)));
	}

	public function getProductDetails($productList)
	{
		$obj_mp_shop_product = new MarketplaceShopProduct();
		$obj_mp_product = new SellerProductDetail();
		$id_lang = $this->context->language->id;
		foreach ($productList as $key => $product)
		{
			$ps_product = $obj_mp_shop_product->findMainProductIdByMppId($product['id']);
			if ($ps_product) // if product activated
			{
				$obj_product = new Product($ps_product['id_product'], false, $id_lang);
				$cover = Product::getCover($ps_product['id_product']);

				if ($cover)
				{
					$obj_image = new Image($cover['id_image']);
					$productList[$key]['image_path'] = _THEME_PROD_DIR_.$obj_image->getExistingImgPath().'.jpg';
					$productList[$key]['cover_image'] = $ps_product['id_product'].'-'.$cover['id_image'];
				}

				$productList[$key]['id_product'] = $ps_product['id_product'];
				$productList[$key]['id_lang'] = $this->context->language->id;
				$productList[$key]['lang_iso'] = $this->context->language->iso_code;
				$productList[$key]['obj_product'] = $obj_product;
			}
			else //if product not active
			{
				$productList[$key]['price'] = Tools::convertPrice($product['price']); //convert price for multiple currency
				$unactive_image = $obj_mp_product->unactiveImage($product['id']);
				// product is unactive so by default first image is taken because no one is cover image
				if ($unactive_image)
					$productList[$key]['unactive_image'] = $unactive_image[0]['seller_product_image_id'];
			}
		}
		return $productList;
	}
	
	public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
        $this->addJS(_MODULE_DIR_.'marketplace/views/js/imageedit.js');
        //data table file included
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/datatable_bootstrap.css');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/jquery.dataTables.min.js');
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/dataTables.bootstrap.js');
    }
}
?>