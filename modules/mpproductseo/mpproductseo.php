<?php
if (!defined('_PS_VERSION_'))
	exit;

include_once dirname(__FILE__).'/../marketplace/classes/MarketplaceClassInclude.php';
include_once 'classes/MarketplaceProductSeo.php';

class MpProductSeo extends Module 
{
	const INSTALL_SQL_FILE = 'install.sql';
	public function __construct() 
	{
		$this->name = 'mpproductseo';
		$this->tab = 'front_office_features';
		$this->version = '2.0.0';
		$this->author = 'Webkul';
		$this->need_instance = 1;
		$this->dependencies = array('marketplace');
		parent::__construct();
		$this->displayName = $this->l('Marketplace Product SEO');
		$this->description = $this->l('Markeplace seller can add their own meta description, title to products');
	}
	
	
	public function hookDisplayMpProductOption() 
	{
		return $this->display(__FILE__, 'add_product_seo.tpl');
	}

	public function hookDisplayMpaddproducttabhook()
	{
		return $this->display(__FILE__, 'add_product_seo_form.tpl');
	}

	public function hookDisplayMpaddproductfooterhook()
	{
		if (Tools::getValue('controller') == 'AdminSellerProductDetail')
		{
			$this->context->smarty->assign('admin', 1);
			return $this->hookDisplayMpaddproducttabhook();
		}
	}
	
	public function hookDisplayMpupdateproducttabhook()
	{
		if ($mp_product_id = Tools::getValue('id'))
		{
			$obj_product_seo = new MarketplaceProductSeo();
			$meta_info = $obj_product_seo->getMetaInfo($mp_product_id);
			if ($meta_info)
			{
				$this->context->smarty->assign('editproduct', Tools::getValue('editproduct'));
				$this->context->smarty->assign('meta_info', $meta_info);
		 	}
			return $this->display(__FILE__, 'add_product_seo_form.tpl');
		}
	}

	public function hookDisplayMpupdateproductfooterhook()
	{
		if (Tools::getValue('controller') == 'AdminSellerProductDetail')
		{
			$this->context->smarty->assign('admin', 1);
			return $this->hookDisplayMpupdateproducttabhook();
		}
	}

	public function hookActionAddproductExtrafield($params) 
	{
		$marketplace_product_id = $params['marketplace_product_id'];
		if ($marketplace_product_id)
		{
			$meta_title = Tools::getValue('meta_title');
			$meta_desc = Tools::getValue('meta_desc');
			$product_name = Tools::getValue('product_name');
			$friendly_url = Tools::getValue('friendly_url');

			if ($friendly_url == "")
				$friendly_url = $product_name;
			else
				$friendly_url = $friendly_url;

			$obj_mp_product_seo = new MarketplaceProductSeo();
			$obj_mp_product_seo->mp_product_id = $marketplace_product_id;
			$obj_mp_product_seo->meta_title = $meta_title;
			$obj_mp_product_seo->meta_description = $meta_desc;
			$obj_mp_product_seo->friendly_url = Tools::link_rewrite($friendly_url);
			$obj_mp_product_seo->save();

			$obj_mpshop_pro = new MarketplaceShopProduct();
			$product_detail = $obj_mpshop_pro->findMainProductIdByMppId($marketplace_product_id);
			$ps_product_id = $product_detail['id_product'];
			if (!empty($ps_product_id))
			{
				$obj_product = new Product($ps_product_id);
				foreach (Language::getLanguages(true) as $lang)
				{
					$obj_product->meta_description[$lang['id_lang']] = $meta_desc;
					$obj_product->meta_title[$lang['id_lang']] = $meta_title;
					$obj_product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($friendly_url);
				}
				$obj_product->save();
			}
		}		
	}
	

	public function hookActionBeforeAddproduct()
	{
		$friendly_url = Tools::getValue('friendly_url');
		if (!Validate::isCatalogName($friendly_url))
			$this->context->controller->errors[] = $this->l('Friendly Url is invalid');
	}

