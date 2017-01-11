<?php
if (!defined('_PS_VERSION_'))
	exit;

include_once dirname(__FILE__).'/../marketplace/classes/MarketplaceClassInclude.php';
include_once dirname(__FILE__).'/classes/StoreLocator.php';
include_once dirname(__FILE__).'/classes/StoreProduct.php';

class MpStoreLocator extends Module
{
	const INSTALL_SQL_FILE = 'install.sql';
	private $_html = '';

	public function __construct()
	{
		$this->name = 'mpstorelocator';
		$this->tab = 'front_office_features';
		$this->version = '2.0.0';
		$this->author = 'Webkul';
		$this->dependencies = array('marketplace');
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Marketplace Shop Locator');
		$this->description = $this->l('Marketplace Shop Location Detector');
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit')) {
			Configuration::updateValue('MP_STORE_LOCATION_ACTIVATION', Tools::getValue('MP_STORE_LOCATION_ACTIVATION'));
			Configuration::updateValue('MP_STORE_ALL_SELLER', Tools::getValue('MP_STORE_ALL_SELLER'));
		}

		$this->_html .= $this->displayConfirmation($this->l('Settings updated'));
	}

	public function getContent()
    {    	
        if (Tools::isSubmit('btnSubmit'))
			$this->_postProcess();
		else
			$this->_html .= '<br />';

		$this->_html .= $this->renderForm();

		return $this->_html;
    }

    public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Configuration'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'switch',
						'label' => $this->l('Seller can manage store status'),
						'name' => 'MP_STORE_LOCATION_ACTIVATION',
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
						'hint' => $this->l('If No, Seller can not activate his/her store location status. Admin have to approve first.')
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Display all seller\'s stores'),
						'name' => 'MP_STORE_ALL_SELLER',
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
						'hint' => $this->l('If Yes, User can see all seller\'s stores in one page')
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$this->fields_form = array();
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'MP_STORE_LOCATION_ACTIVATION' => Tools::getValue('MP_STORE_LOCATION_ACTIVATION',
												Configuration::get('MP_STORE_LOCATION_ACTIVATION')),
			'MP_STORE_ALL_SELLER' => Tools::getValue('MP_STORE_ALL_SELLER',
												Configuration::get('MP_STORE_ALL_SELLER'))
		);
	}

	public function hookDisplayProductButtons()
	{
		$id_product = Tools::getValue('id_product');
		$obj_mp_product = new SellerProductDetail();
        $mp_product = $obj_mp_product->getMarketPlaceShopProductDetail($id_product);
        // Visible only if marketplace product
        if ($mp_product)
        {
        	// button will display if any store exist
        	$stores = StoreLocator::getAllStore();
        	if ($stores)
        	{
        		// check if marketplace responsive theme installed
				$mptheme = Module::isInstalled('mpproductpagerightcolumn');
				if ($mptheme)
					$this->context->smarty->assign('mptheme', 1);

				$this->context->smarty->assign('store_link', $this->context->link->getModuleLink('mpstorelocator', 'storedetails', array('id_product' => $id_product)));
				return $this->display(__FILE__, 'link.tpl');
        	}
		}
	}

	/**
	 * [hookDisplayProductRightColumn: this hook is for Marketplace Responsive Theme if installed]
	 * @return [html] [tpl]
	 */
	public function hookDisplayProductRightColumn()
	{
		return $this->hookDisplayProductButtons();
	}

	public function hookDisplayFooter($params)
	{		
		if (Configuration::get('MP_STORE_ALL_SELLER')) {
			return $this->display(__FILE__, 'viewsellerstores.tpl');
		}
	}
  
	public function hookDisplayMpmyaccountmenuhook() 
	{
		$id_customer = $this->context->customer->id;
		$obj_mpcustomer = new MarketplaceCustomer();
		$mpcustomer = $obj_mpcustomer->findMarketPlaceCustomer($id_customer);
		$this->context->smarty->assign('mpmenu', 0);
		if ($mpcustomer) 
		{
			$is_seller = $mpcustomer['is_seller'];
			if ($is_seller == 1) 
				return $this->display(__FILE__, 'add_store.tpl');
		}
	}
  
	public function callInstallTab()
	{
		$this->installTab('AdminStoreLocator','Manage Store Location','AdminMarketplaceManagement');
		return true;
	}
	
	
	public function installTab($class_name, $tab_name, $tab_parent_name = false)
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
		        return (false);
		    else if (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
		        return (false);
		    $sql = str_replace(array(
		        'PREFIX_',
		        'ENGINE_TYPE'
		    ), array(
		        _DB_PREFIX_,
		        _MYSQL_ENGINE_
		    ), $sql);
		    $sql = preg_split("/;\s*[\r\n]+/", $sql);
		    foreach ($sql as $query)
		        if ($query)
		            if (!Db::getInstance()->execute(trim($query)))
		                return false;

		// default settings for seller can manage store location status
		Configuration::updateValue('MP_STORE_LOCATION_ACTIVATION', '0');
		Configuration::updateValue('MP_STORE_ALL_SELLER', '1');

		/* 
			Note: To use this module with Marketplace Responsive Theme,
			we have to use "displayProductRightColumn" (out custome hook)
			defind in our theme 
		 */
		if (!parent::install()
			|| !$this->callInstallTab() 
			|| !$this->registerHook('displayHeader')
			|| !$this->registerHook('displayProductRightColumn')
			|| !$this->registerHook('displayRightColumnProduct') 
			|| !$this->registerHook('displayProductButtons') 
			|| !$this->registerHook('displayMpmyaccountmenuhook')
			|| !$this->registerHook('displayFooter')
			)
		  return false;
		    return true;
	}
  
	public function callUninstallTab() 
	{
		if (!$this->uninstallTab('AdminStoreLocator'))
			return false;
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

	public function dropTable()
	{
		$tables = array('store_locator', 'store_products');
		foreach ($tables as $table)
		{
			$drop = Db::getInstance()->execute('DROP TABLE '._DB_PREFIX_.$table);
			if (!$drop)
				return false;
		}
		return true;
	}
  
	public function uninstall()
	{
		if (!parent::uninstall()
			|| !$this->callUninstallTab()
			|| !$this->dropTable())
			return false;
		return true;
	}
}
?>