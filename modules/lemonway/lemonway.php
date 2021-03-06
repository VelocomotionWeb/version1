<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
require_once 'services/LemonWayConfig.php';
class Lemonway extends PaymentModule
{
    protected $config_form = false;
    protected $_current_card = null;
    
    /**
     * @since 1.5.0.1
     * @var string Module local path (eg. '/home/prestashop/modules/modulename/')
     */
    protected $local_path = null;

    public function __construct()
    {
        $this->name = 'lemonway';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.1';
        $this->author = 'SIRATECK';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Lemonway');
        $this->description = $this->l('Through its API, Lemon Way offers you state-of-the-art payment technology. Beyond their technological expertise, Lemon Way also offers a multitude of complementary regulation and management services.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module? You loose your datas !');
		
        $this->limited_countries = array();
        $this->limited_currencies = array('EUR');
        
        $this->local_path = _PS_MODULE_DIR_.$this->name.'/';
        
        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5')
        	require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
    }
    
    private function installModuleTab($tabClass, $translations, $idTabParent) {
    	@copy(_PS_MODULE_DIR_ . $this->name . '/logo.png',
    			_PS_IMG_DIR_ . 't/' . $tabClass . '.png');
    	/* @var $tab TabCore */
    	$tab = new Tab();
    	foreach (Language::getLanguages(false) as $language)
    	{
    		if(isset($translations[Tools::strtolower($language['iso_code'])]))
    		{
    			$tab->name[(int)$language['id_lang']] = $translations[Tools::strtolower($language['iso_code'])];
    		}
    	}
    	$tab->class_name = $tabClass;
    	$tab->module = $this->name;
    	$tab->id_parent = $idTabParent;
    	if (!$tab->save())
    		return false;
    	return true;
    }
    
    private function uninstallModuleTab($tabClass) {
    	$idTab = Tab::getIdFromClassName($tabClass);
    	if ($idTab != 0) {
    		$tab = new Tab($idTab);
    		$tab->delete();
    		@unlink(_PS_IMG_DIR . "t/" . $tabClass . ".png");
    		return true;
    	}
    	return false;
    }
    
    /**
     * 
     * @param string $key Configuration key
     * @param array $translations array of name by lang
     * @param string $color Hex code or color's name
     * @param bool $hidden
     * @param bool $send_mail
     * @param bool $delivery
     * @param bool $logable
     * @param bool $invoice
     * @return boolean
     */
    protected function _addStatus($key,$translations,$color='royalblue',$hidden=false,$send_mail=false,$delivery=false,$logable=false,$invoice=false)
    {
    	if (!Configuration::get($key))
    	{
    		
    		$os = new OrderState();
    		$os->name = array();
    		foreach (Language::getLanguages(false) as $language)
    		{
    			if(isset($translations[Tools::strtolower($language['iso_code'])]))
    			{
    				$os->name[(int)$language['id_lang']] = $translations[Tools::strtolower($language['iso_code'])];
    			}
    		}
    		 
    		$os->color = $color;
    		$os->hidden = $hidden;
    		$os->send_email = $hidden;
    		$os->delivery = $delivery;
    		$os->logable = $logable;
    		$os->invoice = $invoice;
    		if ($os->add())
    		{
    			Configuration::updateValue($key, $os->id);
    			copy(dirname(__FILE__).'/icon.gif', dirname(__FILE__).'/../../img/os/'.(int)$os->id.'.gif');
    		}
    		else
    			return false;
    	}
    	return true;
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        if (extension_loaded('curl') == false)
        {
            $this->_errors[] = $this->l('You have to enable the cURL extension on your server to install this module');
            return false;
        }
	
        //API CONFIGURATION
        Configuration::updateValue('LEMONWAY_API_LOGIN', '');
        Configuration::updateValue('LEMONWAY_API_PASSWORD', '');
        Configuration::updateValue('LEMONWAY_MERCHANT_ID', '');
        Configuration::updateValue('LEMONWAY_DIRECTKIT_URL', '');
        Configuration::updateValue('LEMONWAY_WEBKIT_URL', '');
        Configuration::updateValue('LEMONWAY_DIRECTKIT_URL_TEST', '');
        Configuration::updateValue('LEMONWAY_WEBKIT_URL_TEST', '');
        Configuration::updateValue('LEMONWAY_IS_TEST_MODE', false);
        
        //METHOD CONFIGURATION
        Configuration::updateValue('LEMONWAY_COMMISSION_AMOUNT', false);
        Configuration::updateValue('LEMONWAY_IS_AUTO_COMMISSION', true);
        Configuration::updateValue('LEMONWAY_CSS_URL', 'https://www.lemonway.fr/mercanet_lw.css');
        Configuration::updateValue('LEMONWAY_ONECLIC_ENABLED', false);

        include(dirname(__FILE__).'/sql/install.php');
        
        //Prepare status values
        $key = 'LEMONWAY_PENDING_OS';
        
        $translationsAdminLemonway = array('en'=>'Lemonway','fr'=>'Lemonway');
        $this->installModuleTab('AdminLemonway', $translationsAdminLemonway, 0);
        
        $translationsStatus = array('en'=>'Pending payment validation from Lemonway','fr'=>'En attente de validation par Lemonway');
		$translationsAdminMoneyOut = array('en'=>'Money out','fr'=>'Virements bancaire');
		
		
		
		$adminLemonwayId = Db::getInstance()->getValue("SELECT `id_tab` FROM "._DB_PREFIX_."tab WHERE `class_name`='AdminLemonway'");
		
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
        	$this->_addStatus($key, $translationsStatus,'orange') && //Add new Status
        	$this->installModuleTab('AdminMoneyOut', $translationsAdminMoneyOut, $adminLemonwayId);
        ;
    }

