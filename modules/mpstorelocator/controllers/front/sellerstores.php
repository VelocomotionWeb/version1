<?php
class mpstorelocatorsellerstoresModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
		$id_customer = $this->context->customer->id;
		$link = new Link();
		parent::initContent();

		$success = Tools::getValue('success');
		$deleted = Tools::getValue('deleted');
		$delete_logo_msg = Tools::getValue('delete_logo_msg');
		if ($success)
			$this->context->smarty->assign('success', $success);

		if ($deleted)
			$this->context->smarty->assign('deleted', $deleted);

		if ($delete_logo_msg)
			$this->context->smarty->assign('delete_logo_msg', $delete_logo_msg);

		$obj_mpproduct = new SellerProductDetail();
		$obj_mpcustomer = new MarketplaceCustomer();

		if ($id_customer)
		{
			$mp_customer = $obj_mpcustomer->findMarketPlaceCustomer($id_customer);
			if ($mp_customer)
			{
				$id_seller = $mp_customer['marketplace_seller_id'];
				$seller_stores = StoreLocator::getSellerStore($id_seller);
				if ($seller_stores)
				{
					//get store products
					$mp_products = StoreProduct::getMpSellerActiveProducts($id_seller);
					if ($mp_products)
					{
						foreach($mp_products as $key => $product)
						{
							$obj_product = new Product($product['id_product'], false, $id_lang);
							$mp_products[$key]['product_name'] = $obj_product->name;
						}
						$this->context->smarty->assign('mp_products', $mp_products);
					}

					// get store location details
					$sellers = array();
					foreach ($seller_stores as $key => $store)
					{
						$obj_country = new Country($store['country_id'], $id_lang);
						$obj_state = new State($store['state_id']);
						$obj_mpseller = new SellerInfoDetail($store['id_seller']);
						$seller_stores[$key]['country_name'] = $obj_country->name;
						$seller_stores[$key]['state_name'] = $obj_state->name;
						$sellers[$store['id_seller']]= $obj_mpseller->seller_name;
					}

					/*@info::$sellers is like array('id_seller' => 'seller_name') for removing repeatition*/
					$this->context->smarty->assign('sellers',$sellers);
					$this->context->smarty->assign('manage_status', Configuration::get('MP_STORE_LOCATION_ACTIVATION'));
					$this->context->smarty->assign('store_locations', $seller_stores);
				}
				$this->setTemplate('sellerstores.tpl');
			}
			else
			Tools::redirect($link->getPageLink('index'));
		}
		else
			Tools::redirect($link->getPageLink('my-account'));
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/store_details.css');
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.js');
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.css');
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/sellerstores.js');
	}
}
?>