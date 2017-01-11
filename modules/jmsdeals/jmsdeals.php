<?php
/**
* 2007-2014 PrestaShop
*
* Jms Brand logos
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2014 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

if (!defined('_PS_VERSION_'))
	exit;
include_once(_PS_MODULE_DIR_.'jmsdeals/JmsDeal.php');
class JmsDeals extends Module
{
	private $_html = '';
	private $_postErrors = array();

	public function __construct()
	{
		$this->name = 'jmsdeals';
		$this->tab = 'front_office_features';
		$this->version = '1.1.0';
		$this->author = 'Joommasters';
		$this->need_instance = 0;		
		$this->secure_key = Tools::encrypt($this->name);
		$this->bootstrap = true;

		parent::__construct();

		$this->displayName = $this->l('Jms Deals.');
		$this->description = $this->l('Displays Deals.');
	}

	public function install()
	{
		if (parent::install() && $this->registerHook('displayTopContent') && $this->registerHook('header') && $this->registerHook('actionShopDataDuplication'))
		{
			$res = Configuration::updateValue('JMS_DEALS_AUTO', 1);
			$res &= Configuration::updateValue('JMS_DEALS_ITEMS', 3);
			$res &= Configuration::updateValue('JMS_DEALS_ITEMS_SHOW', 1);
			/* Creates tables */
			$res &= $this->createTables();			
			return $res;	
		}		
		return false;		
	}
	public function uninstall()
	{
		/* Deletes Module */
		if (parent::uninstall())
		{
			/* Deletes tables */
			$res = $this->deleteTables();
			/* Unsets configuration */
			$res &= Configuration::deleteByName('JMS_DEALS_AUTO');
			$res &= Configuration::deleteByName('JMS_DEALS_ITEMS');			
			$res &= Configuration::deleteByName('JMS_DEALS_ITEMS_SHOW');			
			return $res;
		}
		return false;
	}
	/**
	 * Creates tables
	 */
	protected function createTables()
	{
		/* Brands */
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsdeals` (
				`id_deal` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_shop` int(10) unsigned NOT NULL,
				PRIMARY KEY (`id_deal`, `id_shop`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');		
		$res &= Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsdeals_items` (
			  `id_deal` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `id_product` int(10) unsigned NOT NULL DEFAULT \'0\',	
			  `expire_time` datetime NOT NULL,
			  `ordering` int(10) unsigned NOT NULL DEFAULT \'0\',			  
			  `active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
			  PRIMARY KEY (`id_deal`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');		

		return $res;
	}

	/**
	 * deletes tables
	 */
	protected function deleteTables()
	{
		$deals = $this->getDeals();		
		foreach ($deals as $deal)	
		{				
			$to_del = new JmsDeal($deal['id_deal']);
			$to_del->delete();
		}		
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmsdeals`;');
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmsdeals_items`;');		
		return true;
	}
	
	public function getContent()
	{
		$this->_html .= $this->headerHTML();

		/* Validate & process */
		if (Tools::isSubmit('submitDeal') || Tools::isSubmit('delete_id_deal') || Tools::isSubmit('submitDeals') || Tools::isSubmit('changeStatus'))
		{
			if ($this->_postValidation())
			{
				$this->_postProcess();
				$this->_html .= $this->renderForm();
				$this->_html .= $this->renderList();
			}
			else
				$this->_html .= $this->renderAddForm();

			$this->clearCache();
		}
		elseif (Tools::isSubmit('addDeal') || (Tools::isSubmit('id_deal') && $this->dealExists((int)Tools::getValue('id_deal'))))
			$this->_html .= $this->renderAddForm();
		else
		{
			$this->_html .= $this->renderForm();
			$this->_html .= $this->renderList();
		}

		return $this->_html;
	}
	
	private function _postValidation()
	{
		$errors = array();

		/* Validation for Slider configuration */
		if (Tools::isSubmit('changeStatus'))
		{
			if (!Validate::isInt(Tools::getValue('id_deal')))
				$errors[] = $this->l('Invalid Deal');
		}
		/* Validation for Slide */
		elseif (Tools::isSubmit('submitDeal'))
		{			
			/* Checks position */
			if (!Validate::isInt(Tools::getValue('ordering')) || (Tools::getValue('ordering') < 0))
				$errors[] = $this->l('Invalid product ordering');
			/* If edit : checks id_slide */
			if (Tools::isSubmit('id_deal'))
			{					
				if (!Validate::isInt(Tools::getValue('id_deal')) && !$this->dealExists(Tools::getValue('id_deal')))
					$errors[] = $this->l('Invalid id_deal');
			}			
			
		} /* Validation for deletion */
		elseif (Tools::isSubmit('delete_id_deal') && (!Validate::isInt(Tools::getValue('delete_id_deal')) || !$this->dealExists((int)Tools::getValue('delete_id_deal'))))
			$errors[] = $this->l('Invalid id_deal');

		/* Display errors if needed */
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));
			return false;
		}

		/* Returns if validation is ok */
		return true;
	}
	private function _postProcess()
	{
		$errors = array();

		/* Processes Slider */
		if (Tools::isSubmit('submitDeals'))
		{
			$res = Configuration::updateValue('JMS_DEALS_AUTO', (int)(Tools::getValue('JMS_DEALS_AUTO')));
			$res &= Configuration::updateValue('JMS_DEALS_ITEMS', (int)(Tools::getValue('JMS_DEALS_ITEMS')));						
			$res &= Configuration::updateValue('JMS_DEALS_ITEMS_SHOW', (int)(Tools::getValue('JMS_DEALS_ITEMS_SHOW')));						
			$this->clearCache();			
			if (!$res)
				$errors[] = $this->displayError($this->l('The configuration could not be updated.'));
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=6&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		} /* Process Slide status */
		elseif (Tools::isSubmit('changeStatus') && Tools::isSubmit('id_deal'))
		{
			$deal = new JmsDeal((int)Tools::getValue('id_deal'));
			if ($deal->active == 0)
				$deal->active = 1;
			else
				$deal->active = 0;
			$res = $deal->update();
			$this->clearCache();
			$this->_html .= ($res ? $this->displayConfirmation($this->l('status updated')) : $this->displayError($this->l('The status could not be updated.')));
		} /* Processes deal */
		elseif (Tools::isSubmit('submitDeal'))
		{			
			/* Sets ID if needed */			
			if (Tools::getValue('id_deal') && $this->dealExists(Tools::getValue('id_deal')))
			{					
				$deal = new JmsDeal((int)Tools::getValue('id_deal'));				
				if (!Validate::isLoadedObject($deal))
				{
					$this->_html .= $this->displayError($this->l('Invalid id_deal'));
					return;
				}				
			}
			else
				$deal = new JmsDeal();
			/* Sets ordering */			
			$deal->id_product = (int)Tools::getValue('id_product');
			$deal->ordering = (int)Tools::getValue('ordering');			
			$deal->expire_time = Tools::getValue('expire_time');
			/* Sets active */
			$deal->active = (int)Tools::getValue('active');			
		//	print_r($deal); exit;
			/* Processes if no errors  */			
			if (!$errors)
			{				
				/* Adds */
				if (!Tools::getValue('id_deal'))
				{					
					if (!$deal->add())
						$errors[] = $this->displayError($this->l('The deal could not be added.'));
				}
				/* Update */
				elseif (!$deal->update())
					$errors[] = $this->displayError($this->l('The deal could not be updated.'));
				$this->clearCache();
			}
		} /* Deletes */
		elseif (Tools::isSubmit('delete_id_deal'))
		{
			$deal = new JmsDeal((int)Tools::getValue('delete_id_deal'));
			$res = $deal->delete();
			$this->clearCache();
			if (!$res)
				$this->_html .= $this->displayError('Could not delete');
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		}

		/* Display errors if needed */
		if (count($errors))
			$this->_html .= $this->displayError(implode('<br />', $errors));
		elseif (Tools::isSubmit('submitDeal') && Tools::getValue('id_deal'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
		elseif (Tools::isSubmit('submitDeal'))
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name);
	}
	public function clearCache()
	{
		$this->_clearCache('jmsdeals.tpl');
	}
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
		INSERT IGNORE INTO '._DB_PREFIX_.'jmsdeals (id_deal, id_shop)
		SELECT id_deal, '.(int)$params['new_id_shop'].'
		FROM '._DB_PREFIX_.'jmsdeals
		WHERE id_shop = '.(int)$params['old_id_shop']);
		$this->clearCache();
	}
	public function headerHTML()
	{
		if (Tools::getValue('controller') != 'AdminModules' && Tools::getValue('configure') != $this->name)
			return;

		$this->context->controller->addJqueryUI('ui.sortable');
		/* Style & js for fieldset 'slides configuration' */
		$html = '<script type="text/javascript">
			$(function() {
				var $mySlides = $("#slides");
				$mySlides.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateSlidesOrdering";
						$.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?secure_key='.$this->secure_key.'", order);
						}
					});
				$mySlides.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			});
		</script>';

		return $html;
	}
	
	public function getNextPosition()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT MAX(hss.`position`) AS `next_position`
			FROM `'._DB_PREFIX_.'jmsdeals_items` hss, `'._DB_PREFIX_.'jmsdeals` hs
			WHERE hss.`id_deal` = hs.`id_deal` AND hs.`id_shop` = '.(int)$this->context->shop->id
		);

		return (++$row['next_position']);
	}
	
	public function getDeals($active = null)
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		
		$deals = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_deal` as id_deal, hss.`id_product`, hss.`expire_time`, hss.`ordering`, hss.`active`
			FROM '._DB_PREFIX_.'jmsdeals hs
			LEFT JOIN '._DB_PREFIX_.'jmsdeals_items hss ON (hs.`id_deal` = hss.`id_deal`)			
			WHERE `id_shop` = '.(int)$id_shop.		
			($active ? ' AND hss.`active` = 1' : ' ').'
			ORDER BY hss.`ordering`'
		);
		
		$total_deals = count($deals);
		for ($i = 0; $i < $total_deals; $i++) 
		{
			$row = $this->getProductName($deals[$i]['id_product']);
			$deals[$i]['product_name'] = $row['name'];			
		}
		return $deals;			
	}
	
	public function getDealToShow()
	{
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
	
		$deals = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT hs.`id_deal` as `id_deal`, hss.`id_product`, hss.`expire_time`, hss.`ordering`, hss.`active`
			FROM '._DB_PREFIX_.'jmsdeals hs
			LEFT JOIN '._DB_PREFIX_.'jmsdeals_items hss ON (hs.`id_deal` = hss.`id_deal`)
			WHERE `id_shop` = '.(int)$id_shop.' AND hss.`active` = 1
			ORDER BY hss.`ordering`'
		);
	
		$products = array();
		$total_deals = count($deals);
		for ($i = 0; $i < $total_deals; $i++)
		{	
			$sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, MAX(product_attribute_shop.id_product_attribute) id_product_attribute, product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, MAX(image_shop.`id_image`) id_image,
					il.`legend`, m.`name` AS manufacturer_name, 
					DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(),
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).'
						DAY)) > 0 AS new, product_shop.price AS orderprice 
				FROM `'._DB_PREFIX_.'product` p				
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
				ON (p.`id_product` = pa.`id_product`)
				'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1').'
				'.Product::sqlStock('p', 'product_attribute_shop', false, $this->context->shop).'				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image` i
					ON (i.`id_product` = p.`id_product`)'.
								Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` = '.$deals[$i]['id_product'].' AND product_shop.`id_shop` = '.(int)$id_shop.'
					 AND product_shop.`active` = 1'
					.' GROUP BY product_shop.`id_product`';
			$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
						
			$products[] = Product::getProductProperties($id_lang, $row);
		}				
		return $products;		 
	}
	public function displayStatus($id_deal, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&id_deal='.(int)$id_deal.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}

	public function dealExists($id_deal)
	{
		$req = 'SELECT hs.`id_deal` as id_deal
				FROM `'._DB_PREFIX_.'jmsdeals` hs
				WHERE hs.`id_deal` = '.(int)$id_deal;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}

	public function renderList()
	{
		$deals = $this->getDeals();
		foreach ($deals as $key => $deal)
			$deals[$key]['status'] = $this->displayStatus($deal['id_deal'], $deal['active']);

		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'deals' => $deals
			)
		);

		return $this->display(__FILE__, 'list.tpl');
	}
	
	public function renderAddForm()
	{
		$this->context->controller->addCSS(($this->_path).'css/admin_style.css', 'all');	
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Deal informations'),
					'icon' => 'icon-cogs'
				),
				'input' => array(									
					array(
						'type' => 'product_search',
						'label' => $this->l('Product'),
						'name' => 'title'
					),					
					array(
						'type' => 'datetime',
						'label' => $this->l('Expire Time'),
						'name' => 'expire_time',
						'lang' => true,
					),										
					array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'active',
						'is_bool' => true,
						'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->l('Yes')
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->l('No')
							)
						),
					),
				),
				'submit' => array(
					'title' => $this->l('Save'),
				)
			),
		);

		if (Tools::isSubmit('id_deal') && $this->dealExists((int)Tools::getValue('id_deal')))
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_deal');		

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitDeal';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));		
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getAddFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);

		$helper->override_folder = '/';		
		return $helper->generateForm(array($fields_form));
	}

	public function renderForm()
	{	
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(					
					array(
						'type' => 'switch',
						'label' => $this->l('Auto Play'),
						'name' => 'JMS_DEALS_AUTO',
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
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number of Items'),
						'name' => 'JMS_DEALS_ITEMS',						
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number of Items Show'),
						'name' => 'JMS_DEALS_ITEMS_SHOW',						
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
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();

		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitDeals';		
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
			'JMS_DEALS_AUTO' => Tools::getValue('JMS_DEALS_AUTO', Configuration::get('JMS_DEALS_AUTO')),
			'JMS_DEALS_ITEMS' => Tools::getValue('JMS_DEALS_ITEMS', Configuration::get('JMS_DEALS_ITEMS')),
			'JMS_DEALS_ITEMS_SHOW' => Tools::getValue('JMS_DEALS_ITEMS_SHOW', Configuration::get('JMS_DEALS_ITEMS_SHOW')),
		);
	}

	public function getAddFieldsValues()
	{
		$fields = array();		
		
		if (Tools::isSubmit('id_deal') && $this->dealExists((int)Tools::getValue('id_deal')))
		{
			$deal = new JmsDeal((int)Tools::getValue('id_deal'));	
			$row = $this->getProductName($deal->id_product);	
			$fields['id_deal'] = (int)Tools::getValue('id_deal', $deal->id);
			$fields['id_product'] = (int)Tools::getValue('id_product', $deal->id_product);
			$fields['product_name'] = $row['name']; 
			$fields['expire_time'] = Tools::getValue('expire_time', $deal->expire_time);
		}
		else
			$deal = new JmsDeal();

		$fields['active'] = Tools::getValue('active', $deal->active);
		return $fields;
	}
	public function getProductName($id_product) 
	{
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		$sql = 'SELECT pl.`name` FROM `'._DB_PREFIX_.'product_lang` pl WHERE id_product = '.$id_product.' AND id_shop = '.$id_shop.' AND id_lang = '.$id_lang;		
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
		return $row;		
	}
	public function hookDisplayHeader()	
	{						
		$this->context->controller->addCSS(($this->_path).'views/css/style.css', 'all');
		$this->context->controller->addJS(($this->_path).'views/js/jquery.plugin.js', 'all');
		$this->context->controller->addJS(($this->_path).'views/js/jquery.countdown.js', 'all');
		$this->context->controller->addJS(($this->_path).'views/js/jmscountdown.js', 'all');
	}
		
	public function hookTop() 
	{								
		$deals = $this->getDeals();
		$products = $this->getDealToShow();				
		$root_url = _PS_BASE_URL_.__PS_BASE_URI__;
		$this->smarty->assign(array(
			'deals' => $deals,
			'products' => $products,
			'root_url' => $root_url,
			'items_show' => Configuration::get('JMS_DEALS_ITEMS_SHOW'),
			'items' => Configuration::get('JMS_DEALS_ITEMS'),
			'auto' => Configuration::get('JMS_DEALS_AUTO')
		));		
		return $this->display(__FILE__, 'jmsdeals.tpl');
	}
	public function hookdisplayTopContent()
	{	
		return $this->hookTop();
	}
}
