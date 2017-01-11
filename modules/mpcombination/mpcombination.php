<?php
if (!defined('_PS_VERSION_'))
	exit;

include_once dirname(__FILE__).'/classes/Mpcombinationcalssinclude.php';
include_once dirname(__FILE__).'/../marketplace/classes/MarketplaceClassInclude.php';

class mpcombination extends Module
{
	const INSTALL_SQL_FILE = 'install.sql';
	public function __construct()
	{
		$this->name = 'mpcombination';
		$this->tab = 'front_office_features';
		$this->version = '2.0.0';
		$this->author = 'Webkul';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->dependencies = array('marketplace');
		parent::__construct();
		$this->displayName = $this->l('Marketplace Product Combination');
		$this->description = $this->l('Seller can create combination for product and can create own combination.');
	}

	public function callAssociateModuleToShop()
	{
		$module_id = Module::getModuleIdByName($this->name);
		Configuration::updateGlobalValue('MPCOMBINATION_MODULE_ID', $module_id);
		return true;
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit'))
			Configuration::updateValue('MP_PRODUCT_ATTRIBUTE_ACTIVATION', Tools::getValue('MP_PRODUCT_ATTRIBUTE_ACTIVATION'));

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
						'label' => $this->l('Seller can create or edit attributes'),
						'name' => 'MP_PRODUCT_ATTRIBUTE_ACTIVATION',
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
						'hint' => $this->l('If No, Seller can not create or edit attributes. Admin have to approve first.')
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
			'MP_PRODUCT_ATTRIBUTE_ACTIVATION' => Tools::getValue('MP_PRODUCT_ATTRIBUTE_ACTIVATION',
												Configuration::get('MP_PRODUCT_ATTRIBUTE_ACTIVATION'))
		);
	}

	public function hookDisplayMpmenuhookext()
	{
		if (Configuration::get('MP_PRODUCT_ATTRIBUTE_ACTIVATION'))
		{			
			$obj_mp_shop = new MarketplaceShop();
			$shop_det = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($this->context->customer->id);	
			if ($shop_det && $shop_det['is_active'])
			{
				$shop_id = $shop_det['id'];
				$this->context->smarty->assign('id_shop',$shop_id);
				$this->context->smarty->assign('mp_menu',0);
				return $this->display(__FILE__, 'product_attribute_link.tpl');
			}
		}
	}

	public function hookDisplayMpmyaccountmenuhook()
	{
		if (Configuration::get('MP_PRODUCT_ATTRIBUTE_ACTIVATION'))
		{			
			$obj_mp_shop = new MarketplaceShop();
			$shop_det = $obj_mp_shop->getMarketPlaceShopInfoByCustomerId($this->context->customer->id);
			if ($shop_det && $shop_det['is_active'])
			{
				$shop_id = $shop_det['id'];
				$this->context->smarty->assign('id_shop',$shop_id);
				$this->context->smarty->assign('mp_menu',1);
				return $this->display(__FILE__, 'product_attribute_link.tpl');
			}
		}
		
	}

	public function hookDisplayMpProductOption()
	{	
		return $this->display(__FILE__, 'add_combination_option.tpl');
	}

	public function hookDisplayMpUpdateProductOption()
	{
		return $this->display(__FILE__, 'update_combination_option.tpl');
	}

	public function hookDisplayMpaddproducttabhook()
	{
		return $this->display(__FILE__, 'add_product_combination.tpl');
	}

	public function hookDisplayMpupdateproducttabhook()
	{
		$flag = 0;
		$mp_product_id = Tools::getValue('id');
		$obj_product_create = new Mpproductattribute();
		$combination_detail = $obj_product_create->getAttributesResume($this->context->shop->id, $mp_product_id, 1);

		//Page Link
		$passing_data = array('mp_product_id'=>Tools::getValue('id'));
		$obj_link = new Link();		
		$createcombination_link = $obj_link->getModuleLink('mpcombination', 'mpattributemanage', $passing_data);

		$obj_seller_product = new SellerProductDetail($mp_product_id);
		$mp_shop_product = $obj_seller_product->getMarketPlaceShopProductDetailBYmspid($mp_product_id);
		if ($mp_shop_product)
		{
			$ps_product_id = $mp_shop_product['id_product'];
			$obj_product = new Product($ps_product_id);
			$is_virtual_product = $obj_product->is_virtual;
			if ($is_virtual_product == 1)
				$flag = 1;
			else
				$flag = 0;
		}
		/*else
		{
			$ismpinstall = Module::isInstalled('mpvirtualproduct');
			if ($ismpinstall)
			{
				include_once dirname(__FILE__).'/../mpvirtualproduct/classes/MarketplaceVirtualProduct.php';
				$obj_virtual_porudct = new MarketplaceVirtualProduct();
				$is_virtual_product = $obj_virtual_porudct->isMpProductIsVirtualProduct($mp_shop_product);
				if ($is_virtual_product)
					$flag = 1;
				else
					$flag = 0;
			}
			else
				$flag = 0;
			//only active product has a combination so set $flag=1			
		}*/
		
		/*$mpproductattibute_link = $obj_link->getModuleLink('mpcombination', 'mpattributeedit', array('id'=>Tools::getValue('id')));
		$this->context->smarty->assign('mpproductattibute_link', $mpproductattibute_link);
		$mpproductattibutesave_link = $obj_link->getModuleLink('mpcombination', 'mpattributeeditsave');
		$this->context->smarty->assign('mpproductattibutesave_link', $mpproductattibutesave_link);*/

		if ($flag == 0)
		{
			//$mp_prod_id = Tools::getValue('id');
			$obj_seller_prod_det = new SellerProductDetail();
			$mp_prod_det = $obj_seller_prod_det->getMarketPlaceShopProductDetailBYmspid($mp_product_id);
			$id_shop = $mp_prod_det['id_shop'];
			$paramms = array('shop'=>$id_shop);
			$link = new Link();
			$pro_upd_link = $link->getModuleLink('marketplace', 'productupdate',$paramms);
			$this->context->smarty->assign('pro_upd_link2', $pro_upd_link);			
			$this->context->smarty->assign('js_root', _PS_JS_DIR_);				
			$this->context->smarty->assign('mp_product_id', Tools::getValue('id'));			
			$this->context->smarty->assign('createcombination_link', $createcombination_link);
			$obj_product = new Mpproductattribute();
			$combination_detail = $obj_product->getAttributesResume($this->context->shop->id, Tools::getValue('id'), 1);				
			$attribute_delete_ajax_link  = $obj_link->getModuleLink('mpcombination', 'deleteCombination');
			if ($combination_detail)
				$this->context->smarty->assign('combination_detail', $combination_detail);
			else
				$this->context->smarty->assign('combination_detail', -1);

			$this->context->smarty->assign('attribute_delete_ajax_link', $attribute_delete_ajax_link);
			$this->context->smarty->assign('admin_img_path', _PS_ADMIN_IMG_);
			$this->context->smarty->assign('modules_dir', _MODULE_DIR_);
			return $this->display(__FILE__, 'update_product_combination.tpl');
		}
	}

	public function hookActionToogleProductStatus($params)
	{
		$mp_product_id = Tools::getValue('id');
		$main_product_id = $params['main_product_id'];		
		$obj_product_attribute = new Mpproductattribute();
		$attr_values = $obj_product_attribute->getAttributeValuesForMpProduct($mp_product_id);	
		$obj_combination = new Combination();
		foreach ($attr_values as $attr_val)
		{
			$obj_combination->id_product = $main_product_id;
			$obj_combination->reference = $attr_val['mp_reference'];
			$obj_combination->supplier_reference = $attr_val['mp_supplier_reference'];
			$obj_combination->location = $attr_val['mp_location'];
			$obj_combination->ean13 = $attr_val['mp_ean13'];
			$obj_combination->upc = $attr_val['mp_upc'];
			$obj_combination->wholesale_price = $attr_val['mp_wholesale_price'];
			$obj_combination->price = $attr_val['mp_price'];
			$obj_combination->ecotax = $attr_val['mp_ecotax'];
			$obj_combination->quantity = $attr_val['mp_quantity'];
			$obj_combination->weight = $attr_val['mp_weight'];
			$obj_combination->unit_price_impact = $attr_val['mp_unit_price_impact'];
			$obj_combination->default_on = $attr_val['mp_default_on'];
			$obj_combination->minimal_quantity = $attr_val['mp_minimal_quantity'];
			$obj_combination->available_date = $attr_val['mp_available_date'];
			$obj_combination->add();
			$id_product_attribute = $obj_combination->id;
			$id_ps_product_attribute = $obj_product_attribute->getPsAttributeIdForMpProduct($attr_val['mp_id_product_attribute']);	
			foreach ($id_ps_product_attribute as $ps_attr)
			{
				$obj_product_attribute->insertIntoPsProductCombination($ps_attr['id_ps_attribute'], $id_product_attribute);
				$obj_product_attribute->insertIntoPsAttrImpact($main_product_id, $ps_attr['id_ps_attribute'], $attr_val['mp_weight'], $attr_val['mp_price']);
			}
			StockAvailable::setQuantity($main_product_id, $id_product_attribute, $attr_val['mp_quantity']);
			
			Mpcombinationmap::insertIntoMpCombinationMap($id_product_attribute, $attr_val['mp_id_product_attribute'], $mp_product_id, $main_product_id);
			
			$atrribute_images = $obj_product_attribute->getAttributeImages($attr_val['mp_id_product_attribute']);
			$obj_ps_mp_img_map = new Mppsimagemap();
			$id_images = array();
			if (!empty($atrribute_images))
			{
				foreach ($atrribute_images as $images)
				{
					$id_ps_image = $obj_ps_mp_img_map->getPsImageIdFromMpImageId($images['mp_id_image']);
					$id_images[] = $id_ps_image;
				}
				$id_ps_product_attribute = $obj_product_attribute->getPsAttriIdFromMpAtrriId($attr_val['mp_id_product_attribute']);
				if ($id_ps_product_attribute)
				{
					$obj_comb = new Combination($id_ps_product_attribute);
					$obj_comb->setImages($id_images);
				}
			}
		}
	}

	public function hookActionPsMpImageMap($params)
	{		
		$obj_ps_mp_img_map = new Mppsimagemap();
		$obj_ps_mp_img_map->mp_id_product = $params['mp_product_id'];
		$obj_ps_mp_img_map->mp_id_image = $params['mp_id_image'];
		$obj_ps_mp_img_map->id_ps_product = $params['ps_id_product'];
		$obj_ps_mp_img_map->id_ps_image = $params['ps_id_image'];
		$obj_ps_mp_img_map->add();
	}

	public function install() 
	{
		if (Module::isInstalled('mpbookingsystem'))
		{
			$this->_errors[] = Tools::displayError('You can not install this module if MpBookingSystem module is already installed.');
			return false;
		}
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		else if (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);

		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);
		foreach ($sql as $query)
			if ($query)
				if (!Db::getInstance()->execute(trim($query)))
					return false;
		if (!parent::install()
			|| !$this->registerHook('actionToogleProductStatus')
			|| !$this->registerHook('displayMpproductdescriptionfooterhook')
			|| !$this->registerHook('displayMpProductOption')
			|| !$this->registerHook('displayMpUpdateProductOption')
			|| !$this->registerHook('displayMpaddproducttabhook')
			|| !$this->registerHook('displayMpupdateproducttabhook')
			|| !$this->registerHook('displayMpmenuhookext')
			|| !$this->registerHook('displayMpmyaccountmenuhook')
			|| !$this->registerHook('actionPsMpImageMap'))
			return false;
		else 
		{
			Configuration::updateValue('MP_ATTRIBUTE_COMBINATION', '0');
			if (!$this->callAssociateModuleToShop())
				return false;
			else
				return true;
		}
	}
	
	public function setMassUploadCombinationVal()
	{
		if(Configuration::get('MASS_UPLOAD_COMBINATION_APPROVE'))
			return Configuration::updateValue('MASS_UPLOAD_COMBINATION_APPROVE', '0');
		else
			return true;
	}

	public function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS
            `'._DB_PREFIX_.'mp_product_attribute`,
            `'._DB_PREFIX_.'mp_product_attribute_image`,
            `'._DB_PREFIX_.'mp_product_attribute_combination`,
            `'._DB_PREFIX_.'mp_product_attribute_shop`,
            `'._DB_PREFIX_.'mp_stock_available`,
            `'._DB_PREFIX_.'mp_combination_map`,
            `'._DB_PREFIX_.'mp_ps_image_map`,
            `'._DB_PREFIX_.'mp_attribute_impact`');
    }

	public function uninstall()
	{
		if (!parent::uninstall() || !$this->setMassUploadCombinationVal() || !$this->deleteTables())
			return false;
		return true;
	}
}
?>