	public function hookActionBeforeUpdateproduct()
	{
		$friendly_url = Tools::getValue('friendly_url');
		if (!Validate::isCatalogName($friendly_url))
			$this->context->controller->errors[] = $this->l('Friendly Url is invalid');
	}



	public function hookActionUpdateproductExtrafield($params) 
	{
		$mp_product_id = $params['marketplace_product_id'];
		if ($mp_product_id)
		{
			$obj_mpshop_pro = new MarketplaceShopProduct();
			$product_detail = $obj_mpshop_pro->findMainProductIdByMppId($mp_product_id);
			$ps_product_id = $product_detail['id_product'];
			$meta_title = Tools::getValue('meta_title');
			$meta_desc = Tools::getValue('meta_desc');
			$product_name = Tools::getValue('product_name');
			$friendly_url = Tools::getValue('friendly_url');

			if($friendly_url == "")
				$friendly_url = $product_name;
			else
				$friendly_url = $friendly_url;
		
			$obj_mp_product_seo = new MarketplaceProductSeo();
			$meta_info = $obj_mp_product_seo->getMetaInfo($mp_product_id);
			if ($meta_info)
				$obj_mp_product_seo = new MarketplaceProductSeo($meta_info['id']);
	
			$obj_mp_product_seo->mp_product_id = $mp_product_id;
			$obj_mp_product_seo->meta_title = $meta_title;
			$obj_mp_product_seo->meta_description = $meta_desc;
			$obj_mp_product_seo->friendly_url = Tools::link_rewrite($friendly_url);
			$obj_mp_product_seo->save();
			
			if (!empty($ps_product_id))
			{
				$obj_product = new Product($ps_product_id);
				foreach (Language::getLanguages(true) as $lang)
				{
					$obj_product->meta_description[$lang['id_lang']] = $meta_desc;
					$obj_product->meta_title[$lang['id_lang']] = $meta_title;
					$obj_product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($friendly_url);
				}

				$obj_product->save();
			}
		}
	}

	public function hookActionToogleProductStatus($params) 
	{
		$mp_product_id = Tools::getValue('id');
		$ps_product_id = $params['main_product_id'];
		$obj_product_seo = new MarketplaceProductSeo();
		$meta_info = $obj_product_seo->getMetaInfo($mp_product_id);
		if (!empty($meta_info))
		{
			$obj_product = new Product($ps_product_id);
			foreach (Language::getLanguages(true) as $lang)
			{
				$obj_product->meta_description[$lang['id_lang']] = $meta_info['meta_description'];
				$obj_product->meta_title[$lang['id_lang']] = $meta_info['meta_title'];
				$obj_product->link_rewrite[$lang['id_lang']] = Tools::link_rewrite($meta_info['friendly_url']);
			}
			$obj_product->save();
		}
	}
	
	public function install() 
	{
		if (!file_exists(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);
		else if (!$sql = Tools::file_get_contents(dirname(__FILE__).'/'.self::INSTALL_SQL_FILE))
			return (false);

		$sql = str_replace(array('PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
		$sql = preg_split("/;\s*[\r\n]+/", $sql);

		foreach ($sql as $query)
			if($query)
				if(!Db::getInstance()->execute(trim($query)))
					return false;

		if (!parent::install() 		
			|| !$this->registerHook('displayMpProductOption') 
			|| !$this->registerHook('displayMpaddproducttabhook') 
			|| !$this->registerHook('displayMpupdateproducttabhook') 
			|| !$this->registerHook('displayMpupdateproductfooterhook') 
			|| !$this->registerHook('actionBeforeAddproduct')
			|| !$this->registerHook('actionAddproductExtrafield')
			|| !$this->registerHook('actionUpdateproductExtrafield')
			|| !$this->registerHook('actionBeforeUpdateproduct')
			|| !$this->registerHook('actionToogleProductStatus')
			|| !$this->registerHook('displayMpaddproductfooterhook')
			)
			return false;

		return true;
	}

	public function dropTable()
	{
		return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'mp_product_seo`');
	}

	public function uninstall()
	{
		if (!parent::uninstall() || !$this->dropTable()) 
			return false; 
		return true;
	}
}
?>