<?php
class AdminSellerInfoDetailController extends ModuleAdminController 
{
	public function __construct() 
	{
		$this->bootstrap = true;
		$this->table = 'marketplace_seller_info';
		$this->className = 'SellerInfoDetail';		
		
		$this->_join .= 'LEFT JOIN `'._DB_PREFIX_.'marketplace_customer` mpc ON (mpc.`marketplace_seller_id` = a.`id`)';
		$this->_select = 'mpc.`is_seller`, mpc.`id_customer`';
		$hook_res = Hook::exec('displayAdminSellerInfoJoin', array('flase' => 1));

		if ($hook_res)
		{
			$this->_join .= $hook_res;
			$hook_sel_res = Hook::exec('displayAdminSellerInfoSelect', array('flase' => 1));
			$this->_select .= $hook_sel_res;
		}
		
		$this->fields_list = array();
		$this->fields_list['id'] = array(
			'title' => $this->l('ID'),
			'align' => 'center',
			'class' => 'fixed-width-xs'
		);
		
		$this->fields_list['id_customer'] = array(
			'title' => $this->l('Id customer'),
			'align' => 'center',
			'callback' => 'checkCustomerId'
		);
		
		$this->fields_list['seller_name'] = array(
			'title' => $this->l('Seller Name')
		);
		
		$this->fields_list['business_email'] = array(
			'title' => $this->l('Business email')
		);
		
		$this->fields_list['shop_name'] = array(
			'title' => $this->l('Shop name')
		);
		
		$this->fields_list['phone'] = array(
			'title' => $this->l('Phone'),
			'align' => 'center'
		);
		
		$this->fields_list['date_add'] = array(
			'title' => $this->l('Registration'),
			'type' => 'date',
			'align' => 'text-right'
		);
		
		if ($hook_res)
		{	
			$this->fields_list['plan_name'] = array(
				'title' => $this->l('Plan Name'),
				'align' => 'center'
			);
		}
		
		$this->fields_list['is_seller'] =array(
				'title' => $this->l('Status'),
				'active' => 'status',
				'align' => 'center',
				'type' => 'bool',
				'orderby' => false
			);
		
		$this->identifier  = 'id';

		$this->bulk_actions = array(
								'delete' => array('text' => $this->l('Delete selected'),
													'icon' => 'icon-trash',
												 'confirm' => $this->l('Delete selected items?')),
								'enableSelection' => array(
													'text' => $this->l('Enable selection'),
													'icon' => 'icon-power-off text-success'),
								'disableSelection' => array(
										'text' => $this->l('Disable selection'),
										'icon' => 'icon-power-off text-danger'),
								);
		parent::__construct();
	}

	public function renderList() 
	{
		$this->addRowAction('edit');
		$this->addRowAction('view');
		$this->addRowAction('delete');

		$this->page_header_toolbar_btn['new'] = array(
			'href' => self::$currentIndex.'&add'.$this->table.'&token='.$this->token,
			'desc' => $this->l('Add new seller')
		);
		return parent::renderList();
	}

	public function postProcess() 
	{
		if (!$this->loadObject(true))
			return;
			
		$this->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
		if (version_compare(_PS_VERSION_, '1.6.0.11', '>'))
			$this->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
		else
			$this->addJS(_PS_JS_DIR_.'tinymce.inc.js');

		$this->addJS(_MODULE_DIR_.'marketplace/libs/rateit/lib/jquery.raty.min.js');
		
		if (Tools::isSubmit('statusmarketplace_seller_info')) 
			$this->makeSellerPartner();

		parent::postProcess();
	}

