<?php
if (!defined('_PS_VERSION_'))
    exit;

require_once (dirname(__FILE__).'/classes/MarketplaceClassInclude.php');

class Marketplace extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    private $_html = '';
    private $_postErrors = array();
    public $seller_details_view = array(
        array('id_group' => 1, 'name' => 'Seller Name'),
        array('id_group' => 2, 'name' => 'Shop Name'),
        array('id_group' => 3, 'name' => 'Seller Email'),
        array('id_group' => 4, 'name' => 'Seller Phone'),
        array('id_group' => 5, 'name' => 'Social Profile'),
        array('id_group' => 6, 'name' => 'Seller Profile Page Link'),
        array('id_group' => 7, 'name' => 'Collection Page Link'),
        array('id_group' => 8, 'name' => 'Store Page Link'),
        array('id_group' => 9, 'name' => 'Contact Seller Link'),
        array('id_group' => 10, 'name' => 'Address'),
        array('id_group' => 11, 'name' => 'About Shop'),
        array('id_group' => 12, 'name' => 'Seller Rating')
    );

    public function __construct()
    {
        $this->name = 'marketplace';
        $this->tab = 'market_place';
        $this->version = '2.0.1';
        $this->author = 'Webkul';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Marketplace');
        $this->description = $this->l('Add customers as a seller');
        $this->confirmUninstall = $this->l('Are you sure? All module data will be lost after uninstalling the module');
        /*$customer_id = $this->context->customer->id;
		if ($customer_id == 163) $this->sellersOrderMail(36);*/
    }

    private function _postValidation()
    {
        if (Tools::isSubmit('btnSubmit'))
        {
            if (!Validate::isEmail(Tools::getValue('MP_SUPERADMIN_EMAIL')))
                $this->_postErrors[] = $this->l('Invalid Email Id.');
            if (!Validate::isInt(Tools::getValue('MP_PHONE_DIGIT')))
                $this->_postErrors[] = $this->l('Invalid Phone Digit.');
        }
    }

    private function _postProcess()
    {
        if (Tools::isSubmit('btnSubmit'))
        {
            Configuration::updateValue('MP_SUPERADMIN_EMAIL', Tools::getValue('MP_SUPERADMIN_EMAIL'));
            Configuration::updateValue('MP_SHOW_SELLER_DETAILS', Tools::getValue('MP_SHOW_SELLER_DETAILS'));
            Configuration::updateValue('MP_SELLER_ADMIN_APPROVE', Tools::getValue('MP_SELLER_ADMIN_APPROVE'));
            Configuration::updateValue('MP_PRODUCT_ADMIN_APPROVE', Tools::getValue('MP_PRODUCT_ADMIN_APPROVE'));
            Configuration::updateValue('MP_REVIEWS_ADMIN_APPROVE', Tools::getValue('MP_REVIEWS_ADMIN_APPROVE'));
            Configuration::updateValue('MP_PHONE_DIGIT', Tools::getValue('MP_PHONE_DIGIT'));
            Configuration::updateValue('MP_TITLE_BG_COLOR', Tools::getValue('MP_TITLE_BG_COLOR'));
            Configuration::updateValue('MP_TITLE_TEXT_COLOR', Tools::getValue('MP_TITLE_TEXT_COLOR'));
            Configuration::updateValue('MP_TERMS_AND_CONDITIONS_STATUS', Tools::getValue('MP_TERMS_AND_CONDITIONS_STATUS'));
            Configuration::updateValue('MP_TERMS_AND_CONDITIONS', Tools::getValue('MP_TERMS_AND_CONDITIONS'));
            Configuration::updateValue('MP_SHOW_ADMIN_COMMISSION', Tools::getValue('MP_SHOW_ADMIN_COMMISSION'));
            Configuration::updateValue('MP_SELLER_PRODUCTS_SETTINGS', Tools::getValue('MP_SELLER_PRODUCTS_SETTINGS'));

            // save seller details access details
            $loop_count = count($this->seller_details_view);
            $seller_details_access = Tools::getValue('groupBox');
            if ($seller_details_access && Configuration::get('MP_SHOW_SELLER_DETAILS'))
            {
                $indx = 0;
                for ($i = 1; $i <= $loop_count; $i++)
                {
                    if (array_key_exists($indx, $seller_details_access))
                    {
                        if ($seller_details_access[$indx] == $i)
                        {
                            $indx++;
                            Configuration::updateValue('MP_SELLER_DETAILS_ACCESS_'.$i, 1);
                        }
                        else
                            Configuration::updateValue('MP_SELLER_DETAILS_ACCESS_'.$i, 0);
                    }
                    else
                        Configuration::updateValue('MP_SELLER_DETAILS_ACCESS_'.$i, 0);
                }
            }
            else
            {
                Configuration::updateValue('MP_SHOW_SELLER_DETAILS', 0);
                for ($i = 1; $i <= $loop_count; $i++)
                {
                    Configuration::updateValue('MP_SELLER_DETAILS_ACCESS_'.$i, 0);
                }
            }
        }

        $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
        $module_config = $this->context->link->getAdminLink('AdminModules');
        Tools::redirectAdmin($module_config.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
    }

    public function getContent()
    {
        //using this variable on admin.js file
        Media::addJsDef(
                    array('terms_and_condision_status' => Configuration::get('MP_TERMS_AND_CONDITIONS_STATUS'),
                        'show_seller_details' => Configuration::get('MP_SHOW_SELLER_DETAILS'))
                );

        $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        $this->context->controller->addJs($this->_path.'views/js/admin.js');

        if (Tools::isSubmit('btnSubmit'))
        {
            $this->_postValidation();
            if (!count($this->_postErrors))
                $this->_postProcess();
            else
                foreach ($this->_postErrors as $err)
                    $this->_html .= $this->displayError($err);
        }
        else
            $this->_html .= '<br />';

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
    	// Get default language
    	$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form = array();
        $fields_form[0]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('SuperAdmin Email '),
                        'name' => 'MP_SUPERADMIN_EMAIL',
                        'hint' => $this->l('This email id will use for all marketplace mails')
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Seller phone maximum digit '),
                        'name' => 'MP_PHONE_DIGIT',
                        'hint' => $this->l('Ristrict the phone number maximum digit, a seller can enter')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show seller details'),
                        'name' => 'MP_SHOW_SELLER_DETAILS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('Display seller details on seller product page, seller store page and seller profile page.')
                    ),
                    array(
                        'type' => 'group',
                        'label' => $this->l('Customize details'),
                        'name' => 'groupBox',
                        'values' => $this->seller_details_view,
                        'col' => '6',
                        'form_group_class' => 'wk_mp_custom_seller_details',
                        'hint' => $this->l('Select the particular seller details you want to display on product page, seller store page and seller profile page.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show admin commission to seller '),
                        'name' => 'MP_SHOW_ADMIN_COMMISSION',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('Display admin commission to seller on add product page.')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Terms and conditions '),
                        'name' => 'MP_TERMS_AND_CONDITIONS_STATUS',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('Seller have to agree this terms and conditions while register.')
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('Write here '),
                        'name' => 'MP_TERMS_AND_CONDITIONS',
                        'rows' => '8',
                        'class' => 't',
                        'form_group_class' => 'wk_mp_termsncond'
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Seller can active and deactive their products'),
                        'name' => 'MP_SELLER_PRODUCTS_SETTINGS',
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l("Seller can enable and disable their products when seller's products are created in catelog.")
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
        );

		$fields_form[1]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Default Approval Setting'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Seller need to be approved by admin '),
                        'name' => 'MP_SELLER_ADMIN_APPROVE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('If No, all marketplace seller\'s request is automatically approved')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Product need to be approved by admin '),
                        'name' => 'MP_PRODUCT_ADMIN_APPROVE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('If No, all marketplace seller\'s product is automatically approved')
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Seller reviews to be approved by admin '),
                        'name' => 'MP_REVIEWS_ADMIN_APPROVE',
                        'required' => false,
                        'class' => 't',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                        'hint' => $this->l('If No, all marketplace seller\'s reviews are automatically approved')
                    )
				),
				'submit' => array(
                    'title' => $this->l('Save'),
                )
		);

		$fields_form[2]['form'] = array(
                'legend' => array(
                    'title' => $this->l('Theme Settings'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                     array(
                        'type' => 'color',
                        'label' => $this->l('Page Title Background Color '),
                        'name' => 'MP_TITLE_BG_COLOR',
                        'hint' => $this->l('Marketplace page title background color')
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Page Title Text Color '),
                        'name' => 'MP_TITLE_TEXT_COLOR',
                        'hint' => $this->l('Marketplace page title text color')
                    )
				),
				'submit' => array(
                    'title' => $this->l('Save'),
                )
		);

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->submit_action = 'btnSubmit';
        $helper->table = $this->table;
        $helper->identifier = $this->identifier;

        //Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        //$this->fields_form = array();
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        $config_vars = array(
            'MP_SUPERADMIN_EMAIL' => Tools::getValue('MP_SUPERADMIN_EMAIL', Configuration::get('MP_SUPERADMIN_EMAIL')),
            'MP_SHOW_SELLER_DETAILS' => Tools::getValue('MP_SHOW_SELLER_DETAILS', Configuration::get('MP_SHOW_SELLER_DETAILS')),
            'MP_SELLER_ADMIN_APPROVE' => Tools::getValue('MP_SELLER_ADMIN_APPROVE', Configuration::get('MP_SELLER_ADMIN_APPROVE')),
            'MP_PRODUCT_ADMIN_APPROVE' => Tools::getValue('MP_PRODUCT_ADMIN_APPROVE', Configuration::get('MP_PRODUCT_ADMIN_APPROVE')),
            'MP_REVIEWS_ADMIN_APPROVE' => Tools::getValue('MP_REVIEWS_ADMIN_APPROVE', Configuration::get('MP_REVIEWS_ADMIN_APPROVE')),
            'MP_PHONE_DIGIT' => Tools::getValue('MP_PHONE_DIGIT', Configuration::get('MP_PHONE_DIGIT')),
            'MP_TITLE_BG_COLOR' => Tools::getValue('MP_TITLE_BG_COLOR', Configuration::get('MP_TITLE_BG_COLOR')),
            'MP_TITLE_TEXT_COLOR' => Tools::getValue('MP_TITLE_TEXT_COLOR', Configuration::get('MP_TITLE_TEXT_COLOR')),
            'MP_TERMS_AND_CONDITIONS_STATUS' => Tools::getValue('MP_TERMS_AND_CONDITIONS_STATUS', Configuration::get('MP_TERMS_AND_CONDITIONS_STATUS')),
            'MP_TERMS_AND_CONDITIONS' => Tools::getValue('MP_TERMS_AND_CONDITIONS', Configuration::get('MP_TERMS_AND_CONDITIONS')),
            'MP_SHOW_ADMIN_COMMISSION' => Tools::getValue('MP_SHOW_ADMIN_COMMISSION', Configuration::get('MP_SHOW_ADMIN_COMMISSION')),
            'MP_SELLER_PRODUCTS_SETTINGS' => Tools::getValue('MP_SELLER_PRODUCTS_SETTINGS', Configuration::get('MP_SELLER_PRODUCTS_SETTINGS'))
        );
        for ($i = 1 ; $i <= count($this->seller_details_view); $i++)
            $config_vars['groupBox_'.$i] = Tools::getValue('MP_SELLER_DETAILS_ACCESS_'.$i, Configuration::get('MP_SELLER_DETAILS_ACCESS_'.$i));

        return $config_vars;
    }
    
    public function hookDisplayMpmenuhook()
    {
        $customer_id = $this->context->customer->id;
        $obj_marketplace_seller = new SellerInfoDetail();
        $mp_seller = $obj_marketplace_seller->getMarketPlaceSellerIdByCustomerId($customer_id);

        if ($mp_seller)
        {
            $is_seller = $mp_seller['is_seller'];
            if ($is_seller == 1) 
            {
                $obj_marketplace_shop = new MarketplaceShop();
                $market_place_shop = $obj_marketplace_shop->getMarketPlaceShopInfoByCustomerId($customer_id);
                $id_shop = $market_place_shop['id'];
                $obj_mpshop = new MarketplaceShop($id_shop);
                $name_shop = $obj_mpshop->link_rewrite;
                $this->context->smarty->assign("name_shop", $name_shop);
            }
            $this->context->smarty->assign("is_seller", $mp_seller['is_seller']);
        }
        else
            $this->context->smarty->assign("is_seller", -1);

        return $this->display(__FILE__, 'mpmenu.tpl');
    }
    
    public function hookDisplayMpmyaccountmenuhook()
    {
        $customer_id = $this->context->customer->id;
        
        $obj_marketplace_seller = new SellerInfoDetail();
        $mp_seller = $obj_marketplace_seller->getMarketPlaceSellerIdByCustomerId($customer_id);
        
        if ($mp_seller)
        {
            $is_seller = $mp_seller['is_seller'];
            if ($is_seller == 1) 
            {
                $obj_marketplace_shop = new MarketplaceShop();
                $market_place_shop = $obj_marketplace_shop->getMarketPlaceShopInfoByCustomerId($customer_id);
                $id_shop   = $market_place_shop['id'];
                $obj_mpshop = new MarketplaceShop($id_shop);
                $name_shop = $obj_mpshop->link_rewrite;
                $this->context->smarty->assign("id_shop", $id_shop);
                $this->context->smarty->assign("id_customer", $customer_id);
                $this->context->smarty->assign("name_shop", $name_shop);
            }
            $this->context->smarty->assign("is_seller", $mp_seller['is_seller']);
        }
        else
            $this->context->smarty->assign("is_seller", -1);

        return $this->display(__FILE__, 'mpmyaccountmenu.tpl');
    }
    
    public function hookDisplayMpOrderheaderlefthook()
    {
        return $this->display(__FILE__, 'orderheaderleft.tpl');
    }
    
    //product description hook
    public function hookDisplayMpproductdescriptionheaderhook()
    {
        return $this->display(__FILE__, 'productdetailheaderhook.tpl');
    }

    public function hookActionValidateOrder($params)
    {
        $id_order = $params['order']->id;
        $id_currency = $this->context->currency->id;
        $obj_mpsellerorderdetails = new MarketplaceSellerOrderDetails();

        $order_commission = $obj_mpsellerorderdetails->getOrderCommissionDetails($id_order);

        // if order commission not calculated
        if (!$order_commission)
        {
            // get cart order products, customer, seller details
            $seller_cart_products = $obj_mpsellerorderdetails->getSellerOrderedProductDetails($id_order);
            if ($seller_cart_products)
            {
                foreach ($seller_cart_products as $product)
                {
                    $obj_mpcommission = new MarketplaceCommision();
                    $obj_mpcommission->customer_id = $product['id_customer'];
                    $commission_by_seller = $obj_mpcommission->getCommissionRateBySeller();
                    if (!$commission_by_seller)
                    //apply global commission, if commission by particular seller not defined and if commission set to 0.00 no commission applied for this seller
                    {
                        if ($global_commission = Configuration::get('MP_GLOBAL_COMMISSION'))
                            $commission_rate = $global_commission;
                        else
                            $commission_rate = 0;
                    }
                    else
                        $commission_rate = $commission_by_seller;

                    // create seller order commission details
                    $admin_commision = (($product['total_price_tax_excl']) * $commission_rate) / 100;

                    //create seller amount, the rest amount from 100 after seller commission
                    $seller_amt = (($product['total_price_tax_excl']) * (100 - $commission_rate)) / 100;


                    //Distribution of product tax
                    $total_tax = $product['total_price_tax_incl']-$product['total_price_tax_excl'];

                    if (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'admin')
                        $admin_commision = $admin_commision + $total_tax;
                    else if (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'seller')
                        $seller_amt = $seller_amt + $total_tax;
                    else if (Configuration::get('MP_PRODUCT_TAX_DISTRIBUTION') == 'distribute_both')
                    {
                        $tax_to_admin = ($total_tax * $commission_rate) / 100; //for ex: 10% to admin
                        $tax_to_seller = $total_tax - $tax_to_admin; //the rest 90% to seller

                        $admin_commision = $admin_commision + $tax_to_admin;
                        $seller_amt = $seller_amt + $tax_to_seller;
                    }
                    //Distribution of product tax close

                    //update seller order, create if not exist
                    $id_seller_order = MarketplaceSellerOrders::updateMarketplaceSellerOrder($product, $admin_commision);

                    if ($id_seller_order)
                    {
                        $obj_mpsellerorderdetails->id_seller_order = $id_seller_order;
                        $obj_mpsellerorderdetails->product_id = $product['id_product'];
                        $obj_mpsellerorderdetails->customer_id = $product['id_customer']; //customer_id is product seller customer id
                        $obj_mpsellerorderdetails->product_name = $product['product_name'];
                        $obj_mpsellerorderdetails->customer_name = $product['firstname'];
                        $obj_mpsellerorderdetails->price = $product['total_price_tax_incl'];
                        $obj_mpsellerorderdetails->quantity = $product['product_quantity'];
                        $obj_mpsellerorderdetails->commision = $admin_commision;
                        $obj_mpsellerorderdetails->id_order = $id_order;
                        $obj_mpsellerorderdetails->add();
                        $id_insert = $obj_mpsellerorderdetails->id;

                        // minimize seller product quantity
                        $mp_product_id = $product['marketplace_seller_id_product'];
                        $obj_seller_product = new SellerProductDetail($mp_product_id);
                        $obj_seller_product->quantity = $obj_seller_product->quantity - $product['product_quantity'];
                        $obj_seller_product->save();

                        Hook::exec('actionSellerPaymentTransaction', array('commision' => $admin_commision,
                                                                'id_seller' => $product['id_customer'],
                                                                'id_currency' => $id_currency,
                                                                'commision_calc_latest_id' => $id_insert,
                                                                'product_price' => $product['product_price'],
                                                                'product_quantity' => $product['product_quantity'],
                                                                'seller_cart_product_data' => $product));
                    }
                }
                Hook::exec('actionOrderPaymentHook', array('customervar' => $seller_cart_products,
                                    'id_currency' => $id_currency));
            }

            // order mail to every seller if his/her product in the cart
            if (isset($id_seller_order) && $id_seller_order)
                $this->sellersOrderMail($id_order);
        }
    }

    public function sellersOrderMail($id_order)
    {
       //for seller order email
        $id_lang = Configuration::get('PS_LANG_DEFAULT');
        $obj_order_detail = new OrderDetail();
		$order = new Order($id_order);
        $product_details = $obj_order_detail->getList($id_order);
        $obj_mp_prod = new SellerProductDetail();
        $obj_mp_seller = new SellerInfoDetail();
        $seller_list = array();

        // get seller product details for mail
        foreach ($product_details as $product)
        {
           $mp_product_id = $obj_mp_prod->checkProduct($product['product_id']);
            if ($mp_product_id)
            {
               $mp_seller_id = $obj_mp_prod->getSellerIdByProduct($mp_product_id);
               if (!array_key_exists($mp_seller_id, $seller_list))
               {
                    $seller_list[$mp_seller_id]['products'][] = $product['product_id'];
					$seller_list[$mp_seller_id]['product_attribute'][] = $product['product_attribute_id'];
					$seller_list[$mp_seller_id]['product_price'][] = Tools::displayPrice($product['unit_price_tax_incl'], $this->context->currency, false);
                    $seller_list[$mp_seller_id]['quantity'][] = $product['product_quantity'];
                    $seller_list[$mp_seller_id]['unit_price'][] = Tools::displayPrice($product['unit_price_tax_excl'], $this->context->currency, false);
                    $seller_list[$mp_seller_id]['total_price'][] = Tools::displayPrice($product['unit_price_tax_excl'] * $product['product_quantity'], $this->context->currency, false);
               }
               else
               {
                    $count = count($seller_list[$mp_seller_id]['products']);
                    $seller_list[$mp_seller_id]['products'][$count] = $product['product_id'];
                    $seller_list[$mp_seller_id]['quantity'][$count] = $product['product_quantity'];
					$seller_list[$mp_seller_id]['product_attribute'][] = $product['product_attribute_id'];
					$seller_list[$mp_seller_id]['product_price'][] = Tools::displayPrice($product['unit_price_tax_incl'], $this->context->currency, false);
                    $seller_list[$mp_seller_id]['unit_price'][] = Tools::displayPrice($product['unit_price_tax_excl'], $this->context->currency, false);
                    $seller_list[$mp_seller_id]['total_price'][] = Tools::displayPrice($product['unit_price_tax_excl'] * $product['product_quantity'], $this->context->currency, false);
               }
            }
        }
		
        if (count($seller_list))
        {
           foreach ($seller_list  as $key => $value)
            {
                $customer_info = $obj_mp_prod->getCustomerInfo($this->context->customer->id);
                $id_address_delivery = $obj_mp_prod->getDeliverAddress($id_order); 
                $shipping_details = $obj_mp_prod->getShippingInfo($id_address_delivery);
                $state = $obj_mp_prod->getState($shipping_details['id_state']);
                $country = $obj_mp_prod->getCountry($shipping_details['id_country']);
                $customer_id = $obj_mp_prod->getCustomerIdBySellerId($key);
                $seller_info = $obj_mp_prod->getSellerInfo($customer_id);
                $produst_details = array();
                $i = 0;
                foreach ($value['products'] as $id_product)
                {
                    $obj_prod = new Product($id_product, false, $id_lang);
					
                    $produst_details[$i]['name'] = $obj_prod->name;
                    $produst_details[$i]['qty'] = $value['quantity'][$i];
                    $produst_details[$i]['unit_price'] = $value['unit_price'][$i];
                    $produst_details[$i]['total_price'] = $value['total_price'][$i];
					$produst_details[$i]['product_attribute'] = $value['product_attribute'][$i];
					$produst_details[$i]['product_price'] = $value['product_price'][$i];
					
						$sql = "SELECT id_cart FROM ps_orders WHERE id_order=$id_order";
						$id_cart = Db::getInstance()->getValue($sql);
                        $customized_datas = Product::getAllCustomizedDatas((int)$id_cart);
                        if (isset($customized_datas[$id_product][$produst_details[$i]['product_attribute']])) {
							
                            $product_var_tpl['customization'] = array();
                            foreach ($customized_datas[$id_product][$produst_details[$i]['product_attribute']][$order->id_address_delivery] as $customization) {
                                
								$customization_text = '';
                                if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                                    foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                        $customization_text .= $text['name'].': '.$text['value'];
                                    }
                                }

                                if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                                    $customization_text .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE]));
                                }
								
                                $customization_quantity = (int)$value['quantity'][$i];
								
                                $product_var_tpl['customization'] = array(
                                    'customization_text' => $customization_text,
                                    'customization_quantity' => $customization_quantity,
                                    'quantity' => Tools::displayPrice($produst_details[$i]['product_price'] * $customization_quantity, $this->context->currency, false)
                                );
								
                            }
 						$produst_details[$i]['customization'] = $product_var_tpl;
                       }
						
						
					
                    $i++;
                }
                $customer_name = $customer_info['firstname'].' '.$customer_info['lastname'];
                $ship_address_name = $shipping_details['firstname'].' '.$shipping_details['lastname'];
                $ship_address = $shipping_details['address1'].', '.$shipping_details['address2'];
                $product_html = $obj_mp_seller->getMpEmailTemplateContent('mp_order_product_list.tpl', Mail::TYPE_HTML, $produst_details);
				
                $templateVars = array('{seller_firstname}' => $seller_info['firstname'],
                                      '{seller_lastname}' => $seller_info['lastname'],
                                      '{customer_name}' => $customer_name,
                                      '{customer_email}' => $customer_info['email'],
                                      '{ship_address_name}' => $ship_address_name,
                                      '{ship_address}' => $ship_address,
                                      '{city}' => $shipping_details['city'],
                                      '{state}' => $state,
                                      '{country}' => $country,
                                      '{zipcode}' =>$shipping_details['postcode'],
                                      '{phone}' => $shipping_details['phone'],
                                      '{product_html}' => $product_html);
                //$template = 'mp_order';
                //$subject = 'Order Created';
                $to = $seller_info['email'];
