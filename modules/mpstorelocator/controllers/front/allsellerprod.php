<?php
class mpstorelocatorallsellerprodModuleFrontController extends ModuleFrontController
{
	public function initContent()
    {
		parent::initContent();

		if (Configuration::get('MP_STORE_ALL_SELLER')) {
			$id_lang = $this->context->language->id;
			$seller_stores = StoreLocator::getAllStore(true);
			if ($seller_stores) {
				// get store location details			
				foreach ($seller_stores as $key => $store) {
					$obj_country = new Country($store['country_id'], $id_lang);
					$obj_state = new State($store['state_id']);
					$seller_stores[$key]['country_name'] = $obj_country->name;
					$seller_stores[$key]['state_name'] = $obj_state->name;

					if(file_exists(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg')) {
						$seller_stores[$key]['img_exist'] = 1;
		            } else {
		            	$seller_stores[$key]['img_exist'] = 0;
		            }
				}

				$this->context->smarty->assign('manage_status', Configuration::get('MP_STORE_LOCATION_ACTIVATION'));
				$this->context->smarty->assign('store_locations', $seller_stores);
			}

			$this->context->smarty->assign('title_text_color', Configuration::get('MP_TITLE_TEXT_COLOR'));
			$this->context->smarty->assign('title_bg_color', Configuration::get('MP_TITLE_BG_COLOR'));
			
			$this->setTemplate('allsellerprod.tpl');
		}
	}

	public function setMedia()
	{
		parent::setMedia();

		$this->addCSS(_MODULE_DIR_.'marketplace/views/css/marketplace_account.css');
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/store_details.css');
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.js');
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/js/tinyscrollbar/tinyscroll.css');
		$this->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places");
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/storedetails.js');
	}
}
?>