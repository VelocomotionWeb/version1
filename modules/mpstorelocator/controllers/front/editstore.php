<?php
class mpstorelocatoreditstoreModuleFrontController extends ModuleFrontController
{
	public function postProcess()
	{
		if(Tools::isSubmit('update_store_submit'))
		{
			//Delete Store Logo
			$id_delete_logo = Tools::getValue('id_delete_logo');
			if ($id_delete_logo)
			{
				if(!@unlink(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_delete_logo.'.jpg'))
					$delete_logo_msg = 2;
				else
				{
					//upload default image
					$store_logo_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_delete_logo .".jpg";
					$default_image_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/default.jpg';
					ImageManager::resize($default_image_path, $store_logo_path, 50, 50);
					$delete_logo_msg = 1;
				}

				if (isset($delete_logo_msg) && $delete_logo_msg)
				{
					$param = array('delete_logo_msg' => $delete_logo_msg);
					$addstore_link = $this->context->link->getModuleLink('mpstorelocator', 'sellerstores', $param);
					Tools::redirect($addstore_link);
				}
			}		
			$id_seller = Tools::getValue('id_seller');
			$shop_name = Tools::getValue('shop_name');
			$country_id = Tools::getValue('countries');
			$state_id = Tools::getValue('state');
			$city_name = Tools::getValue('city_name');
			$street = Tools::getValue('street');
			$latitude = Tools::getValue('latitude');
			$longitude = Tools::getValue('longitude');
			$map_address = Tools::getValue('map_address');
			$zip_code = Tools::getValue('zip_code');
			$phone = Tools::getValue('phone');
			$store_products = Tools::getValue('store_products');
			$id_store = Tools::getValue('id_store');
			$store_status = Tools::getValue('store_status');
			$map_address_text = Tools::getValue('map_address_text');

			// Validation
			if ($shop_name == '')
				$this->errors[] = Tools::displayError('Store name is required.');
			elseif (!Validate::isGenericName($shop_name))
				$this->errors[] = Tools::displayError('Invalid Store name.');
			elseif($street == '')
				$this->errors[] = Tools::displayError('Street is required.');
			elseif (!Validate::isAddress($street))
				$this->errors[] = Tools::displayError('Invalid Street address.');
			elseif ($city_name == '')
				$this->errors[] = Tools::displayError('City is required.');
			elseif (!Validate::isCityName($city_name))
				$this->errors[] = Tools::displayError('Invalid City  name.');
			elseif (!$country_id)
				$this->errors[] = Tools::displayError('Country is required.');
			elseif ($zip_code == '')
				$this->errors[] = Tools::displayError('Zip/Postal code is required.');
			elseif (!Validate::isPostCode($zip_code))
				$this->errors[] = Tools::displayError('Invalid Zip/Postal Code.');
			elseif (strlen($zip_code) > 12)
				$this->errors[] = Tools::displayError('Zip/Postal Code length can not be more that 12.');
			

			if ($_FILES['store_logo']['size'] != 0)
			{
				list($shop_width, $shop_height) = getimagesize($_FILES['store_logo']['tmp_name']);
				if ($shop_width > 800 || $shop_height > 800 )
					$this->errors[] = Tools::displayError('Invalid file size. File size must be of 800 x 800.'); //'File size must be of 800 x 800.';
				else
				{
					if ($_FILES['store_logo']['error'] == 0)
						if (!ImageManager::isCorrectImageFileExt($_FILES['store_logo']['name']))
							$this->errors[] = Tools::displayError('Invalid image extention.'); //'Only "jpg","jpeg","gif" and "gif" file can be uploaded.';
				}
			}

			if (!count($this->errors))
			{
				$obj_store = new StoreLocator($id_store);
					//deleting the previous store products
				Db::getInstance()->delete('store_products', 'id_store = '.$id_store);
				$obj_store->name = $shop_name;
				$obj_store->id_seller = $id_seller;
				$obj_store->street = $street;
				$obj_store->city_name = $city_name;
				$obj_store->country_id = $country_id;
				$obj_store->state_id = $state_id;
				$obj_store->latitude = $latitude;
				$obj_store->longitude = $longitude;
				$obj_store->map_address = $map_address;
				$obj_store->map_address_text = $map_address_text;
				$obj_store->zip_code = $zip_code;
				$obj_store->phone = $phone;

				if (Configuration::get('MP_STORE_LOCATION_ACTIVATION'))
					$obj_store->active = $store_status;
				else
					$obj_store->active = 0;

				$obj_store->save();
				$insert_id = $obj_store->id;

				if ($insert_id)
				{
					$width = 50;
					$height = 50;
					$upload_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$insert_id .".jpg";
					if ($id_store) // if edit store
					{
						if ($_FILES['store_logo']['size'] != 0)
							ImageManager::resize($_FILES['store_logo']['tmp_name'], $upload_path, $width, $height);
					}
					else
					{
						if ($_FILES['store_logo']['size'] != 0)
							ImageManager::resize($_FILES['store_logo']['tmp_name'], $upload_path, $width, $height);
						else
						{
							$default_image_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/default.jpg';
							ImageManager::resize($default_image_path, $upload_path, $width, $height);
						}
					}

					// Save store products if provided
					if ($store_products)
					{
						foreach ($store_products as $id_products)
						{
							$obj_storeproduct = new StoreProduct();
							$obj_storeproduct->id_product = $id_products;
							$obj_storeproduct->id_store = $insert_id;
							$obj_storeproduct->add();
						}
					}

					$param = array('success' => 2);
					Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'sellerstores', $param));
				}
				else
					$this->errors[] = Tools::displayError('Some problem error occured while updating records. Please try later.'); //'Some problem error occured while updating records.Please try after some time.';
			}
		}
	}
	public function initContent()
    {
    	parent::initContent();
		$id_lang = $this->context->language->id;
		$obj_mpcustomer = new MarketplaceCustomer();

		$error = Tools::getValue('error');
		$success = Tools::getValue('success');
		$id_store = Tools::getValue('id_store');

		if ($error)
			$this->context->smarty->assign('error', $error);

		if ($success)
			$this->context->smarty->assign('success', $success);

		if ($this->context->customer->isLogged())
        {
        	$id_customer = $this->context->customer->id;
			$seller = $obj_mpcustomer->findMarketPlaceCustomer($id_customer);
			if ($seller)
			{
				// delete store 
				$delete = Tools::getValue('delete');
				if ($delete)
				{
					$delete_prod = Db::getInstance()->delete('store_products', 'id_store = '.$id_store);
					$delete_store = Db::getInstance()->delete('store_locator', 'id = '.$id_store);
					if ($delete_prod && $delete_store)
						Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'sellerstores', array('deleted' => 1)));
					else
						Tools::redirect($this->context->link->getModuleLink('mpstorelocator', 'sellerstores', array('deleted' => 2)));
				}
				// close

				$obj_mpseller = new SellerInfoDetail($seller['marketplace_seller_id']);
				$id_seller = $seller['marketplace_seller_id'];
				$store = StoreLocator::getStoreById($id_store);
				if ($store)
				{
					$obj_country = new Country($store['country_id'], $id_lang);
		            $obj_state = new State($store['state_id']);
		            $store['country_name'] = $obj_country->name;
		            $store['state_name'] = $obj_state->name;
		            $store['products'] = StoreProduct::getSellerProducts($id_store);
		            $this->context->smarty->assign('store', $store);
				}
				
				$seller_name = $obj_mpseller->seller_name;
				$countries = Country::getCountries($id_lang, true);

				$mp_products = StoreProduct::getMpSellerActiveProducts($id_seller);
				if ($mp_products)
					$this->context->smarty->assign('mp_products', $mp_products);

				$this->context->smarty->assign('manage_status', Configuration::get('MP_STORE_LOCATION_ACTIVATION'));
				$this->context->smarty->assign('countries', $countries);
				$this->context->smarty->assign('id_customer', $id_customer);
				$this->context->smarty->assign('id_seller', $id_seller);
				$this->context->smarty->assign('seller_name', $seller_name);
				$this->setTemplate('editsellerstore.tpl');
			}
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('mpstorelocator', 'editstore')));
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/addstorelocation.css');
		$this->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places");
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/addstorelocation.js');
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/editstorelocation_new.js');
	}
}
?>