	public function renderView()
	{
		$id_seller = Tools::getValue('id');
		$id_lang = $this->context->language->id;
		$obj_mp_cus = new MarketplaceCustomer();
		$id_customer = $obj_mp_cus->getCustomerId($id_seller);
		if ($id_customer)
		{
			$mp_customer = $obj_mp_cus->getMpCustomer($id_seller);
			
			$obj_customer = new Customer($id_customer);
			$obj_mpcustomerpayment = new MarketplaceCustomerPaymentDetail();
			$payment_detail = $obj_mpcustomerpayment->getPaymentDetailByCustomerId($id_customer);
			if ($payment_detail)
				$this->context->smarty->assign('payment_detail', $payment_detail);

			$obj_mpseller = new SellerInfoDetail();
			$mp_seller = $obj_mpseller->sellerDetail($id_seller);
			
			if ($mp_seller && is_array($mp_seller))
			{
				$gender = new Gender($obj_customer->id_gender, $id_lang);
				if ($gender)
					$this->context->smarty->assign('gender',$gender);

				//For default image
				$shopimagepath = SellerInfoDetail::getShopImageLink($id_seller);
				if ($shopimagepath)
					$this->context->smarty->assign('shopimagepath', $shopimagepath);

				$sellerimagepath = SellerInfoDetail::getSellerImageLink($id_seller);
				if ($sellerimagepath)
					$this->context->smarty->assign('sellerimagepath', $sellerimagepath);	
				
				// Review Details
				$avg_rating = Reviews::getSellerAvgRating($id_seller);
				if ($avg_rating)
					$this->context->smarty->assign('avg_rating',$avg_rating);
				
				if (empty($obj_customer->id))
					$this->context->smarty->assign('customer_id', 0);

				$mp_seller['mp_shop_rewrite'] = Tools::link_rewrite($mp_seller['shop_name']);
				$mp_seller['is_seller'] = $mp_customer['is_seller'];

				$this->context->smarty->assign('mp_seller', $mp_seller);
				$this->context->smarty->assign('modules_dir', _MODULE_DIR_);
			}
		}
		return parent::renderView();
	}

