<?php
/**
* 2007-2015 PrestaShop
*
* Jms Gallery
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2015 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

if (!defined('_PS_VERSION_'))
	exit;
include_once(_PS_MODULE_DIR_.'jmsgallery/jmsgallerycat.php');
include_once(_PS_MODULE_DIR_.'jmsgallery/jmsgalleryitem.php');
class JmsGallery extends Module
{
	public function __construct()
	{
		$this->name = 'jmsgallery';
		$this->tab = 'front_office_features';
		$this->version = '1.1.0';
		$this->author = 'joommasters';
		$this->need_instance = 0;
		$this->bootstrap = true;
		$this->child	= array();

		parent::__construct();

		$this->displayName = $this->l('Jms Gallery.');
		$this->description = $this->l('Displays Gallery.');
	}

	public function install()
	{
		if (parent::install() && $this->registerHook('header'))
		{
			$res = true;
			$res &= Configuration::updateValue('TOTAL_ITEMS', 12);
			$res &= Configuration::updateValue('CONF_ACTIVE', 1);
			$res &= Configuration::updateValue('JMS_POSITION', 2);
			$res &= Configuration::updateValue('CONF_CATEGORY', 0);
			$res &= Configuration::updateValue('NUMBER_ITEMS', 6);

			$res = Configuration::updateValue('JMS_WIGHT_IMAGE', 387);
			$res &= Configuration::updateValue('JMS_HEIGHT_IMAGE', 362);
			$res &= Configuration::updateValue('IMG_ACTIVE', 1);

			$res &= Configuration::updateValue('autoPlay', 0);
			$res &= Configuration::updateValue('navigation', 1);
			$res &= Configuration::updateValue('pagination', 0);
			$res &= Configuration::updateValue('rewindNav', 1);
			$res &= Configuration::updateValue('scrollPerPage', 0);
			$res &= $this->createtable();
			$res &= $this->installSamples();
			return $res;
		}
		return false;
	}

	public function uninstall()
	{
		$this->clearCache();
		/* Deletes Module */
		if (parent::uninstall())
		{
			$res = true;
			$res = Configuration::deleteByName('JMS_WIGHT_IMAGE');
			$res &= Configuration::deleteByName('JMS_HEIGHT_IMAGE');
			$res &= Configuration::deleteByName('IMG_ACTIVE');

			$res &= Configuration::deleteByName('TOTAL_ITEMS');
			$res &= Configuration::deleteByName('CONF_ACTIVE');
			$res &= Configuration::deleteByName('JMS_POSITION');
			$res &= Configuration::deleteByName('CONF_CATEGORY');
			$res &= Configuration::deleteByName('NUMBER_ITEMS');

			$res &= Configuration::deleteByName('autoPlay');
			$res &= Configuration::deleteByName('navigation');
			$res &= Configuration::deleteByName('pagination');
			$res &= Configuration::deleteByName('rewindNav');
			$res &= Configuration::deleteByName('scrollPerPage');
			$res &= $this->deletetable();
			return $res;
		}
		return false;
	}

	protected function createtable()
	{
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsgallery_category` (
				`id_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  	`image` varchar(255) NOT NULL,
				`jmsparent` int(1) unsigned NOT NULL DEFAULT \'0\',
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',				
				
				PRIMARY KEY (`id_category`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');
		$res &= (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsgallery_category_lang` (
				`id_category` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_lang` int(10) unsigned NOT NULL,
				`name_category` varchar(255) NOT NULL,
			  	`description` text NOT NULL,		
				
				PRIMARY KEY (`id_category`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');
		$res &= (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsgallery_item` (
				`id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_category` int(10) unsigned NOT NULL,
			  	`image` varchar(255) NOT NULL,
				`date_add` date NOT NULL,
				`date_update` date NOT NULL,
				`view` int(100) unsigned NOT NULL,
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',				
				
				PRIMARY KEY (`id_item`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');
		$res &= (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmsgallery_item_lang` (
				`id_item` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_lang` int(10) unsigned NOT NULL,
				`title` varchar(255) NOT NULL,
			  	`description` text NOT NULL,				
				
				PRIMARY KEY (`id_item`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');
	}

	protected function deletetable()
	{
		$res = (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmsgallery_category`');
		$res &= (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmsgallery_category_lang`');
		$res &= (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmsgallery_item`');
		$res &= (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmsgallery_item_lang`');
		return $res;
	}

	private function installSamples()
	{
		$languages = Language::getLanguages(false);
		$res = true;
		// add gallery category
		$item = new JmsGalleryCat();
		$item->active = 1;
		$item->jmsparent = 0;
		$item->image = '';
		/* Sets each language fields */
		foreach ($languages as $language)
		{
			$item->name_category[$language['id_lang']] = 'Demo Gallery';
			$item->description[$language['id_lang']] = 'Present equine est, Loretta sit met nuns ut, consequent ferment-um torpor. Propellent seed consequent ligula.';
		}
		$res &= $item->add();		
		//add gallery item
		for	($i = 1; $i < 11; $i++)
		{
			$item = new JmsGalleryItem();	
			$item->active = 1;
			$item->id_category = 1;
			$item->date_add = '2010-12-12';
			$item->image = 'img'.$i.'.jpg';
			/* Sets each language fields */
			foreach ($languages as $language)
			{
				$item->title[$language['id_lang']] = 'Lores.';
				$item->description[$language['id_lang']] = 'Aliquam a massa ac ipsum lobortis pulvinar quis vel libero. Suspendisse porta pharetra porttitor. Donec ac augue sit amet sem imperdiet congue ac quis leo. Aliquam dictum egestas euismod.';
			}
			$res &= $item->add();
		}
		return $res;
	}
	public function getContent()
	{
		$this->_html .= $this->HtmlHeader();
		$this->_html .= $this->renderPathway();
		if (Tools::isSubmit('submitItem') || Tools::isSubmit('delete_id_item') || Tools::isSubmit('status_id_item') || Tools::isSubmit('list_id_category'))
		{
			if ($this->_postValidation())
			{
				$this->_postProcess();
				$this->_html .= $this->renderGalleryList();
			}
		}
		elseif (Tools::isSubmit('submitCat') || Tools::isSubmit('delete_id_cat') || Tools::isSubmit('status_id_category'))
		{
			if ($this->_postValidation())
			{
				$this->_postCatProcess();
				$this->_html .= $this->renderCatList();
			}
		}
		elseif (Tools::isSubmit('submitConf'))
		{
			if ($this->_confValidation())
			{
				$res = Configuration::updateValue('JMS_WIGHT_IMAGE', Tools::getValue('JMS_WIGHT_IMAGE'));
				$res &= Configuration::updateValue('JMS_HEIGHT_IMAGE', Tools::getValue('JMS_HEIGHT_IMAGE'));
				$res &= Configuration::updateValue('IMG_ACTIVE', Tools::getValue('IMG_ACTIVE'));

				$res &= Configuration::updateValue('TOTAL_ITEMS', Tools::getValue('TOTAL_ITEMS'));
				$res &= Configuration::updateValue('CONF_ACTIVE', Tools::getValue('CONF_ACTIVE'));
				$res &= Configuration::updateValue('JMS_POSITION', Tools::getValue('JMS_POSITION'));
				$res &= Configuration::updateValue('CONF_CATEGORY', Tools::getValue('CONF_CATEGORY'));
				$res &= Configuration::updateValue('NUMBER_ITEMS', Tools::getValue('NUMBER_ITEMS'));

				$res &= Configuration::updateValue('autoPlay', Tools::getValue('autoPlay'));
				$res &= Configuration::updateValue('navigation', Tools::getValue('navigation'));
				$res &= Configuration::updateValue('pagination', Tools::getValue('pagination'));
				$res &= Configuration::updateValue('rewindNav', Tools::getValue('rewindNav'));
				$res &= Configuration::updateValue('scrollPerPage', Tools::getValue('scrollPerPage'));
				$this->_html .= $this->rendConf();
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=config');
			}
		}
		elseif (Tools::isSubmit('addCat') || Tools::isSubmit('id_category'))
			$this->_html .= $this->rendCat();
		elseif (Tools::isSubmit('addImag') || Tools::isSubmit('id_item'))
			$this->_html .= $this->rendForm();
		else
		{
			$view = Tools::getValue('view', 'categories');
			if ($view == 'categories')
				$this->_html .= $this->renderCatList();
			if ($view == 'items')
				$this->_html .= $this->renderGalleryList();
			if ($view == 'config')
				$this->_html .= $this->rendConf();
		}
		return $this->_html;
	}
	private function _postValidation()
	{
		$errors = array();
		if (Tools::isSubmit('submitCat'))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('name_category_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The title is too long.');
				if (Tools::strlen(Tools::getValue('description_'.$language['id_lang'])) > 4000)
					$errors[] = $this->l('The description is too long.');
			}
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('name_category_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The name category is not set.');
		}
		elseif (Tools::isSubmit('submitItem'))
		{
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('title_item_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The title is too long.');
				if (Tools::strlen(Tools::getValue('description_item_'.$language['id_lang'])) > 4000)
					$errors[] = $this->l('The description is too long.');
			}
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('title_item_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The title is not set.');
			if (Tools::getValue('jms_category') == 0)
				$errors[] = $this->l('Please choose category.');
			if (strtotime(Tools::getValue('DATE_ADD')) == null)
				$errors[] = $this->l('Please fill date add.');
			if (strtotime(Tools::getValue('DATE_UP')) == null)
				$errors[] = $this->l('Please fill date up.');
			if (strtotime(Tools::getValue('DATE_UP')) < strtotime(Tools::getValue('DATE_ADD')))
				$errors[] = $this->l('field date add must not be greater than .');
		}
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));
			$this->_html .= $this->rendForm();
			return false;
		}
		return true;
	}
	private function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitItem'))
		{
			if (Tools::getValue('id_item'))
			{
				$tes = new JmsGalleryItem((int)Tools::getValue('id_item'));
				if (!Validate::isLoadedObject($tes))
				{
					$this->_html .= $this->displayError($this->l('Invalid id_item'));
					return;
				}
			}
			else
				$tes = new JmsGalleryItem();
			$tes->active = (int)Tools::getValue('JMS_ACTIVE');
			$tes->id_category = Tools::getValue('jms_category');
			$tes->date_add = Tools::getValue('DATE_ADD');
			$tes->date_update = Tools::getValue('DATE_UP');
			/* Uploads image and sets gallery */
				$type = Tools::strtolower(Tools::substr(strrchr($_FILES['image']['name'], '.'), 1));
				$imagesize = array();
				$imagesize = @getimagesize($_FILES['image']['tmp_name']);
				if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']) && !empty($imagesize) && in_array(Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) && in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
				{
					$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
					$salt = sha1(microtime());
					if ($error = ImageManager::validateUpload($_FILES['image']))
						$errors[] = $error;
					elseif (!$temp_name || !move_uploaded_file($_FILES['image']['tmp_name'], $temp_name))
						return false;
					elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/views/img/'.Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type, null, null, $type))
						$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
					if (isset($temp_name))
						@unlink($temp_name);
					$tes->image = Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type;
					$this->CreateThumb(dirname(__FILE__).'/views/img/', Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type, Configuration::get('JMS_HEIGHT_IMAGE'), Configuration::get('JMS_WIGHT_IMAGE'), 'resized_', 0);
					//delete old img
					$old_img = Tools::getValue('image_old');
					if	($old_img && file_exists(dirname(__FILE__).'/views/img/'.$old_img))
						@unlink(dirname(__FILE__).'/views/img/'.$old_img);
						@unlink(dirname(__FILE__).'/views/img/resized_'.$old_img);
				}
				elseif (Tools::getValue('image_old') != '')
					$tes->image = Tools::getValue('image_old');
			/* Sets each langue fields */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				$tes->title[$language['id_lang']] = Tools::getValue('title_item_'.$language['id_lang']);
				$tes->description[$language['id_lang']] = strip_tags(Tools::getValue('description_time_'.$language['id_lang']));
			}

			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_item'))
				{
					if (!$tes->add())
					$errors[] = $this->displayError($this->l('The Information could not be added.'));
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
				}
				/* Update */
				elseif (!$tes->update())
				{
					$errors[] = $this->displayError($this->l('The Information could not be updated.'));
					$this->clearCache();
				}
			}
		}
		elseif (Tools::isSubmit('status_id_item'))
		{
			$this->changeStatus( tools::getValue('status_id_item'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=5&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
		}
		elseif (Tools::isSubmit('delete_id_item'))
		{
			$tes = new JmsGalleryItem((int)Tools::getValue('delete_id_item'));
			$tes->delete();
			$this->clearCache();
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
		}
	}

	private function _postCatProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitCat'))
		{
			if (Tools::getValue('id_category'))
			{
				$gcat = new JmsGalleryCat((int)Tools::getValue('id_category'));
				if (!Validate::isLoadedObject($gcat))
				{
					$this->_html .= $this->displayError($this->l('Invalid id category'));
					return;
				}
			}
			else
				$gcat = new JmsGalleryCat();
			$gcat->active = (int)Tools::getValue('active');
			$gcat->jmsparent = Tools::getValue('jmsparent');

			/* Uploads image and sets gallery */
				$type = Tools::strtolower(Tools::substr(strrchr($_FILES['image']['name'], '.'), 1));
				$imagesize = array();
				$imagesize = @getimagesize($_FILES['image']['tmp_name']);
				if (isset($_FILES['image']) && isset($_FILES['image']['tmp_name']) && !empty($_FILES['image']['tmp_name']) && !empty($imagesize) && in_array(Tools::strtolower(Tools::substr(strrchr($imagesize['mime'], '/'), 1)), array('jpg', 'gif', 'jpeg', 'png')) && in_array($type, array('jpg', 'gif', 'jpeg', 'png')))
				{
					$temp_name = tempnam(_PS_TMP_IMG_DIR_, 'PS');
					$salt = sha1(microtime());
					if ($error = ImageManager::validateUpload($_FILES['image']))
						$errors[] = $error;
					elseif (!$temp_name || !move_uploaded_file($_FILES['image']['tmp_name'], $temp_name))
						return false;
					elseif (!ImageManager::resize($temp_name, dirname(__FILE__).'/views/img/'.Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type, null, null, $type))
						$errors[] = $this->displayError($this->l('An error occurred during the image upload process.'));
					if (isset($temp_name))
						@unlink($temp_name);
					$gcat->image = Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type;
					$this->CreateThumb(dirname(__FILE__).'/views/img/', Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type, Configuration::get('JMS_HEIGHT_IMAGE'), Configuration::get('JMS_WIGHT_IMAGE'), 'resized_', 0);
					//delete old img
					$old_img = Tools::getValue('image_old');
					if	($old_img && file_exists(dirname(__FILE__).'/views/img/'.$old_img))
						@unlink(dirname(__FILE__).'/views/img/'.$old_img);
						@unlink(dirname(__FILE__).'/views/img/resized_'.$old_img);
				}
				elseif (Tools::getValue('image_old') != '')
					$gcat->image = Tools::getValue('image_old');
			/* Sets each langue fields */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				$gcat->name_category[$language['id_lang']] = Tools::getValue('name_category_'.$language['id_lang']);
				$gcat->description[$language['id_lang']] = strip_tags(Tools::getValue('description_'.$language['id_lang']));
			}
			/* Processes if no errors  */
			if (!$errors)
			{
				/* Adds */
				if (!Tools::getValue('id_category'))
				{
					if (!$gcat->add())
					$errors[] = $this->displayError($this->l('The Information could not be added.'));
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=3&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=categories');
				}
				/* Update */
				elseif (!$gcat->update())
				{
					$errors[] = $this->displayError($this->l('The Information could not be updated.'));
					$this->clearCache();
				}
			}
		}
		elseif (Tools::isSubmit('status_id_category'))
		{
			$this->changeCatStatus(tools::getValue('status_id_category'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=5&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=categories');
		}
		elseif (Tools::isSubmit('delete_id_cat'))
		{
			$gcat = new JmsGalleryCat((int)Tools::getValue('delete_id_cat'));
			$gcat->delete();
			$this->clearCache();
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=categories');
		}
	}
	private function _confValidation()
	{
		$errors = array();
		if (Tools::isSubmit('submitConf'))
		{
			if (Tools::getValue('NUMBER_ITEMS') > Tools::getValue('TOTAL_ITEMS'))
				$errors[] = $this->l('field number items must not be greater than .');
		}
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));
			$this->_html .= $this->rendConf();
			return false;
		}
		return true;
	}
	public function hookDisplayFooter()
	{
		if (Configuration::get('JMS_POSITION') == 0)
			$jmsgallery = $this->getImage(0);
		elseif (Configuration::get('JMS_POSITION') == 1 && Configuration::get('CONF_CATEGORY'))
			$jmsgallery = $this->getImage(1);
		elseif (Configuration::get('JMS_POSITION') == 2)
			$jmsgallery = $this->getImage(2);

		$this->smarty->assign(array(
		'root_url'=> _PS_BASE_URL_.__PS_BASE_URI__,
		'CONF_ACTIVE' => Configuration::get('CONF_ACTIVE'),
		'NUMBER_ITEMS' => Configuration::get('NUMBER_ITEMS'),
		'autoPlay' => Configuration::get('autoPlay'),
		'navigation' => Configuration::get('navigation'),
		'rewindNav' => Configuration::get('rewindNav'),
		'pagination' => Configuration::get('pagination'),
		'scrollPerPage' => Configuration::get('scrollPerPage'),
		'active' => Configuration::get('IMG_ACTIVE'),
		));
		$this->smarty->assign('jmsgallerys', $jmsgallery);
		$this->context->controller->addCSS(($this->_path).'views/css/style.css', 'all');
		return $this->display(__FILE__, 'jmsgallery.tpl');
	}
	public function hookDisplayRightColumn()
	{
		return $this->hookDisplayFooter();
	}
	public function getImage($filter = -1)
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		if ($filter == 0)
			$filter = 'ORDER BY it.date_add DESC';
		elseif ($filter == 1)
			$filter = 'AND it.`id_category` ="'.Configuration::get('CONF_CATEGORY').'" ';
		elseif ($filter == 2)
			$filter = 'ORDER BY RAND()';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT DISTINCT it.`image`,it.`id_item`,itl.`title`,itl.`description`,it.`active`
		FROM '._DB_PREFIX_.'jmsgallery_item it
		LEFT JOIN '._DB_PREFIX_.'jmsgallery_item_lang itl ON (it.`id_item` = itl.`id_item`)
		WHERE it.`active` = 1 AND itl.`id_lang` = '.$id_lang.' '.$filter.'
		LIMIT '.Configuration::get('TOTAL_ITEMS')
		);
	}
	public function renderCatList()
	{
		$this->getCats(0, 0);
		$items = $this->child;
		foreach ($items as $key => $item)
		{
			$items[$key]['status'] = $this->displayCatStatus($item['id_category'], $item['active']);
			$items[$key]['item_count'] = $this->getItemCount($item['id_category']);
		}
		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'items' => $items,
				'image_baseurl' => $this->_path.'views/img/',
				'active' => Configuration::get('IMG_ACTIVE')
			)
		);
		return $this->display(__FILE__, 'catlist.tpl');
	}
	public function getCats($parent = 0, $lvl = 0)
	{
		$lvl ++;
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		$items = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'jmsgallery_category jmc			
			LEFT JOIN '._DB_PREFIX_.'jmsgallery_category_lang jmcl ON (jmc.`id_category` = jmcl.`id_category`)
			WHERE jmcl.`id_lang` = '.(int)$id_lang.
			' AND jmc.`jmsparent` = '.$parent.' 
			ORDER BY jmc.`id_category` ASC'
		);//print_r(current($items));exit;
		if (count($items))
		{
			while ($element = current($items))
			{
				$items[key($items)]['lvl'] = $lvl;
				$this->child[] = $items[key($items)];
				$this->getCats($element['id_category'], $lvl);
				next($items);
			}
		}
	}
	public function renderGalleryList()
	{
		$gallery = $this->getlistprove();

		foreach ($gallery as $key => $gallerys)
			$gallery[$key]['status'] = $this->displayStatus($gallerys['id_item'], $gallerys['active']);
		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'gallerys' => $gallery,
				'image_url' =>$this->_path.'views/img/',
				'active' => Configuration::get('IMG_ACTIVE')
			)
		);
		$this->context->controller->addCSS(($this->_path).'views/css/style.css', 'all');
		return $this->display(__FILE__, 'jmsgalleryitemlist.tpl');
	}
	public function getlistprove()
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		if (Tools::isSubmit('list_id_category'))
			$filter = ' AND it.id_category = '.Tools::getValue('list_id_category');
		else
			$filter = '';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT DISTINCT it.`id_item`,it.`image`,it.active,it.`id_category`,itl.`title`,itl.`description`,cat.`name_category`
			FROM '._DB_PREFIX_.'jmsgallery_item it			
			LEFT JOIN '._DB_PREFIX_.'jmsgallery_item_lang itl ON (itl.`id_item` = it.`id_item`)
			LEFT JOIN '._DB_PREFIX_.'jmsgallery_category_lang cat ON (cat.`id_category` = it.`id_category`)
			WHERE itl.`id_lang` = '.(int)$id_lang.' '.$filter.'
			ORDER BY it.`id_item`'
		);
	}
	public function getItemCount($id_item)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(hss.`id_item`) 			
			FROM '._DB_PREFIX_.'jmsgallery_item hss						
			WHERE hss.id_category = '.(int)$id_item
		);
	}
	public function displayStatus($id_product_status, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&status_id_item='.(int)$id_product_status.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';
		return $html;
	}
	public function displayCatStatus($id_item, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeCatStatus&status_id_category='.(int)$id_item.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';

		return $html;
	}
	public function changeCatStatus($id_product)
	{
		$active = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `active` FROM '._DB_PREFIX_.'jmsgallery_category
		WHERE `id_category`="'.$id_product.'"
		');
		$change_active = ($active[0]['active'] == 0 ? $active[0]['active'] = 1 : $active[0]['active'] = 0);
		$abc = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
		UPDATE '._DB_PREFIX_.'jmsgallery_category  SET `active` = "'.$change_active.'" WHERE `id_category` = "'.$id_product.'" 
		');
		return $abc;
	}
	public function changeStatus($id_product)
	{
		$active = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `active` FROM '._DB_PREFIX_.'jmsgallery_item
		WHERE `id_item`="'.$id_product.'"
		');
		$change_active = ($active[0]['active'] == 0 ? $active[0]['active'] = 1 : $active[0]['active'] = 0);
		$abc = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
		UPDATE '._DB_PREFIX_.'jmsgallery_item  SET `active` = "'.$change_active.'" WHERE `id_item` = "'.$id_product.'" 
		');
		return $abc;
	}
	public function renderPathway()
	{
		$this->context->smarty->assign(
			array(
				'view' => Tools::getValue('view'),
				'link' => $this->context->link
			)
		);
		return $this->display(__FILE__, 'path.tpl');
	}
	public function rendForm()
	{
		$cat_id	= (int)Tools::getValue('id_category', 0);
		$cats = $this->getParentOptions($cat_id);
		if (!count($cats))
			$cats = array();
		array_unshift($cats, array ( 'id_category' => 0,'name_category' => 'Choose Category' ));
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Add Image'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Title Item'),
						'name' => 'title_item',
						'lang' => true,
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Description'),
						'name' => 'description_time',
						'lang' => true,
						'autoload_rte' => true,
					),
					array(
						'type' => 'select',
						'label' => $this->l('Category'),
						'name' => 'jms_category',
						'desc' => $this->l('Please Select a category'),
						'options' => array('query' => $cats,'id' => 'id_category','name' => 'name_category')
					),
					array(
						'type' => 'date',
						'label' => $this->l('Date Add'),
						'name' => 'DATE_ADD',
						'desc' => 'date when you add ',
					),
					array(
						'type' => 'date',
						'label' => $this->l('Date Update'),
						'name' => 'DATE_UP',
						'desc' => 'date when you update ',
					),
					array(
						'type' => 'file_lang',
						'label' => $this->l('Image'),
						'name' => 'image',
						'desc' => $this->l(sprintf('Max image size %s', ini_get('upload_max_filesize')))
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'JMS_ACTIVE',
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
		if (Tools::isSubmit('id_item'))
		{
			$item = new JmsGalleryItem((int)Tools::getValue('id_item'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_item');
			if	(count($item->image) > 0)
				$fields_form['form']['images'] = $item->image;
			$has_picture = true;
			foreach (Language::getLanguages(false) as $lang)
				if (!isset($item->image[$lang['id_lang']]))
					$has_picture &= false;
			if ($has_picture)
				$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'has_picture');
		}
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitItem';
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
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'views/img/'
		);
		$helper->override_folder = '/';
		return $helper->generateForm(array($fields_form));
	}
	public function getAddFieldsValues()
	{
		$fields = array();
		if (Tools::isSubmit('id_item'))
		{
			$item = new JmsGalleryItem((int)Tools::getValue('id_item'));
			$fields['id_item'] 	= (int)Tools::getValue('id_item', $item->id);
		}
		else
			$item = new JmsGalleryItem();
		$fields['JMS_ACTIVE'] = Tools::getValue('JMS_ACTIVE', $item->active);
		$fields['has_picture'] = true;
		$fields['jms_category'] = Tools::getValue('jms_category', $item->id_category);
		$fields['DATE_ADD'] = Tools::getValue('DATE_ADD', $item->date_add);
		$fields['DATE_UP'] = Tools::getValue('DATE_UP', $item->date_update);
		$fields['image'] = Tools::getValue('image', $item->image);
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang)
		{
			$fields['title_item'][$lang['id_lang']] = Tools::getValue('title_item_'.(int)$lang['id_lang'], $item->title[$lang['id_lang']]);
			$fields['description_time'][$lang['id_lang']] = Tools::getValue('description_time_'.(int)$lang['id_lang'], $item->description[$lang['id_lang']]);
		}
		return $fields;
	}

	public function rendCat()
	{
		$cat_id	= (int)Tools::getValue('id_category', 0);
		$cats 	= $this->getParentOptions($cat_id);
		if (!count($cats))
			$cats = array();
		array_unshift($cats, array ( 'id_category' => 0,'name_category' => 'Root Category' ));
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Setting Categories'),
					'icon' => 'icon-cogs',
				),
				'input' => array(
					array(
						'type' => 'text',
						'label' => $this->l('Name category'),
						'name' => 'name_category',
						'lang' => true,
					),
					array(
						'type' => 'textarea',
						'label' => $this->l('Description'),
						'name' => 'description',
						'autoload_rte' => true,
						'lang' => true,
					),
					array(
						'type' => 'select',
						'lang' => true,
						'label' => $this->l('Parent Category'),
						'name' => 'jmsparent',
						'desc' => $this->l('Please Select parent category'),
						'options' => array('query' => $cats,'id' => 'id_category','name' => 'name_category')
					),
					array(
						'type' => 'file_lang',
						'label' => $this->l('Image'),
						'name' => 'image',
						'desc' => $this->l(sprintf('Max image size %s', ini_get('upload_max_filesize'))),
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
		if (Tools::isSubmit('id_category'))
		{
			$item = new JmsGalleryCat((int)Tools::getValue('id_category'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_category');
			if	(count($item->image) > 0)
				$fields_form['form']['images'] = $item->image;
			$has_picture = true;
			foreach (Language::getLanguages(false) as $lang)
				if (!isset($item->image[$lang['id_lang']]))
					$has_picture &= false;
			if ($has_picture)
				$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'has_picture');
		}
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$this->fields_form = array();
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitCat';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getCatValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'views/img/'
		);

		$helper->override_folder = '/';
		return $helper->generateForm(array($fields_form));
	}
	public function getCatValues()
	{
		$fields = array();
		if (Tools::isSubmit('id_category'))
		{
			$item = new JmsGalleryCat((int)Tools::getValue('id_category'));
			$fields['id_category'] 	= (int)Tools::getValue('id_category', $item->id);
		}
		else
			$item = new JmsGalleryCat();
		$fields['active'] = Tools::getValue('active', $item->active);
		$fields['has_picture'] = true;
		$fields['image'] = Tools::getValue('image', $item->image);
		$fields['jmsparent'] = Tools::getValue('jmsparent', $item->jmsparent);
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang)
		{
			$fields['name_category'][$lang['id_lang']] = Tools::getValue('name_category_'.(int)$lang['id_lang'], $item->name_category[$lang['id_lang']]);
			$fields['description'][$lang['id_lang']] = Tools::getValue('description_'.(int)$lang['id_lang'], $item->description[$lang['id_lang']]);
		}
		return $fields;
	}
	public function getParentOptions($cat_id = 0)
	{
		$this->context = Context::getContext();
		$id_lang = $this->context->language->id;
		$cat_arr = array();
		$cats = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT jc.`id_category` as id_category,jcl.`name_category`			
			FROM '._DB_PREFIX_.'jmsgallery_category jc	
			LEFT JOIN '._DB_PREFIX_.'jmsgallery_category_lang jcl ON (jc.`id_category` = jcl.`id_category`)
			WHERE jcl.`id_lang` = '.(int)$id_lang.'
			ORDER BY jc.`jmsparent`
		');
		$total_cats = count($cats);
		for	($i = 0; $i < $total_cats; $i++)
		{
			$check = $this->is_child($cat_id, $cats[$i]['id_category']);
			if (!$check)
				$cat_arr[] = $cats[$i];
		}
		return $cat_arr;
	}
	public function is_child($parent_id, $test_id)
	{
		$is_child = 0;
		if ($parent_id == $test_id)
			$is_child = 1;
		else
		{
			$cat_list = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT `id_category`,`jmsparent`	
				FROM '._DB_PREFIX_.'jmsgallery_category	'
				);
			$cats = array();
			$total_catlist = count($cat_list);
			for ($i = 0; $i < $total_catlist; $i ++)
				$cats[$cat_list[$i]['id_category']] = $cat_list[$i]['jmsparent'];
			while ($cats[$test_id] != 0)
			{
				if ($cats[$test_id] == $parent_id)
					$is_child = 1;
				$test_id = $cats[$test_id];
			}
		}
		return $is_child;
	}

	public function rendConf()
	{
		$cat_id	= (int)Tools::getValue('id_category', 0);
		$cats = $this->getParentOptions($cat_id);
		if (!count($cats))
			$cats = array();
		array_unshift($cats, array ( 'id_category' => 0,'name_category' => 'Choose Category' ));
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Configure Gallery'),
					'icon' => 'icon-cogs',
				),
				'tabs' => array('general' => 'General Setting','navgation' => 'Navgation Setting','images' => 'Background & Thumbnail'),
				'input' => array(
					array(
						'type' => 'radio',
						'label' => $this->l('Source'),
						'name' => 'JMS_POSITION',
						'hint' => $this->l('Select which source is displayed in the block. The current category is the one the visitor is currently browsing.'),
						'values' => array(
							array(
								'id' => 'home',
								'value' => 0,
								'label' => $this->l('Show Image Latest')
							),
							array(
								'id' => 'current',
								'value' => 1,
								'label' => $this->l('Show Image In Categories')
							),
							array(
								'id' => 'current_parent',
								'value' => 2,
								'label' => $this->l('Random Image')
							),
						),
						'tab' => 'general'
					),
					array(
						'type' => 'select',
						'form_group_class' => 'nofwne',
						'label' => $this->l('Category'),
						'name' => 'CONF_CATEGORY',
						'desc' => $this->l('Please Select a category'),
						'options' => array('query' => $cats,'id' => 'id_category','name' => 'name_category'),
						'tab' => 'general',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Total Items'),
						'name' => 'TOTAL_ITEMS',
						'tab' => 'general',
					),
					array(
						'type' => 'text',
						'label' => $this->l('Number Items'),
						'name' => 'NUMBER_ITEMS',
						'tab' => 'general',
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Active'),
						'name' => 'CONF_ACTIVE',
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
						'tab' => 'general',
					),

					array(
						'type' => 'switch',
						'label' => $this->l('autoPlay'),
						'name' => 'autoPlay',
						'desc' => 'Enable autoplay for slider',
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
							),
						),
						'tab' => 'navgation'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Navigation'),
						'name' => 'navigation',
						'desc' => 'Enable arrow slider',
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
							),
						),
						'tab' => 'navgation'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Pagination'),
						'name' => 'pagination',
						'desc' => 'Enable pagination for Slide',
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
							),
						),
						'tab' => 'navgation'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('rewindNav'),
						'name' => 'rewindNav',
						'desc' => 'Continue play if your product was limit',
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
							),
						),
						'tab' => 'navgation'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('scrollPerPage'),
						'name' => 'scrollPerPage',
						'desc' => 'scroll per page or product',
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
							),
						),
						'tab' => 'navgation'
					),
					array(
						'type' => 'text',
						'label' => $this->l('Thumbnail Height'),
						'name' => 'JMS_HEIGHT_IMAGE',
						'tab' => 'images'
					),
					array(
						'type' => 'text',
						'label' => $this->l('Thumbnail Wight'),
						'name' => 'JMS_WIGHT_IMAGE',
						'tab' => 'images'
					),
					array(
						'type' => 'switch',
						'label' => $this->l('Thumbnail Active'),
						'name' => 'IMG_ACTIVE',
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
						'tab' => 'images'
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
		$helper->module = $this;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitConf';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getConfigValues(),
			'languages' => $this->context->controller->getLanguages(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'views/img/'
		);

		$helper->override_folder = '/';
		return $helper->generateForm(array($fields_form));
	}
	public function getConfigValues()
	{
		return array(
			'JMS_HEIGHT_IMAGE' => Tools::getValue('JMS_HEIGHT_IMAGE', (int)Configuration::get('JMS_HEIGHT_IMAGE')),
			'JMS_WIGHT_IMAGE' => Tools::getValue('JMS_WIGHT_IMAGE', (int)Configuration::get('JMS_WIGHT_IMAGE')),
			'IMG_ACTIVE' => Tools::getValue('IMG_ACTIVE', (int)Configuration::get('IMG_ACTIVE')),

			'TOTAL_ITEMS' => Tools::getValue('TOTAL_ITEMS', (int)Configuration::get('TOTAL_ITEMS')),
			'NUMBER_ITEMS' => Tools::getValue('NUMBER_ITEMS', (int)Configuration::get('NUMBER_ITEMS')),
			'CONF_CATEGORY' => Tools::getValue('CONF_CATEGORY', (int)Configuration::get('CONF_CATEGORY')),
			'CONF_ACTIVE' => Tools::getValue('CONF_ACTIVE', (int)Configuration::get('CONF_ACTIVE')),
			'JMS_POSITION' => Tools::getValue('JMS_POSITION', (int)Configuration::get('JMS_POSITION')),

			'autoPlay' => Tools::getValue('autoPlay', (int)Configuration::get('autoPlay')),
			'navigation' => Tools::getValue('navigation', (int)Configuration::get('navigation')),
			'pagination' => Tools::getValue('pagination', (int)Configuration::get('pagination')),
			'scrollPerPage' => Tools::getValue('scrollPerPage', (int)Configuration::get('scrollPerPage')),
			'rewindNav' => Tools::getValue('rewindNav', (int)Configuration::get('rewindNav')),
			);
	}
	public function CreateThumb($src, $image, $max_height, $max_width, $resize_name, $crop)
	{
		if ($image)
		{
			if ($crop)
			{
				$imgInfo = getimagesize($src.'/'.$image);
				$width = $imgInfo[0];
				$height = $imgInfo[1];
				$ratio = max($max_width / $width, $max_height / $height);
				$y = ($height - $max_height / $ratio) / 2;
				$height = $max_height / $ratio;
				$x = ($width - $max_width / $ratio) / 2;
				$width = $max_width / $ratio;
				$rzname = $resize_name.$image; // get the file extension
				$resized = $src.'/'.$rzname;
				switch ($imgInfo[2])
				{
					case 1:
						$im = imagecreatefromgif($src.'/'.$image);
						break;
					case 2:
						$im = imagecreatefromjpeg($src.'/'.$image);
						break;
					case 3:
						$im = imagecreatefrompng($src.'/'.$image);
						break;
					default:
						return '';
				}
				$newImg = imagecreatetruecolor($max_height, $max_height);

				/* Check if this image is PNG or GIF, then set if Transparent*/
				if	(($imgInfo[2] == 1) || ($imgInfo[2] == 3))
				{
					imagealphablending($newImg, false);
					imagesavealpha($newImg, true);
					$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
					imagefilledrectangle($newImg, 0, 0, $max_width, $max_height, $transparent);
				}
				imagecopyresampled($newImg, $im, 0, 0, $x, $y, $max_width, $max_height, $width, $height);

				//Generate the file, and rename it to $newfilename
				switch ($imgInfo[2])
				{
					case 1:
						imagegif($newImg, $resized);
						break;
					case 2:
						imagejpeg($newImg, $resized, 90);
						break;
					case 3:
						imagepng($newImg, $resized);
						break;
					default:
						return '';
				}
				return $src.'/'.$rzname;
			}
			else
			{
				$imgInfo = getimagesize($src.'/'.$image);
				$width = $imgInfo[0];
				$height = $imgInfo[1];
				if (!$max_width && !$max_height)
				{
					$max_width = $width;
					$max_height = $height;
				}
				else
				{
					if (!$max_width) $max_width = 1000;
					if (!$max_height) $max_height = 1000;
				}
				$x_ratio = $max_width / $width;
				$y_ratio = $max_height / $height;
				if (($width <= $max_width) && ($height <= $max_height))
				{
					$tn_width = $width;
					$tn_height = $height;
				}
				else if (($x_ratio * $height) < $max_height)
				{
					$tn_height = ceil($x_ratio * $height);
					$tn_width = $max_width;
				}
				else
				{
					$tn_width = ceil($y_ratio * $width);
					$tn_height = $max_height;
				}

				$rzname = $resize_name.$image; // get the file extension

				$resized = $src.'/'.$rzname;

				switch ($imgInfo[2])
				{
					case 1:
						$im = imagecreatefromgif($src.'/'.$image);
						break;
					case 2:
						$im = imagecreatefromjpeg($src.'/'.$image);
						break;
					case 3:
						$im = imagecreatefrompng($src.'/'.$image);
						break;
					default:
						return '';
				}

				$newImg = imagecreatetruecolor($tn_width, $tn_height);

				/* Check if this image is PNG or GIF, then set if Transparent*/
				if	(($imgInfo[2] == 1) || ($imgInfo[2] == 3))
				{
					imagealphablending($newImg, false);
					imagesavealpha($newImg, true);
					$transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
					imagefilledrectangle($newImg, 0, 0, $tn_width, $tn_height, $transparent);
				}
				imagecopyresampled($newImg, $im, 0, 0, 0, 0, $tn_width, $tn_height, $imgInfo[0], $imgInfo[1]);

				//Generate the file, and rename it to $newfilename
				switch ($imgInfo[2])
				{
					case 1:
						imagegif($newImg, $resized);
						break;
					case 2:
						imagejpeg($newImg, $resized, 90);
						break;
					case 3:
						imagepng($newImg, $resized);
						break;
					default:
						return '';
				}
				return $src.'/'.$rzname;
			}
		}
	}
	public function HtmlHeader()
	{
		$html = '<script type="text/javascript">
			$(function() {
				$(".nofwne").hide("slow");
				$("#current").click(function(){
						$(".nofwne").show("slow");
				});
				$("#home,#current_parent").click(function(){
						$(".nofwne").hide("slow");
				});
			 });
		</script>';

		return $html;
	}
	public function clearCache()
	{
		$this->_clearCache('jmsgallery.tpl');
	}
}