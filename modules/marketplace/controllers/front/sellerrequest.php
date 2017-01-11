<?php
class MarketplaceSellerRequestModuleFrontController extends ModuleFrontController
{
    public function postProcess()
    {
        if (Tools::isSubmit('seller_save'))
        {
            $customer_id = $this->context->customer->id;
            $id_lang = $this->context->language->id;
            $valid_image = $this->validateShopLogoSize($_FILES['upload_logo']);
            if ($valid_image)
            {
                $shop_name = trim(Tools::getValue('shop_name'));
                $seller_name = trim(Tools::getValue('person_name'));
                $phone = trim(Tools::getValue('phone'));
                $business_email_id = trim(Tools::getValue('business_email_id'));
                $fb_id = Tools::getValue('fb_id');
                $tw_id = Tools::getValue('tw_id');
                $fax = Tools::getValue('fax');
                $about_business = Tools::getValue('about_business');
                $address = Tools::getValue('address');
                $admin_seller_approve = Configuration::get('MP_SELLER_ADMIN_APPROVE');
                if ($admin_seller_approve == 0)
                    $active = 1;
                else
                    $active = 0;

                if ($shop_name == '')
                    $this->errors[] = Tools::displayError('Shop name is required field.');
                else if (!Validate::isCatalogName($shop_name))
                    $this->errors[] = Tools::displayError('Shop name is invalid.');
                else if (SellerInfoDetail::isShopNameExist($shop_name))                
                    $this->errors[] = Tools::displayError('Shop name is already taken. Try another.');
                
                if($seller_name == '')                
                    $this->errors[] = Tools::displayError('Seller name is required field.');
                else if (!Validate::isName($seller_name))
                    $this->errors[] = Tools::displayError('Invalid seller name.');

                if($phone == '')                
                    $this->errors[] = Tools::displayError('Phone is required field.');
                else if(!Validate::isPhoneNumber($phone))
                    $this->errors[] = Tools::displayError('Invalid phone number.');

                if($business_email_id == '')
                    $this->errors[] = Tools::displayError('Email ID is requird field.');
                elseif(!Validate::isEmail($business_email_id))
                    $this->errors[] = Tools::displayError('Invalid Email ID.');
                elseif(SellerInfoDetail::isSellerEmailExist($business_email_id))
                    $this->errors[] = Tools::displayError('Email ID already exist.');

                Hook::exec('actionBeforeAddSeller');

                //Saving seller details
                if (!count($this->errors))
                {
                    $obj_seller_detail = new SellerInfoDetail();
                    $obj_seller_detail->business_email = $business_email_id;
                    $obj_seller_detail->seller_name = $seller_name;
                    $obj_seller_detail->shop_name = $shop_name;
                    $obj_seller_detail->phone = $phone;
                    $obj_seller_detail->fax = $fax;
                    $obj_seller_detail->about_shop = $about_business;
                    $obj_seller_detail->address = $address;
                    $obj_seller_detail->facebook_id = $fb_id;
                    $obj_seller_detail->twitter_id = $tw_id;
                    $obj_seller_detail->business_email = $business_email_id;
                    $obj_seller_detail->active = $active;
                    $obj_seller_detail->save();
                    $mp_seller_id = $obj_seller_detail->id;
    
                
                    //for checking
                    $obj_mp_cust = new MarketplaceCustomer();
                    if (Configuration::get('MP_SELLER_ADMIN_APPROVE'))
                        $obj_mp_cust->insertMarketplaceCustomer($mp_seller_id, $customer_id);
                    else
                    {
                        // creating seller shop when admin setting is default
                        $is_mpcustomer_insert = $obj_mp_cust->insertActiveMarketplaceCustomer($mp_seller_id, $customer_id);
                        if($is_mpcustomer_insert)
                            $obj_seller_detail->makeDefaultSellerPartner($mp_seller_id);
                    }
            
                    //Upload Shop Logo
                    $this->uploadShopLogo($_FILES['upload_logo'], $shop_name, $mp_seller_id);
                
                    //Mail to admin
                    $this->mailToAdminWhenSellerRequest($seller_name, $shop_name, $business_email_id, $phone, $id_lang);
                    Hook::exec('actionAddshopExtrafield', array('marketplace_seller_id' => $mp_seller_id));
                    if ($mp_seller_id)
                        Tools::redirect($this->context->link->getModuleLink('marketplace','sellerrequest'));
                    else
                        $this->errors[] = Tools::displayError('Something wrong while creating seller.');
                }
            }
        }
    }