//if ($customer_info['email']=="contactfloum@gmail.com") $to = "rols@free.fr";
                $temp_path = _PS_MODULE_DIR_.'marketplace/mails/';
                Mail::Send($id_lang,
                    'mp_order',
                    Mail::l('Order Created', $id_lang),
                    $templateVars,
                    $to,
                    null,
                    null,
                    'Marketplace',
                    null,
                    null,
                    $temp_path,
                    false,
                    null,
                    null);
//if ($customer_info['email']=="contactfloum@gmail.com") $to = "contactfloum@gmail.com";
                Mail::Send($id_lang,
                    'mp_order',
                    Mail::l('Order Created', $id_lang),
                    $templateVars,
                    $to,
                    null,
                    null,
                    'Marketplace',
                    null,
                    null,
                    $temp_path,
                    false,
                    null,
                    null);
            }
        }
    }

    public function hookDisplayCustomerAccount()
    {
        return $this->display(__FILE__, 'customeraccount.tpl');
    }
    
    /**
     * [hookactionProductSave - Active/deactive seller product from catalog]
     * @param  [type] $params [Get psproductid and product status]
     * @return [type]         [Comment - We can send email after active/deactive product from catalog because this hook is also called when we save the product from save button. But we want action only when admin click on active/deactive button.]
     */
    public function hookactionProductSave($params)
    {
        if ($id_product = $params['id_product'])
        {
            $obj_shop_product = new MarketplaceShopProduct();
            if ($mp_id_product = $obj_shop_product->getMpProductIdByPsProductId($id_product))
            {
                $obj_mp_product = new SellerProductDetail($mp_id_product);
                
                if($params['product']->active == 1)
                {
                    $obj_mp_product->active = 1;                    
                    Hook::exec('actionToogleProductStatusGlobal', array('mp_product_id' => $mp_id_product, 'active' => $obj_mp_product->active));
                }
                else
                    $obj_mp_product->active = 0;
              
                $obj_mp_product->save();
                Hook::exec('actionToogleProductStatusNew', array('main_product_id' => $id_product, 'active' => $obj_mp_product->active));           
            }
        }
    }
    
    public function hookactionProductDelete($params)
    {
        if ($id_product = $params['id_product'])
        {
            $obj_shop_product = new MarketplaceShopProduct();
            if ($mp_id_product = $obj_shop_product->getMpProductIdByPsProductId($id_product))
            {
                // Status inactive of seller product according to mp seller product id
                $obj_seller_product = new SellerProductDetail();
                $obj_seller_product->changeSellerProductStatusBySellerProductId($mp_id_product, 0);

                // Status inactive of seller product image according to mp seller product id
                $obj_seller_product = new MarketplaceProductImage();
                $obj_seller_product->changeProductImageStatusBySellerProductId($mp_id_product, 0);

                // Delete shop product according to mp seller product id
                $obj_shop_product->deleteProductBySellerProductId($mp_id_product);                        
            }
        }
    }

    public function hookDisplayProductTab()
    {
        $id_product = Tools::getValue('id_product');
        $obj_mp_product = new SellerProductDetail();
        $mp_product = $obj_mp_product->getMarketPlaceShopProductDetail($id_product);
        if ($mp_product)
            return $this->display(__FILE__, 'seller_details_tab.tpl');
    }

    public function hookDisplayProductTabContent()
    {
        $this->context->controller->addCSS($this->_path.'views/css/productsellerdetails.css');

        $id_customer = $this->context->cookie->id_customer;
        $id_product = Tools::getValue('id_product');
        $obj_mp_product = new SellerProductDetail();
        $obj_mp_seller = new SellerInfoDetail();
        
        $seller_shop_detail = $obj_mp_product->getMarketPlaceShopProductDetail($id_product);
        
        if ($seller_shop_detail)
        {
            $mp_id_shop = $seller_shop_detail['id_shop'];
            $id_product = $seller_shop_detail['id_product'];
          
            $mp_shop = $obj_mp_product->getMarketPlaceShopDetail($mp_id_shop);
            if ($mp_shop)
            {
                $mp_customer = $obj_mp_seller->getMarketPlaceSellerIdByCustomerId($mp_shop['id_customer']);
                if ($mp_customer)
                {
                    $seller_id = $mp_customer['marketplace_seller_id'];
                    $mp_seller_info = $obj_mp_seller->getmarketPlaceSellerInfo($seller_id);
                    if ($mp_seller_info)
                         $this->context->smarty->assign('mp_seller_info', $mp_seller_info);
                    
                    $obj_ps_shop = new MarketplaceShop($mp_id_shop);
                    $name_shop = $obj_ps_shop->link_rewrite;

                    $this->context->smarty->assign('id_customer', $id_customer);
                    $this->context->smarty->assign('id_shop', $mp_id_shop);
                    $this->context->smarty->assign('name_shop', $name_shop);
                    $this->context->smarty->assign('id_product', $id_product);
                    $this->context->smarty->assign('seller_id', $seller_id);
                    //assign the seller details view vars
                    SellerInfoDetail::assignSellerDetailsView();
                    $this->context->smarty->assign('MP_SHOW_SELLER_DETAILS', Configuration::get('MP_SHOW_SELLER_DETAILS'));
                    return $this->display(__FILE__, 'seller_details_content.tpl');
                }
            }
            
        }    
    }
    
    public function callInstallTab()
    {
        $this->installTab('AdminMarketplaceManagement', 'Marketplace Management');
        $this->installTab('AdminSellerInfoDetail', 'Manage Seller Profile', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerProductDetail', 'Manage Seller Product', 'AdminMarketplaceManagement');
        $this->installTab('AdminCustomerCommision', 'Manage Admin Commission', 'AdminMarketplaceManagement');
        $this->installTab('AdminSellerOrders', 'Manage Seller Orders', 'AdminMarketplaceManagement');
        $this->installTab('AdminPaymentMode', 'Manage Payment Mode', 'AdminMarketplaceManagement');
        $this->installTab('AdminReviews', 'Manage Seller Reviews', 'AdminMarketplaceManagement');
        return true;
    }
    
    public function installTab($class_name,$tab_name,$tab_parent_name=false) 
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;

        if($tab_parent_name)
            $tab->id_parent = (int)Tab::getIdFromClassName($tab_parent_name);
        else
            $tab->id_parent = 0;
        
        $tab->module = $this->name;
        return $tab->add();
    }
    
    public function install()
    {
        if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;
        else if (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
            return false;

        $sql = str_replace(array('PREFIX_',  'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
        $sql = preg_split("/;\s*[\r\n]+/", $sql);

        foreach ($sql as $query)
            if ($query)
                if (!Db::getInstance()->execute(trim($query)))
                    return false;

         if (!parent::install()
            || !$this->registerHook('displayleftColumn')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displaycustomerAccount')
            || !$this->registerHook('displayProductTab')
            || !$this->registerHook('displayProductTabContent')
            || !$this->registerHook('displayMenuhook')
            || !$this->registerHook('displayMpmenuhook')
            || !$this->registerHook('displayMpmyaccountmenuhook')
            || !$this->registerHook('displayMpOrderheaderlefthook')
            || !$this->registerHook('displayMpOrderheaderrighthook')
            || !$this->registerHook('displayMpbottomordercustomerhook')
            || !$this->registerHook('displayMpbottomorderstatushook')
            || !$this->registerHook('displayMpbottomorderproductdetailhook')
            || !$this->registerHook('displayMpordershippinghook')
            || !$this->registerHook('displayMpordershippinglefthook')
            || !$this->registerHook('displayMpordershippingrighthook')
            || !$this->registerHook('displayMpdashboardtophook')
            || !$this->registerHook('displayMpdashboardbottomhook')
            || !$this->registerHook('displayMpsplefthook')
            || !$this->registerHook('displayMpspcontentbottomhook')
            || !$this->registerHook('displayMpsprighthook')
            || !$this->registerHook('displayMpshoplefthook')
            || !$this->registerHook('displayMpshopcontentbottomhook')
            || !$this->registerHook('displayMpshoprighthook')
            || !$this->registerHook('displayMpcollectionlefthook')
            || !$this->registerHook('displayMpcollectionfooterhook')
            || !$this->registerHook('displayMpaddproductfooterhook')
            || !$this->registerHook('displayMpupdateproductfooterhook')
            || !$this->registerHook('displayMpshoprequestfooterhook')
            || !$this->registerHook('displayMpshopaddfooterhook')
            || !$this->registerHook('displayMpproductdetailheaderhook')
            || !$this->registerHook('displayMpproductdetailfooterhook')
            || !$this->registerHook('displayMppaymentdetailfooterhook')
            || !$this->registerHook('displayMpsellerinfobottomhook')
            || !$this->registerHook('displayMpsellerleftbottomhook')
            || !$this->registerHook('actionAddproductExtrafield')
            || !$this->registerHook('actionUpdateproductExtrafield')
            || !$this->registerHook('actionAddshopExtrafield')
            || !$this->registerHook('actionUpdateshopExtrafield')
            || !$this->registerHook('actionValidateOrder')
            || !$this->registerHook('actionProductDelete')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('actionProductSave')
            || !$this->callInstallTab()
        )
        return false;

        //set default config variable
        Configuration::updateValue('MP_PRODUCT_ADMIN_APPROVE', 1);
        Configuration::updateValue('MP_SELLER_ADMIN_APPROVE', 1);
        Configuration::updateValue('MP_REVIEWS_ADMIN_APPROVE', 1);

        Configuration::updateValue('MP_TITLE_BG_COLOR', '#333333');
        Configuration::updateValue('MP_TITLE_TEXT_COLOR', '#ffffff');
        Configuration::updateValue('MP_PHONE_DIGIT', 12);
        Configuration::updateValue('MP_GLOBAL_COMMISSION', 10);
        Configuration::updateValue('MP_SHOW_SELLER_DETAILS', 1);

        // default tax distribution to admin
        Configuration::updateValue('MP_PRODUCT_TAX_DISTRIBUTION', 'admin');

        $obj_emp = new Employee(1); //By default super admin email
        Configuration::updateValue('MP_SUPERADMIN_EMAIL', $obj_emp->email);
        Configuration::updateValue('MP_SELLER_PRODUCTS_SETTINGS', 0);

        return true;
    }
    
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCss($this->_path.'views/css/admin/css/marketplacemenu.css');
    }

    public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'marketplace_seller_product`,
            `'._DB_PREFIX_.'marketplace_seller_product_category`,
            `'._DB_PREFIX_.'marketplace_seller_info`,
            `'._DB_PREFIX_.'marketplace_shop`,
            `'._DB_PREFIX_.'marketplace_shop_product`,
            `'._DB_PREFIX_.'marketplace_customer`,
            `'._DB_PREFIX_.'marketplace_product_image`,
            `'._DB_PREFIX_.'marketplace_commision_calc`,
            `'._DB_PREFIX_.'marketplace_commision`,
            `'._DB_PREFIX_.'marketplace_payment_mode`,
            `'._DB_PREFIX_.'marketplace_customer_payment_detail`,
            `'._DB_PREFIX_.'marketplace_customer_query`,
            `'._DB_PREFIX_.'marketplace_query_records`,
            `'._DB_PREFIX_.'marketplace_seller_reviews`,
            `'._DB_PREFIX_.'marketplace_order_commision`,
            `'._DB_PREFIX_.'marketplace_seller_orders`,
            `'._DB_PREFIX_.'marketplace_shipping`,
            `'._DB_PREFIX_.'marketplace_delivery`');
    }
        
    public function callUninstallTab()
    {
        $this->uninstallTab('AdminReviews');
        $this->uninstallTab('AdminPaymentMode');
        $this->uninstallTab('AdminCustomerCommision');
        $this->uninstallTab('AdminSellerOrders');
        $this->uninstallTab('AdminSellerProductDetail');
        $this->uninstallTab('AdminSellerInfoDetail');
        $this->uninstallTab('AdminMarketplaceManagement');
        return true;
    }
        
    public function uninstallTab($class_name)
    {
        $id_tab = (int)Tab::getIdFromClassName($class_name);
        if ($id_tab)
        {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        else
            return false;
    }

    public function deleteConfigKeys()
    {
        $var = array('MP_SUPERADMIN_EMAIL',
                    'MP_SELLER_PROFILE_ID', 'MP_SELLER_ADMIN_APPROVE',
                    'MP_PRODUCT_ADMIN_APPROVE', 'MP_TITLE_COLOR',
                    'MP_MENU_BORDER_COLOR', 'MP_GLOBAL_COMMISSION');

        foreach ($var as $key)
            if (!Configuration::deleteByName($key))
                return false;
        
        return true;
    }

    public function reset()
    {
        if (!$this->uninstall(false))
            return false;
        if (!$this->install(false))
            return false;
        return true;
    }

    public function uninstall($keep = true)
    {
        if(!parent::uninstall() || ($keep && !$this->deleteTables())
            || !$this->callUninstallTab()
            || !$this->deleteConfigKeys())
            return false;

        return true;
    }
}
?>