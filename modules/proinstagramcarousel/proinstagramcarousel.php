<?php

/*
	* proinstagramcarousel module.
	* @author 0RS <lookshoper@gmail.com>
	* @copyright Copyright &copy; 2014 procoder
	* @license    http://www.opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
	* @version 0.1
*/


	if (!defined('_PS_VERSION_'))
	  exit;	
		
	class ProInstagramCarousel extends Module
	{	
		 public function __construct()
		  {
			$this->name = 'proinstagramcarousel';
			$this->tab = 'front_office_features';
			$this->version = '0.1';
			$this->author = 'procoder';
			$this->bootstrap = true;
			parent::__construct();
			$this->displayName = $this->l('Instagram images carousel');
			$this->description = $this->l('Display Instagram images carousel');
			$this->confirmUninstall = $this->l('Are you sure you want to uninstall?'); 
		}

		public function install()
		{
		  	return parent::install() &&
			$this->registerHook('displayHeader') &&
			$this->registerHook('displayHome') &&
			Configuration::updateValue('PROISTC_ID', '1413083421') &&
			Configuration::updateValue('PROISTC_LIMIT', '3') &&
			Configuration::updateValue('PROISTC_TOKEN', '1413083421.5b9e1e6.1c663ee16a0d4b13a1651346458e1bf0');
		  }

		public function uninstall()
		{
		  if (!parent::uninstall() ||
		 	!Configuration::deleteByName('PROISTC_ID')||
		  	!Configuration::deleteByName('PROISTC_LIMIT')||
		 	!Configuration::deleteByName('PROISTC_TOKEN'))
			return false;
		    return true;
		}
		
			public function getContent()
		{
			$output = null;
	
			if (Tools::isSubmit('submit'.$this->name))
			{
				$proistcid = strval(Tools::getValue('PROISTC_ID'));
				$proistclimit = strval(Tools::getValue('PROISTC_LIMIT'));
				$proistctoken = strval(Tools::getValue('PROISTC_TOKEN'));
					Configuration::updateValue('PROISTC_ID', $proistcid);
					Configuration::updateValue('PROISTC_LIMIT', $proistclimit);
					Configuration::updateValue('PROISTC_TOKEN', $proistctoken);
					$output .= $this->displayConfirmation($this->l('Settings updated'));
		}
			return $output.$this->displayForm();
		}
		
		public function displayForm()
		{
			// Get default Language
			$default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
			 
			// Init Fields form array
			$fields_form[0]['form'] = array(
				'legend' => array(
					'title' => $this->l('Settings'),
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('User Id:'),
						'name' => 'PROISTC_ID',
						'size' => '100',
						'desc' => $this->l('Get Your Instagram Access Token and USER ID: http://www.pinceladasdaweb.com.br/instagram/access-token/')
					),
						array(
						'type' => 'text',
						'label' => $this->l('Access token:'),
						'name' => 'PROISTC_TOKEN',
						'size' => '100',
						'desc' => $this->l('You need to get your own access token from Instagram.')
					),
           			array(
						'type' => 'text',
						'label' => $this->l('Number of Images:'),
						'name' => 'PROISTC_LIMIT',
						'size' => '100',
						'desc' => $this->l('The number display images.')
					)
				),
				'submit' => array(
					'title' => $this->l('Save'),
					'class' => 'button'
				)	
		);
		
				$helper = new HelperForm();

    // Module, t    oken and currentIndex
    $helper->module = $this;
    $helper->name_controller = $this->name;
    $helper->token = Tools::getAdminTokenLite('AdminModules');
    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

    // Language
    $helper->default_form_language = $default_lang;
    $helper->allow_employee_form_lang = $default_lang;

    // Title and toolbar
    $helper->title = $this->displayName;
	
    $helper->show_toolbar = true;        // false -> remove toolbar
    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
    $helper->submit_action = 'submit'.$this->name;
	
    $helper->toolbar_btn = array(
        'save' =>
        array(
            'desc' => $this->l('Save'),
            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                '&token='.Tools::getAdminTokenLite('AdminModules'),
        ),
        'back' => array(
            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
            'desc' => $this->l('Back to list')
        )
    );
				 
				// Load current value
				$helper->fields_value['PROISTC_ID'] = Configuration::get('PROISTC_ID');
				$helper->fields_value['PROISTC_LIMIT'] = Configuration::get('PROISTC_LIMIT');
				$helper->fields_value['PROISTC_TOKEN'] = Configuration::get('PROISTC_TOKEN');
				return $helper->generateForm($fields_form);
	}
				
			// Display module
		public function hookDisplayHome($params)
		{
		 $this->context->smarty->assign(
			array(
				'proistcid' => Configuration::get('PROISTC_ID'),
				'proistclimit' => Configuration::get('PROISTC_LIMIT'),
				'proistctoken' => Configuration::get('PROISTC_TOKEN')
				  )
				  );
				return $this->display(__FILE__, 'proinstagramcarousel.tpl');
		}
			
				public function hookDisplayHeader()
				{
				  $this->context->controller->addCSS($this->_path.'css/proinstagramcarousel.css', 'all');
				  $this->context->controller->addJS($this->_path.'js/proinstagramcarousel.js', 'all');
				} 
}
?>