	public function renderForm() 
	{
		$selle_info = new SellerInfoDetail();

		//tinymce setup
		$this->context->smarty->assign(array('path_css' => _THEME_CSS_DIR_,
											'ad' => __PS_BASE_URI__.basename(_PS_ADMIN_DIR_),
											'autoload_rte' => true,
											'lang' => true,
											'iso' => $this->context->language->iso_code,
											'max_phone_digit' => Configuration::get('MP_PHONE_DIGIT')));

		$obj_mpcustomer = new MarketplaceCustomer();
		if ($this->display == 'add')
		{
			$customer_info = $obj_mpcustomer->getPsCustomerWhoseNotSeller();
			if ($customer_info)
				$this->context->smarty->assign('customer_info', $customer_info);

			$this->fields_form = array(
				'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'button'
				)
			);
		}
		elseif ($this->display == 'edit') 
		{
			if(Tools::getValue('id'))
				$mp_id_seller = Tools::getValue('id');
			else
				$mp_id_seller = Tools::getValue('mp_id_seller');
			
			$mp_seller_info = $selle_info->sellerDetail($mp_id_seller);
			
			$this->context->smarty->assign('edit', 1);
			$this->context->smarty->assign('mp_seller_info', $mp_seller_info);
			
			//For default image
			$shopimagepath = SellerInfoDetail::getShopImageLink($mp_id_seller);
			if ($shopimagepath)
				$this->context->smarty->assign('shopimagepath', $shopimagepath);

			$sellerimagepath = SellerInfoDetail::getSellerImageLink($mp_id_seller);
			if ($sellerimagepath)
				$this->context->smarty->assign('sellerimagepath', $sellerimagepath);

			$this->fields_form = array(
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'button'
				)
			);
		}
		return parent::renderForm();
	}
		
		
	public function processSave() 
	{
		$mp_id_seller = Tools::getValue('mp_id_seller');
		$shop_name = trim(Tools::getValue('shop_name'));
		$about_business = Tools::getValue('about_business');
		$person_name = trim(Tools::getValue('person_name'));
		$phone = trim(Tools::getValue('phone'));
		$fax = Tools::getValue('fax');
		$fb_id = Tools::getValue('fb_id');
		$tw_id = Tools::getValue('tw_id');
		$address = Tools::getValue('address');
		$business_email_id = Tools::getValue('business_email_id');
		$mp_seller_admin_approve = Configuration::get('MP_SELLER_ADMIN_APPROVE');
		if ($mp_seller_admin_approve == 0)
			$active = 1;
		else
			$active = 0;
		if (!$mp_id_seller) //if add the seller
		{
			$id_customer = Tools::getValue('shop_customer');
			if (!$id_customer)
				$this->errors[] = Tools::displayError($this->l('Customer is required field'));
		}

		if ($shop_name == '')
			$this->errors[] = Tools::displayError($this->l('Shop name is requried field.'));
		else if (!Validate::isCatalogName($shop_name))
			$this->errors[] = Tools::displayError($this->l('Invalid shop name.'));
		else if (SellerInfoDetail::isShopNameExist($shop_name, $mp_id_seller))
			$this->errors[] = Tools::displayError($this->l('Shop name already taken. Try another.'));

		if ($person_name == '')
			$this->errors[] = Tools::displayError($this->l('Seller name is requried field.'));
		else if (!Validate::isName($person_name))
			$this->errors[] = Tools::displayError($this->l('Invalid seller name.'));

		if (!Validate::isEmail($business_email_id))
			$this->errors[] = Tools::displayError($this->l('Invalid email ID.'));
		else if (SellerInfoDetail::isSellerEmailExist($business_email_id, $mp_id_seller))
			$this->errors[] = Tools::displayError($this->l('Email ID already exist.'));
		
		if ($phone == '')
			$this->errors[] = Tools::displayError($this->l('Phone is requried field and must be numeric.'));
		else if(!Validate::isPhoneNumber($phone))
			$this->errors[] = Tools::displayError($this->l('Phone number must be numeric.'));

		if ($_FILES['shop_logo'])
			$this->validateMpImages($_FILES['shop_logo']);

		if ($_FILES['seller_logo'])
			$this->validateMpImages($_FILES['seller_logo']);
		
		if (empty($this->errors))
		{
			if ($mp_id_seller) // if edit
			{
				$obj_seller_info = new SellerInfoDetail($mp_id_seller);
				$obj_seller_info->business_email = $business_email_id;
				$obj_seller_info->seller_name = $person_name;
				$obj_seller_info->shop_name = $shop_name;
				$obj_seller_info->phone = $phone;
				$obj_seller_info->fax = $fax;
				$obj_seller_info->address = $address;
				$obj_seller_info->about_shop = $about_business;
				$obj_seller_info->facebook_id = $fb_id;
				$obj_seller_info->twitter_id = $tw_id;
				$obj_seller_info->active = $active;
				$obj_seller_info->save();

				//Update marketplace shop
				$shop_name_rewrite = Tools::link_rewrite($shop_name);
				$obj_mpcustomer = new MarketplaceCustomer();
				$id_customer = $obj_mpcustomer->getCustomerId($mp_id_seller);
				
				//update marketplace shop table
				$obj_mpshop = new MarketplaceShop();
				$mp_shop = $obj_mpshop->getMarketPlaceShopInfoByCustomerId($id_customer);
				if ($mp_shop)
				{
					$obj_mpshop = new MarketplaceShop($mp_shop['id']);
					$obj_mpshop->shop_name = $shop_name;
					$obj_mpshop->link_rewrite = $shop_name_rewrite;
					$obj_mpshop->id_customer = $id_customer;
					$obj_mpshop->about_us = $about_business;
					$obj_mpshop->save();
				}

				// if shop name changed, rename the shop image name also
				$prev_shop_name = Tools::getValue('pre_shop_name');
				if ($prev_shop_name != $shop_name)
				{
					$new_image_name = $mp_id_seller.'-'.$shop_name.'.jpg';
					$old_image_name = $mp_id_seller.'-'.$prev_shop_name.'.jpg';
					$old_image_file = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$old_image_name;
					if (file_exists($old_image_file))
					{
						$dir = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/';
						rename($dir.$old_image_name, $dir.$new_image_name);
					}
				}
				
				if ($_FILES['shop_logo'])
				{
					if (!$_FILES['shop_logo']['error'])
					{
						//delete prev image, than upload new
						$dir = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/';
						$prev_shop_name = Tools::getValue('pre_shop_name');
						$old_image_name = $mp_id_seller.'-'.$prev_shop_name.".jpg";
						if (file_exists($dir.$old_image_name))
							unlink($dir.$old_image_name);

						$shop_path = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$mp_id_seller.'-'.$shop_name.'.jpg';
						$width = 200;
						$height = 200;
						ImageManager::resize($_FILES['shop_logo']['tmp_name'], $shop_path, $width, $height);
					}
				}

				if ($_FILES['seller_logo'])
				{
					if (!$_FILES['seller_logo']['error'])
					{
						// upload seller logo
						$seller_path = _PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$mp_id_seller.'.jpg';		
						ImageManager::resize($_FILES['seller_logo']['tmp_name'], $seller_path, 200, 200);
					}
				}

				Hook::exec('actionUpdateshopExtrafield', array('marketplace_seller_id' => $mp_id_seller));
			}
			else // if add
			{
				$id_customer = Tools::getValue('shop_customer');
				$obj_seller_info = new SellerInfoDetail();
				$obj_seller_info->business_email = $business_email_id;
				$obj_seller_info->seller_name = $person_name;
				$obj_seller_info->shop_name = $shop_name;
				$obj_seller_info->phone = $phone;
				$obj_seller_info->fax = $fax;
				$obj_seller_info->address = $address;
				$obj_seller_info->about_shop = $about_business;
				$obj_seller_info->facebook_id = $fb_id;
				$obj_seller_info->twitter_id = $tw_id;
				$obj_seller_info->active = $active;
				$obj_seller_info->save();
				$id_seller = $obj_seller_info->id;

				$obj_mpcustomer = new MarketplaceCustomer();
				if (Configuration::get('MP_SELLER_ADMIN_APPROVE'))
					$obj_mpcustomer->insertMarketplaceCustomer($id_seller, $id_customer);
				else
				{
					// creating seller shop when admin setting is default
					$is_mpcustomer = $obj_mpcustomer->insertMarketplaceCustomer($id_seller, $id_customer, 1);
					if ($is_mpcustomer)
						$obj_seller_info->makeDefaultSellerPartner($id_seller);
				}
				
				$shop_path = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/'.$id_seller.'-'.$shop_name.'.jpg';
				$width = 200;
				$height = 200;
				ImageManager::resize($_FILES['shop_logo']['tmp_name'], $shop_path, $width, $height);

				$seller_path = _PS_MODULE_DIR_.'marketplace/views/img/seller_img/'.$id_seller.'.jpg';
				ImageManager::resize($_FILES['seller_logo']['tmp_name'], $seller_path, 200, 200);

				Hook::exec('actionAddshopExtrafield', array('marketplace_seller_id' => $id_seller));
			}
			if (Tools::isSubmit('submitAdd'.$this->table.'AndStay'))
			{
				if ($mp_id_seller)
					Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$mp_id_seller.'&update'.$this->table.'&conf=4&token='.$this->token);
				else
					Tools::redirectAdmin(self::$currentIndex.'&id='.(int)$id_seller.'&update'.$this->table.'&conf=3&token='.$this->token);
			}
			else
			{
				if ($mp_id_seller)
					Tools::redirectAdmin(self::$currentIndex.'&conf=4&token='.$this->token);
				else
					Tools::redirectAdmin(self::$currentIndex.'&conf=3&token='.$this->token);
			}
		}
		else
		{
			if ($mp_id_seller)
				$this->display = 'edit';
			else
				$this->display = 'add';
		}
	}

	public function validateMpImages($upload_logo)
	{
		if (!empty($upload_logo['name']))
        {
            if (!ImageManager::isCorrectImageFileExt($upload_logo['name']))
            {
                $this->errors[] = Tools::displayError('<strong>'.$upload_logo['name'].'</strong> : Image format not recognized, allowed formats are: .gif, .jpg, .png', false);
                return false;
            }
            else 
            {
                list($width, $height) = getimagesize($upload_logo['tmp_name']);
                if ($width == 0 || $height == 0)
                {
                    $this->errors[] = Tools::displayError('Invalid image size. Minimum image size must be 200X200.');
                    return false;
                }
                else if ($width < 200 || $height < 200)
                {
                    $this->errors[] = Tools::displayError('Invalid image size. Minimum image size must be 200X200.');
                    return false;
                }
                else
                    return true;
            } 
        }
        else
            return true;
	}

	public function makeSellerPartner($id_seller = false) 
	{
		if (!$id_seller)
			$id_seller = Tools::getValue('id');

		$obj_mpcustomer = new MarketplaceCustomer();
		$obj_seller_info = new SellerInfoDetail();
		$obj_mp_shop = new MarketplaceShop();

		$mp_seller = $obj_mpcustomer->getMpCustomer($id_seller);
		if ($mp_seller)
		{
			$is_seller = $mp_seller['is_seller'];
			$id_customer = $mp_seller['id_customer'];

			if ($is_seller == 0) // seller is deactive, make it active
			{
				$is_seller_active = $obj_mpcustomer->changeMpSellerStatus($id_seller, 1);
				if ($is_seller_active) 
				{
					$is_shop_created = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
					if($is_shop_created) // if shop is already created
					{
						//enable shop
						$is_shop_active = $obj_mp_shop->changeMpShopStatus($id_customer, 1);
						if ($is_shop_active)
						{
							// change product status
							$mp_id_shop = $is_shop_created['id'];
							$this->changeSellerProductStatus($mp_id_shop, 1);
						
							// activation mail to seller
							$obj_seller_info->callMailFunction($id_seller, 1, 1);
						}
					}
					else // first time active
					{
						$obj_seller = new SellerInfoDetail($id_seller);
						$obj_mp_shop->shop_name = pSQL($obj_seller->shop_name);
						$shop_rewrite = Tools::link_rewrite($obj_seller->shop_name);
						$obj_mp_shop->link_rewrite = pSQL($shop_rewrite);
						$obj_mp_shop->id_customer = $mp_seller['id_customer'];
						$obj_mp_shop->about_us = pSQL(trim($obj_seller->about_shop));
						$obj_mp_shop->is_active = 1;
						$obj_mp_shop->save();
						$mp_shop_id = $obj_mp_shop->id;
						if($mp_shop_id)	
						{
							Hook::exec('actionActiveSellerPlan', array('mp_id_seller' => $id_seller));
							$obj_seller_info = new SellerInfoDetail();
							$obj_seller_info->callMailFunction($id_seller, 1, 1);
						}
						else 
						{
							$obj_mpcustomer->changeMpSellerStatus($id_seller, 0);
							Tools::displayError($this->l('Some error occurs'));
						}
					}
				}
				else
					Tools::displayError($this->l('Some error occurs'));
			}
			else // seller is active, make it deactive
			{
				$is_seller_inactive = $obj_mpcustomer->changeMpSellerStatus($id_seller, 0);
				if($is_seller_inactive)
				{
					//disable shop
					$is_shop_inactive = $obj_mp_shop->changeMpShopStatus($id_customer, 0);

					if ($is_shop_inactive)
					{
						$is_shop_created = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($id_customer);
						$mp_id_shop = $is_shop_created['id'];
						$this->changeSellerProductStatus($mp_id_shop, 0);
						$obj_seller_info->callMailFunction($id_seller, 2, 2);
					}
				}
				else
					Tools::displayError($this->l('Some error occurs'));
			}
			Hook::exec('actionSellerProfileStatus', array('mp_id_seller' => $id_seller,'is_seller' => $is_seller));
		}
		else
			Tools::displayError($this->l('Some error occurs'));
	}

	public function changeSellerProductStatus($mp_id_shop, $active = false)
	{
		$obj_mpproduct = new SellerProductDetail();
		$obj_mpshopproduct = new MarketplaceShopProduct();

		// active seller product
		if ($active)
			$obj_mpproduct->changeSellerProductStatus($mp_id_shop, 1);
		else
			$obj_mpproduct->changeSellerProductStatus($mp_id_shop, 0);

		$seller_products = $obj_mpshopproduct->getSellerPsIdProduct($mp_id_shop);

		if ($seller_products)
		{
			foreach ($seller_products as $product) 
			{
				$obj_product = new Product($product['id_product']);
				$obj_product->active = $active ? 1 : 0;
				$obj_product->save();
			}
		}
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
		$obj_mpcustomer = new MarketplaceCustomer();
		if ($status == 1)
		{
			if (is_array($this->boxes) && !empty($this->boxes))
			{
				foreach ($this->boxes as $id)
				{
					$mp_seller = $obj_mpcustomer->getMpCustomer($id);
					if($mp_seller)
					{
						if($mp_seller['is_seller'] == 0)
							$this->makeSellerPartner($id);
					}
					else
						$this->active_seller_product($id);
				}
			}
		}
		elseif ($status == 0)
		{
			if (is_array($this->boxes) && !empty($this->boxes))
			{
				foreach ($this->boxes as $id)
				{
					$mp_seller = $obj_mpcustomer->getMpCustomer($id);
					if($mp_seller)
						if($mp_seller['is_seller'] == 1)
							$this->makeSellerPartner($id);
				}
			}
		}
	}

	public function ajaxProcessCheckUniqueShopName()
	{
		$shop_name = Tools::getValue('shop_name');
		$id_seller = Tools::getValue('id_seller');
		if ($shop_name)
		{
			if (SellerInfoDetail::isShopNameExist($shop_name, $id_seller))
				echo 1;
			else
				echo 0;
		}
	}

	public function ajaxProcessCheckUniqueSellerEmail()
	{
		$seller_email = Tools::getValue('seller_email');
		$id_seller = Tools::getValue('id_seller');
		if ($seller_email)
		{
			if (SellerInfoDetail::isSellerEmailExist($seller_email, $id_seller))
				echo 1;
			else
				echo 0;
		}
	}

	public function checkCustomerId($id)
	{
		$customer = new Customer($id);
		if (!empty($customer->id))
			return $customer->id;
		else
			return '--';
	}
}
?>