<?php
class MarketplaceProductDetailsModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {
        parent::initContent();
		$link = new Link();
		$id_lang = $this->context->language->id;
		$this->context->smarty->assign('is_seller', 1);
		$obj_mp_shop = new MarketplaceShop();
		$obj_mp_sellerproduct = new SellerProductDetail();
		$obj_mp_shopproduct = new MarketplaceShopProduct();

		if (isset($this->context->cookie->id_customer)) 
		{
			$id_customer = $this->context->cookie->id_customer;
			if (MarketplaceCustomer::isCustomerActiveSeller($id_customer))
			{
				$mp_shop = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
				$id_shop = $mp_shop['id'];
				$this->context->smarty->assign('id_shop', $id_shop);
				$id_product = Tools::getValue('id');
				$product_info = $obj_mp_sellerproduct->getMarketPlaceProductInfo($id_product);
				if($product_info)
				{
					$id_product_info = $obj_mp_shopproduct->findMainProductIdByMppId($id_product);
					if ($id_product_info)
					{
						$id_product = $id_product_info['id_product'];
						$obj_product = new Product($id_product, false, $id_lang);
						$image_detail = $obj_product->getImages($id_lang);
						$product_link_rewrite = $obj_product->link_rewrite;

						if ($image_detail && !empty($image_detail))
							foreach($image_detail as $key => $image)
							{
								$obj_image = new Image($image['id_image']);
								$image_detail[$key]['image_path'] = _THEME_PROD_DIR_.$obj_image->getExistingImgPath().'.jpg';
								$image_detail[$key]['product_image'] = $id_product.'-'.$image['id_image'];
							}

						$this->context->smarty->assign('link_rewrite', $product_link_rewrite);
						$this->context->smarty->assign('img_info', $image_detail);
						$this->context->smarty->assign('id', $id_product);
						$this->context->smarty->assign('id_product', $id_product);	
						$this->context->smarty->assign('obj_product', $obj_product);	
						$this->context->smarty->assign('is_approve',1);
					}
					else //product not approved yet
					{
						$this->context->smarty->assign('is_approve', 0);
						$obj_mp_pro_image = new MarketplaceProductImage();
						$mp_pro_image = $obj_mp_pro_image->findProductImageByMpProId(Tools::getValue('id'));
						if($mp_pro_image)
						{
							$this->context->smarty->assign('mp_pro_image', $mp_pro_image);
							$cover_img = $mp_pro_image['0']['seller_product_image_id'];
							$this->context->smarty->assign('cover_img', $cover_img);
						}
						else
							$this->context->smarty->assign("mp_pro_image",'0');
					}

					$imageediturl = $link->getModuleLink('marketplace','productimageedit');	
					$this->context->smarty->assign('imageediturl',$imageediturl);
					$this->context->smarty->assign('product', $product_info);
					$this->context->smarty->assign("id_shop", $id_shop);
					$this->context->smarty->assign('logic', '999');
					$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
					$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
					$this->setTemplate('productdetails.tpl');
				}
			}
			else
				Tools::redirect($link->getModuleLink('marketplace', 'sellerrequest'));
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}
	
	public function setMedia()
    {
        parent::setMedia();
		$this->addJS(_MODULE_DIR_.'marketplace/views/js/imageedit.js');
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/product_details.css');
		$this->addJqueryPlugin(array('fancybox','tablednd'));
    }
}
?>