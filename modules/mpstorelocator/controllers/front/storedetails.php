<?php
class mpstorelocatorstoredetailsModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
		parent::initContent();

		$id_product = Tools::getValue('id_product');
		$stores = array();
		if ($id_product)
		{
			$product_stores = StoreProduct::getProductStore($id_product, true);
			if ($product_stores)
			{
				//store list
				foreach ($product_stores as $value)
					$stores[] = StoreLocator::getStoreById($value['id_store']);
			}
			$obj_mpproduct = new SellerProductDetail();
			$mp_product = $obj_mpproduct->getMarketPlaceShopProductDetail($id_product);
			$mp_id_shop = $mp_product['id_shop'];
			if ($mp_id_shop)
			{
				$all_products = $obj_mpproduct->findAllProductInMarketPlaceShop($mp_id_shop);
				$id_seller = $all_products[0]['id_seller'];

				if ($all_products)
				{
					foreach($all_products as $key => $product)
					{
						$obj_product = new Product($product['id_product'], false, $id_lang);
						$all_products[$key]['product_name'] = $obj_product->name;
					}
					$this->context->smarty->assign('all_products', $all_products);

					// get store location details
					$sellers = array();
					foreach ($stores as $key => $store)
					{
						$obj_country = new Country($store['country_id'], $id_lang);
						$obj_state = new State($store['state_id']);
						$obj_mpseller = new SellerInfoDetail($store['id_seller']);
						$stores[$key]['country_name'] = $obj_country->name;
						$stores[$key]['state_name'] = $obj_state->name;
						$sellers[$store['id_seller']]= $obj_mpseller->seller_name;
					}

					/*@info::$seller is like array('id_seller' => 'seller_name') for removing repeatition*/
					$this->context->smarty->assign('sellers',$sellers);
					if (count($stores))
						$this->context->smarty->assign('store_locations', $stores);
					$this->context->smarty->assign('active_product_id', $id_product);
					$this->setTemplate('storedetails.tpl');
				}
			}
		}
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/store_details.css');
		$this->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places");
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/storedetails.js');
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.js');
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.css');
	}
}
?>