    public function uninstall()
    {
    	//API CONFIGURATION
        Configuration::deleteByName('LEMONWAY_API_LOGIN');
        Configuration::deleteByName('LEMONWAY_API_PASSWORD');
        Configuration::deleteByName('LEMONWAY_MERCHANT_ID');
        Configuration::deleteByName('LEMONWAY_DIRECTKIT_URL');
        Configuration::deleteByName('LEMONWAY_WEBKIT_URL');
        Configuration::deleteByName('LEMONWAY_DIRECTKIT_URL_TEST');
        Configuration::deleteByName('LEMONWAY_WEBKIT_URL_TEST');
        Configuration::deleteByName('LEMONWAY_IS_TEST_MODE');
        
        //METHOD CONFIGURATION
        Configuration::deleteByName('LEMONWAY_COMMISSION_AMOUNT');
        Configuration::deleteByName('LEMONWAY_IS_AUTO_COMMISSION');
        Configuration::deleteByName('LEMONWAY_CSS_URL');
        Configuration::deleteByName('LEMONWAY_ONECLIC_ENABLED');
        
        //Do Not delete this configuration
        //Configuration::deleteByName('LEMONWAY_PENDING_OS');
        
        $this->uninstallModuleTab('AdminMoneyOut');
        $this->uninstallModuleTab('AdminLemonway');

        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitLemonwayModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLemonwayModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getApiConfigForm(),$this->getMethodConfigForm()));
    }
    
    /**
     * Create the structure of api informations form.
     */
    protected function getMethodConfigForm()
    {
    	$container = array(
    			'form' => array(
    					'legend'=>array(
    						'title' => $this->l('METHOD CONFIGURATION'),
    						'icon' => 'icon-cogs',
    					),
    					'input' => array(),
    					'submit' => array(
    							'title' => $this->l('Save'),
    					)
    			),
    	);
    	
    	
    	$container['form']['input'][] = array(
                        'type' => 'switch',
                        'label' => $this->l('Auto commission'),
                        'name' => 'LEMONWAY_IS_AUTO_COMMISSION',
                        'is_bool' => true,
                        'desc' => $this->l('If No you must fill field Commission amount below.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        )
    			);
    	
    	$container['form']['input'][] = array(
    			'col' => 3,
    			'label' => $this->l('Comission amount'),
    			'name' => 'LEMONWAY_COMMISSION_AMOUNT',
    			'type' => 'text',
    			'prefix' => '<i class="icon icon-eur"></i>',
    			'is_number' => true,
    			'desc' => '',
    	);
    	
    	$container['form']['input'][] = array(
    			'type' => 'switch',
    			'label' => $this->l('Enable Oneclic'),
    			'name' => 'LEMONWAY_ONECLIC_ENABLED',
    			'is_bool' => true,
    			'desc' => $this->l('Display oneclic form on payment step'),
    			'values' => array(
    					array(
    							'id' => 'active_on',
    							'value' => true,
    							'label' => $this->l('Enabled')
    					),
    					array(
    							'id' => 'active_off',
    							'value' => false,
    							'label' => $this->l('Disabled')
    					)
    			)
    	);
    	
    	$container['form']['input'][] = array(
    			'col' => 6,
    			'label' => $this->l('CSS URL'),
    			'name' => 'LEMONWAY_CSS_URL',
    			'type' => 'text',
    			'prefix' => '<i class="icon icon-css3"></i>',
    			'is_number' => true,
    			'desc' => '',
    	);
    	
    	
    	
    	return $container;
    }

    /**
     * Create the structure of api informations form.
     */
    protected function getApiConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('API CONFIGURATION'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                		array(
                				'col' => 3,
                				'type' => 'text',
                				'prefix' => '<i class="icon icon-user"></i>',
                				'desc' => $this->l('Production Api login'),
                				'name' => 'LEMONWAY_API_LOGIN',
                				'label' => $this->l('API LOGIN'),
                		),
                		array(
                				'col' => 3,
                				'type' => 'password',
                				'name' => 'LEMONWAY_API_PASSWORD',
                				'label' => $this->l('API Password'),
                		),
                		array(
                				'col' => 3,
                				'type' => 'text',
                				'prefix' => '<i class="icon icon-google-wallet"></i>',
                				'desc' => $this->l('It\'s the wallet where your payments are credited.You must to create it in BO Lemonway'),
                				'name' => 'LEMONWAY_MERCHANT_ID',
                				'label' => $this->l('Wallet Merchant ID'),
                		),
                		array(
                				'col' => 6,
                				'type' => 'text',
                				'prefix' => '<i class="icon icon-cloud-upload"></i>',
                				'desc' => $this->l(''),
                				'name' => 'LEMONWAY_DIRECTKIT_URL',
                				'label' => $this->l('DIRECTKIT URL'),
                		),
                		array(
                				'col' => 6,
                				'type' => 'text',
                				'prefix' => '<i class="icon icon-cloud-upload"></i>',
                				'desc' => $this->l(''),
                				'name' => 'LEMONWAY_WEBKIT_URL',
                				'label' => $this->l('WEBKIT URL'),
                		),
                		array(
                				'col' => 6,
                				'type' => 'text',
                				'prefix' => '<i class="icon icon-cloud-upload"></i>',
                				'desc' => $this->l(''),
                				'name' => 'LEMONWAY_DIRECTKIT_URL_TEST',
                				'label' => $this->l('DIRECTKIT URL TEST'),
                		),
                		array(
                				'col' => 6,
                				'type' => 'text',
                				'prefix' => '<i class="icon icon-cloud-upload"></i>',
                				'desc' => $this->l(''),
                				'name' => 'LEMONWAY_WEBKIT_URL_TEST',
                				'label' => $this->l('WEBKIT URL TEST'),
                		),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable test mode'),
                        'name' => 'LEMONWAY_IS_TEST_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Call requests in test API Endpoint'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
    		
        return array(
            'LEMONWAY_API_LOGIN' => Configuration::get('LEMONWAY_API_LOGIN', null),
            'LEMONWAY_API_PASSWORD' => Configuration::get('LEMONWAY_API_PASSWORD', null),
            'LEMONWAY_MERCHANT_ID' => Configuration::get('LEMONWAY_MERCHANT_ID', null),
        	'LEMONWAY_DIRECTKIT_URL' => Configuration::get('LEMONWAY_DIRECTKIT_URL', null),
        	'LEMONWAY_WEBKIT_URL' => Configuration::get('LEMONWAY_WEBKIT_URL', null),
        	'LEMONWAY_DIRECTKIT_URL_TEST' => Configuration::get('LEMONWAY_DIRECTKIT_URL_TEST', null),
        	'LEMONWAY_WEBKIT_URL_TEST' => Configuration::get('LEMONWAY_WEBKIT_URL_TEST', null),
        	'LEMONWAY_IS_TEST_MODE' => Configuration::get('LEMONWAY_IS_TEST_MODE', null),
        	'LEMONWAY_IS_AUTO_COMMISSION' => Configuration::get('LEMONWAY_IS_AUTO_COMMISSION', null),
        	'LEMONWAY_COMMISSION_AMOUNT' => Configuration::get('LEMONWAY_COMMISSION_AMOUNT', null),
        	'LEMONWAY_CSS_URL' => Configuration::get('LEMONWAY_CSS_URL',null),
        	'LEMONWAY_ONECLIC_ENABLED' => Configuration::get('LEMONWAY_ONECLIC_ENABLED',null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
        	if($key == 'LEMONWAY_API_PASSWORD' && trim(Tools::getValue($key)) == "" )
        		continue;
 
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }


    
    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            
        }
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    /**
     * This method is used to render the payment button,
     * Take care if the button should be displayed or not.
     */
    public function hookPayment($params)
    {
        $currency_id = $params['cart']->id_currency;
        $currency = new Currency((int)$currency_id);

        if (in_array($currency->iso_code, $this->limited_currencies) == false)
            return false;
        /* @var $cart Cart */
        $cart = $params['cart'];
        /* @var $customer CustomerCore */
        $customer = $this->context->customer;
        
        $card_num = "";
        $card_type = "";
        $card_exp = "";
        $card = $this->getCustomerCard($customer->id);
		
        if($card){
        	$card_num = $card['card_num'];
        	$card_type = $card['card_type'];
        	$card_exp = $card['card_exp'];
        }
        $customer_has_card = $card && !empty($card_num);
        
        $this->smarty->assign(array('module_dir'=>$this->_path,
        							'oneclic_allowed'=>LemonWayConfig::getOneclicEnabled() && $customer->isLogged(),
        							'customer_has_card'=> $customer_has_card,
        							'card_num'=>$card_num,
        							'card_type'=>$card_type,
        							'card_exp'=>$card_exp
        		
        ));

        return $this->display(__FILE__, 'views/templates/hook/payment.tpl');
    }

    /**
     * This hook is used to display the order confirmation page.
     */
    public function hookPaymentReturn($params)
    {
        if ($this->active == false)
            return;

        $order = $params['objOrder'];

        if ($order->getCurrentOrderState()->id != Configuration::get('PS_OS_ERROR'))
            $this->smarty->assign('status', 'ok');

        $this->smarty->assign(array(
            'id_order' => $order->id,
            'reference' => $order->reference,
            'params' => $params,
            'total' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false),
        ));

        return $this->display(__FILE__, 'views/templates/hook/confirmation.tpl');
    }
    
    public function getCustomerCard($id_customer){
    	if(is_null($this->_current_card))
    	{
    		
	    	$query = 'SELECT * FROM `'._DB_PREFIX_.'lemonway_oneclic` lo WHERE lo.`id_customer` = '.(int)$id_customer;
	    	$this->_current_card = Db::getInstance()->getRow($query);
    	
    	}
    	return $this->_current_card;
    }
    
    public function insertOrUpdateCard($id_customer,$data){
    	
    	$oldCard = $this->getCustomerCard($id_customer);
    	if($oldCard){
    		$data = array_merge($oldCard,$data);
    	}
    	
    	Db::getInstance()->insert('lemonway_oneclic', $data,false,true,Db::REPLACE);
    	
    }
    
    
}