    public function initContent()
    {
        $link = new Link();
        $smarty_vars = array();
        if (isset($this->context->cookie->id_customer)) 
        {
            $id_customer = $this->context->cookie->id_customer;
            $obj_mp_cust = new MarketplaceCustomer();
            $mp_customer = $obj_mp_cust->findMarketPlaceCustomer($id_customer);

            if ($shop_img_error = Tools::getValue('shop_img_error'))
                $smarty_vars['shop_img_error'] = $shop_img_error;

            if ($mp_error = Tools::getValue('mp_error'))
                 $smarty_vars['mp_error'] = $mp_error;

            if ($mp_customer) 
            {
                $is_seller = $mp_customer['is_seller'];
                $smarty_vars['is_seller'] = $is_seller;
            }

            if (Configuration::get('MP_TERMS_AND_CONDITIONS_STATUS'))
                $smarty_vars['terms_and_conditions'] = Configuration::get('MP_TERMS_AND_CONDITIONS');

            $smarty_vars['terms_and_condition_active'] = Configuration::get('MP_TERMS_AND_CONDITIONS_STATUS');
            $smarty_vars['max_phone_digit'] = Configuration::get('MP_PHONE_DIGIT');

            $this->context->smarty->assign($smarty_vars);
            $this->setTemplate('sellerrequest.tpl');
        }
        else 
            Tools::redirect('index.php?controller=authentication&back='.urlencode($link->getModuleLink('marketplace', 'sellerrequest')));

        parent::initContent();
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_MODULE_DIR_.'marketplace/views/css/registration.css');
        $this->addJS(_PS_JS_DIR_.'validate.js');
        $this->addJS(_MODULE_DIR_.'marketplace/views/js/mp_form_validation.js');

        //for tiny mce field
        Media::addJsDef(array('iso' => $this->context->language->iso_code,
                            'mp_tinymce_path' => _MODULE_DIR_.'marketplace/libs'));

        if(Configuration::get('PS_JS_THEME_CACHE') == 0)
            $this->addJS(array(
                        _MODULE_DIR_ .'marketplace/views/js/tinymce/tinymce.min.js',
                        _MODULE_DIR_ .'marketplace/views/js/tinymce/tinymce_wk_setup.js'
                ));
    }

    public function validateShopLogoSize($upload_logo)
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

    public function uploadShopLogo($upload_logo, $shop_name, $mp_seller_id)
    {
        $image_name = $shop_name.'.jpg';
        if ($upload_logo['error'] > 0)
            return false;

        $newpath = _PS_MODULE_DIR_.'marketplace/views/img/shop_img/';
        $width = '200';
        $height = '200';
        ImageManager::resize($upload_logo['tmp_name'], $newpath.$mp_seller_id.'-'.$image_name, $width, $height);
    }

    public function mailToAdminWhenSellerRequest($seller_name, $shop_name, $business_email_id, $phone, $id_lang)
    {
        $obj_emp = new Employee(1);    //1 for superadmin
        if(Configuration::get('MP_SUPERADMIN_EMAIL'))
            $admin_email = Configuration::get('MP_SUPERADMIN_EMAIL');
        else
            $admin_email = $obj_emp->email;
        
        $seller_vars = array(
            '{seller_name}' => $seller_name,
            '{seller_shop}' => $shop_name,
            '{seller_email_id}' => $business_email_id,
            '{seller_phone}' => $phone
        );
        
        $template_path = _PS_MODULE_DIR_."/marketplace/mails/";
        Mail::Send(
            (int)$id_lang,
            'seller_request',
            Mail::l('New seller request', (int)$id_lang),
            $seller_vars,
            $admin_email,
            null,
            null,
            null,
            null,
            null,
            $template_path,
            false,
            null,
            null
        );
    }
}
?>