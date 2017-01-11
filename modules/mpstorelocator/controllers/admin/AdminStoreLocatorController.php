<?php
class AdminStoreLocatorController extends ModuleAdminController 
{
	public function __construct()
	{
		$this->bootstrap = true;
		$this->table = 'store_locator';
		$this->className = 'StoreLocator';
		$this->list_no_link = true;
		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'marketplace_seller_info` mci ON (mci.`id` = a.`id_seller`)';
		$this->_select = 'mci.`seller_name` AS seller_name, mci.`business_email` AS seller_email';
		$this->fields_list = array(
				'id' => array(
						'title' => $this->l('ID'),
						'align' => 'center',
						'class' => 'fixed-width-xs'
				),
				'image' => array(
						'title' => $this->l('Logo'),
						'align' => 'center',
						'image' => 'store_logo',
						'search' => false
				),
				'seller_name' => array(
						'title' => $this->l('Seller Name'),
						'align' => 'center'
				),
				'seller_email' => array(
						'title' => $this->l('Seller Email'),
						'align' => 'center'
				),
				'name' => array(
						'title' => $this->l('Store Name'),
						'align' => 'center'
				),
				'active' => array(
						'title' => $this->l('Status'),
						'align' => 'center',
						'active' => 'status',
						'type' => 'bool'
				)
		);
		$this->identifier  = 'id';
		$this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'),
													  'icon' => 'icon-trash',
													  'confirm' => $this->l('Delete selected items?'))
									);
		parent::__construct();
	}

	public function renderList()
	{
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->context->smarty->assign('module_dir', _MODULE_DIR_);
		$this->page_header_toolbar_btn['new'] = array(
				'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
				'desc' => $this->l('Add new store')
			);
		return parent::renderList();
	}

	public function postProcess()
	{
		$this->addCSS(_MODULE_DIR_.'mpstorelocator/views/css/addstorelocation.css');	
		$submit_store = Tools::getValue('submit_store');
		$id_store = Tools::getValue('id_store');
		if ($id_store) //if edit
			$this->saveStoreLocation($id_store);
		else if ($submit_store)
			$this->saveStoreLocation();


		if (Tools::isSubmit('Arraystore_locator'))
			$this->changeStatus();

		return parent::postProcess();
	}

	public function changeStatus()
	{
		$id = Tools::getValue('id');
		$obj_store_locator = new StoreLocator();
		$current_status = $obj_store_locator->findStoreLocatorStatus($id);
		if ($current_status)
		{
			if ($current_status['active'] == 1)
				$status = 0;
			else
				$status = 1;
			Hook::exec('actionStoreLocationToggle', array('id_store' => $id, 'current_status' => $current_status['active']));
			$is_update = $obj_store_locator->activeStoreLocator($status,$id);
		}
		$redirect = self::$currentIndex.'&conf=4&token='.$this->token;
		$this->redirect_after = $redirect;
	}
 
	public function saveStoreLocation($id_store = false)
	{
		$id_seller = Tools::getValue('seller_name');
		$shop_name = Tools::getValue('shop_name');
		$street = Tools::getValue('street');
		$country_id = Tools::getValue('countries');
		$state_id = Tools::getValue('state');
		$city_name = Tools::getValue('city_name');
		$latitude = Tools::getValue('latitude');
		$longitude = Tools::getValue('longitude');
		$map_address = Tools::getValue('map_address');
		$phone = Tools::getValue('phone');
		$zip_code = Tools::getValue('zip_code');
		$store_products = Tools::getValue('store_products');
		$store_status = Tools::getValue('store_status');
		$map_address_text = Tools::getValue('map_address_text');
		$destine1 = Tools::getValue('destine1');
		$destine2 = Tools::getValue('destine2');
		$bic = Tools::getValue('bic');

		//List of assigned product of this store
		$products_list = Tools::getValue('products_list');
	    
		if ($id_seller == 0)
			$this->errors[] = Tools::displayError($this->l('Seller name is required'));

		if ($shop_name == '')
			$this->errors[] = Tools::displayError($this->l('Shop name is required'));
		else
		{
			if (!Validate::isGenericName($shop_name))
				$this->errors[] = Tools::displayError($this->l('Invalid shop name'));
		}

		if ($country_id == '')
			$this->errors[] = Tools::displayError($this->l('Country is required'));

		if ($city_name)
			if (!Validate::isCityName($city_name))
				$this->errors[] = Tools::displayError($this->l('Invalid city name'));

		if ($zip_code == '')
			$this->errors[] = Tools::displayError($this->l('Zip/Postal Code is required'));
		else
		{
			if (strlen($zip_code) > 12)
				$this->errors[] = Tools::displayError($this->l('Zip/Postal Code cannot be more than 12 digit'));
			elseif (!Validate::isPostCode($zip_code))
				$this->errors[] = Tools::displayError($this->l('Invalid zip code'));
		}

		if ($phone)
			if (!Validate::isPhoneNumber($phone))
				$this->errors[] = Tools::displayError($this->l('Invalid phone number'));

			
		if ($_FILES['store_logo']['size'] != 0)
		{
			list($shop_width, $shop_height) = getimagesize($_FILES['store_logo']['tmp_name']);
			if ($shop_width > 800 || $shop_height > 800 )
				$this->errors[] = Tools::displayError($this->l('File size must be less than 800 x 800 px.'));
			else
			{
				if ($_FILES['store_logo']['error'] == 0) 
				{
					if (!ImageManager::isCorrectImageFileExt($_FILES['store_logo']['name']))
						$this->errors[] = Tools::displayError($this->l('Invalid image extension. Only jpg, jpeg, gif file can be uploaded.'));
				}
			}
		}

		if (empty($this->errors))
		{
			if ($id_store)
			{
				$obj_store = new StoreLocator($id_store);
				//deleting the previous store products
				Db::getInstance()->delete('store_products', 'id_store = '.$id_store);
			}
			else
				$obj_store = new StoreLocator();

			$obj_store->name = $shop_name;
			$obj_store->id_seller = $id_seller;
			$obj_store->country_id = $country_id;
			$obj_store->state_id = $state_id;
			$obj_store->city_name = $city_name;
			$obj_store->street = $street;
			$obj_store->latitude = $latitude;
			$obj_store->longitude = $longitude;
			$obj_store->map_address = $map_address;
			$obj_store->map_address_text = $map_address_text;
			$obj_store->zip_code = $zip_code;
			$obj_store->destine1 = $destine1;
			$obj_store->destine2 = $destine2;
			$obj_store->bic = $bic;
			$obj_store->phone = $phone;
			$obj_store->active = $store_status;
			$obj_store->save();
			$id_insert = $obj_store->id;

			if ($id_insert)
			{
				$width = 50;
				$height = 50;
				$store_logo_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_insert .".jpg";
				if ($id_store) // if edit store
				{
					if ($_FILES['store_logo']['size'] != 0)
						ImageManager::resize($_FILES['store_logo']['tmp_name'], $store_logo_path, $width, $height);
				}
				else
				{
					if ($_FILES['store_logo']['size'] != 0)
						ImageManager::resize($_FILES['store_logo']['tmp_name'], $store_logo_path, $width, $height);
					else
					{
						$default_image_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/default.jpg';
						ImageManager::resize($default_image_path, $store_logo_path, $width, $height);
					}
				}

				// Save store products if provided
				if ($store_products)
				{
					foreach ($store_products as $id_products)
					{
						$obj_storeproduct = new StoreProduct();
						$obj_storeproduct->id_product = $id_products;
						$obj_storeproduct->id_store = $id_insert;
						$obj_storeproduct->add();
					}
				}
				$redirect = self::$currentIndex.'&conf=4&token='.$this->token;
				$this->redirect_after = $redirect;
			}
			else
				$this->errors[] = Tools::displayError($this->l('Some problem occured while updating records. Please try after some time.'));
		}
	}

	public function renderForm()
	{
		$link = new Link();
		$id_lang = $this->context->language->id;
		// get only active country(admin can manage it)
		$countries = Country::getCountries($id_lang, true);
		$autocomplete_link = $link->getModuleLink('mpstorelocator', 'frontautocomplete'); 
		$this->tpl_form_vars = array('countries' => $countries,
									'modules_dir' => _MODULE_DIR_,
									'autocomplete_link' => $autocomplete_link
									);
		$seller_info = Db::getInstance()->executeS('SELECT `id`,`seller_name` FROM `'._DB_PREFIX_.'marketplace_seller_info`');
		if ($seller_info)
			$this->tpl_form_vars['seller_info'] = $seller_info;

		// if edit store
		$id_store = Tools::getValue('id');
		
		$sql = 'SELECT * FROM '._DB_PREFIX_.'cms_lang cl
					INNER JOIN '._DB_PREFIX_.'cms c ON c.id_cms = cl.id_cms AND id_cms_category = 5 AND active = 1
					WHERE id_lang = '.$id_lang.' 
					ORDER BY position ';
		$destines = Db::getInstance()->executeS($sql);
		if ($id_store)
		{
			$obj_store = new StoreLocator();
			$store = $obj_store->getStoreById($id_store);
			if ($store)
			{
				$obj_country = new Country($store['country_id'], $id_lang);
	            $obj_state = new State($store['state_id']);
	            $store['country_name'] = $obj_country->name;
	            $store['state_name'] = $obj_state->name;
	            $store['products'] = Tools::jsonEncode(StoreProduct::getSellerProducts($id_store)); //jsonEncode bcz using this in js
	            //logo details
	            $this->context->smarty->assign('logo_path',_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$store['id'].'.jpg');
	            $this->context->smarty->assign('store', $store);
	            $this->context->smarty->assign('destines', $destines);
			}
		}

		$this->fields_form = array(
					'submit' => array(
						'title' => $this->l('Save'),
						'class' => 'button'
						)
					);
		return parent::renderForm();
	}

	public function ajaxProcessFilterStates()
	{
		$id_country = Tools::getValue('id_country');
        $states = State::getStatesByIdCountry((int)$id_country);
        if ($states)
        	$jsondata = Tools::jsonEncode($states);
        else
        	$jsondata = Tools::jsonEncode(array('failed'));
        die($jsondata);
	}

	public function ajaxProcessGetSellerProducts()
	{
		$id_seller = Tools::getValue('id_seller');
        $mp_products = StoreProduct::getMpSellerActiveProducts($id_seller);
        if ($mp_products)
        	$jsondata = Tools::jsonEncode($mp_products);
        else
        	$jsondata = Tools::jsonEncode(array('failed'));
        die($jsondata);
	}

	public function ajaxProcessDeleteStoreLogo()
	{
		$id_store = Tools::getValue('id_store');
		if(!@unlink(_PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_store.'.jpg'))
			$data = array('status' => 'failed', 'msg' => 'Error while deleting file.');
		else
		{
			//upload default image
			$store_logo_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/'.$id_store .".jpg";
			$default_image_path = _PS_MODULE_DIR_.'mpstorelocator/views/img/store_logo/default.jpg';
			ImageManager::resize($default_image_path, $store_logo_path, 50, 50);
			$data = array('status' => 'success', 'msg' => 'Image successfully deleted.');
		}

		die(Tools::jsonEncode($data));
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJS("https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=places");
		$this->addJS(_MODULE_DIR_.'mpstorelocator/views/js/addstorelocation.js');
	}
}
?>