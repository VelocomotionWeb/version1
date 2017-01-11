<?php
/**
* 2007-2014 PrestaShop
*
* Jms Testimonials
*
*  @author    Joommasters <joommasters@gmail.com>
*  @copyright 2007-2014 Joommasters
*  @license   license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*  @Website: http://www.joommasters.com
*/

if (!defined('_PS_VERSION_'))
	exit;
include_once(_PS_MODULE_DIR_.'jmstestimonials/JmsTestimonialsInfo.php');
class JmsTestimonials extends Module
{
	public function __construct()
	{
		$this->name = 'jmstestimonials';
		$this->tab = 'front_office_features';
		$this->version = '1.1.0';
		$this->author = 'Joommasters';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct(); 

		$this->displayName = $this->l('Jms Testimonials');
		$this->description = $this->l('Displays Testimonials.');
	}

	public function install()
	{
		if (parent::install() && $this->registerHook('header'))
		{
			$res = true;			
			$res &= Configuration::updateValue('JMSTESTIMONIALS_THUMBWIDTH', 400);
			$res &= Configuration::updateValue('JMSTESTIMONIALS_ITEM', 1);
			$res &= Configuration::updateValue('JMSTESTIMONIALS_ALLITEMS', 3);
			$res &= Configuration::updateValue('JMSTESTIMONIALS_HEIGHT', 400);
			$res &= Configuration::updateValue('JMSTESTIMONIALS_IMAGE', 1);
			$res &= Configuration::updateValue('JMSTESTIMONIALS_DATETIME', 0);
			$res &= Configuration::updateValue('JMSTESTIMONIALS_OFFICE', 1);
			$res &= $this->createTables();
			$res &=	$this->installSamples();
			return $res;	
		}		
		return false;	
	}

	public function uninstall()
	{
		$this->_clearCache('*');

		if (parent::uninstall())
		{
			$res = true;			
			$res &= Configuration::deleteByName('JMSTESTIMONIALS_THUMBWIDTH');
			$res &= Configuration::deleteByName('JMSTESTIMONIALS_ITEM');
			$res &= Configuration::deleteByName('JMSTESTIMONIALS_ALLITEMS');	
			$res &= Configuration::deleteByName('JMSTESTIMONIALS_HEIGHT');	
			$res &= Configuration::deleteByName('JMSTESTIMONIALS_IMAGE');	
			$res &= Configuration::deleteByName('JMSTESTIMONIALS_DATETIME');	
			$res &= Configuration::deleteByName('JMSTESTIMONIALS_OFFICE');	
			$res = $this->deleteTables();
			return $res;
		}
		return false;
	}
	
	protected function createTables()
	{
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmstestimonials` (
				`id_testimonial` int(10) unsigned NOT NULL AUTO_INCREMENT,			
				`author` varchar(255) NOT NULL,
			  	`posttime` datetime NOT NULL,
			  	`image` varchar(255) NOT NULL,
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',	
				PRIMARY KEY (`id_testimonial`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');		
		$res = (bool)Db::getInstance()->execute('
			CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'jmstestimonials_lang` (
				`id_testimonial` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`id_lang` int(10) unsigned NOT NULL,
				`office` varchar(255) NOT NULL,
				`comment` text NOT NULL,
				PRIMARY KEY (`id_testimonial`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=UTF8;
		');		
		return $res;
	}
	
	protected function deleteTables()
	{
		$res1 = (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmstestimonials`');
		$res1 = (bool)Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'jmstestimonials_lang`');
		return $res1;
	}
	
	private function installSamples() 
	{
		$languages = Language::getLanguages(false);
		$res = true;		
		//add testimonials 
		for	($i = 1; $i < 5; $i++) 
		{
			$item = new JmsTestimonialsInfo();	
			$item->active = 1;
			$item->author = 'John Doe';
			$item->posttime = '2012-12-12';
			/* Sets each language fields */						
			foreach ($languages as $language)
			{				
				$item->office[$language['id_lang']] = 'CEO Modelfashion';
				$item->comment[$language['id_lang']] = 'Curabitur at facilisis ante. Morbi ultrices sit amet ex ac commodo., donec quis augue sed magna mollis luctus, sed lobortis, tortor nec mollis mattis, nisl neque elementum dui, pulvinar pharetra velit augue nec ex.';			
				$item->image = 'img'.$i.'.jpg';
			}
			$res	&= $item->add();				
		}
		
		return $res;	
	}
	
	public function getContent()
	{
		$this->_html .= $this->renderPathway();	
		$this->_html .= $this->headerHTML();
		if (Tools::isSubmit('submitConfig') || Tools::isSubmit('submitInfo') || Tools::isSubmit('delete_id_tes') || Tools::isSubmit('status_id_testimonial'))
		{ 
			if ($this->_postValidation())
			{				
				$this->_postProcess();	
				$this->_html .= $this->renderTestimonialsList();	
			}
			
			$this->clearCache();
		}
		elseif (Tools::isSubmit('addTes') || Tools::isSubmit('id_testimonial'))
			$this->_html .= $this->renderForm();
		else 
		{
			$view = Tools::getValue('view', 'items');
			if ($view == 'items')
				$this->_html .= $this->renderTestimonialsList();
			if ($view == 'config')
				$this->_html .= $this->renderConf();
		}
		return $this->_html;
	}
	
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Item informations'),
					'icon' => 'icon-cogs'
				),
				'input' => array(									
					array(
						'type' => 'text',
						'label' => $this->l('Author'),
						'name' => 'author',
					),	
					array(
						'type' => 'text',
						'label' => $this->l('Office'),
						'name' => 'office',
						'lang' => true,
					),	
					array(
						'type' => 'textarea',
						'label' => $this->l('comment'),
						'name' => 'comment',
						'autoload_rte' => true,
						'lang' => true,
					),
					array(
						'type' => 'datetime',
						'label' => $this->l('Date Time'),
						'name' => 'posttime',
					),			
					array(
						'type' => 'file_lang',
						'label' => $this->l('Image'),
						'name' => 'image',
						'lang' => false,
						'desc' => $this->l(sprintf('Max image size %s', ini_get('upload_max_filesize')))
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
		if (Tools::isSubmit('id_testimonial') && $this->TestimonialsExists((int)Tools::getValue('id_testimonial')))
		{
			$Testimonials = new JmsTestimonialsInfo((int)Tools::getValue('id_testimonial'));
			$fields_form['form']['input'][] = array('type' => 'hidden', 'name' => 'id_testimonial');
			if	(count($Testimonials->image) > 0)
				$fields_form['form']['images'] = $Testimonials->image;
			$has_picture = true;
			foreach (Language::getLanguages(false) as $lang)
				if (!isset($Testimonials->image))
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
		$helper->submit_action = 'submitInfo';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->tpl_vars = array(
			'base_url' => $this->context->shop->getBaseURL(),
			'language' => array(
				'id_lang' => $language->id,
				'iso_code' => $language->iso_code
			),
			'fields_value' => $this->getFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'image_baseurl' => $this->_path.'views/img/'
		);
		$helper->override_folder = '/';		
		return $helper->generateForm(array($fields_form));
	}
	
	public function renderConf()
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
						'label' => $this->l('Show Image'),
						'name' => 'JMSTESTIMONIALS_IMAGE',
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
						'label' => $this->l('Thumb Width'),
						'name' => 'JMSTESTIMONIALS_THUMBWIDTH'
					),
					array(
						'type' => 'text',
						'label' => $this->l('Thumb Height'),
						'name' => 'JMSTESTIMONIALS_HEIGHT'
					),	
					array(
						'type' => 'text',
						'label' => $this->l('All Items'),
						'name' => 'JMSTESTIMONIALS_ALLITEMS'
					),	
					array(
						'type' => 'text',
						'label' => $this->l('Item Show'),
						'name' => 'JMSTESTIMONIALS_ITEM'
					),	
					array(
						'type' => 'switch',
						'label' => $this->l('Show DateTime'),
						'name' => 'JMSTESTIMONIALS_DATETIME',
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
						'type' => 'switch',
						'label' => $this->l('Show Office'),
						'name' => 'JMSTESTIMONIALS_OFFICE',
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
		$helper->submit_action = 'submitConfig';
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
		'JMSTESTIMONIALS_THUMBWIDTH' => Tools::getValue('JMSTESTIMONIALS_THUMBWIDTH', Configuration::get('JMSTESTIMONIALS_THUMBWIDTH')),
		'JMSTESTIMONIALS_HEIGHT' => Tools::getValue('JMSTESTIMONIALS_HEIGHT', Configuration::get('JMSTESTIMONIALS_HEIGHT')),
		'JMSTESTIMONIALS_IMAGE' => Tools::getValue('JMSTESTIMONIALS_IMAGE', Configuration::get('JMSTESTIMONIALS_IMAGE')),
		'JMSTESTIMONIALS_DATETIME' => Tools::getValue('JMSTESTIMONIALS_DATETIME', Configuration::get('JMSTESTIMONIALS_DATETIME')),
		'JMSTESTIMONIALS_OFFICE' => Tools::getValue('JMSTESTIMONIALS_OFFICE', Configuration::get('JMSTESTIMONIALS_OFFICE')),
		'JMSTESTIMONIALS_ALLITEMS' => Tools::getValue('JMSTESTIMONIALS_ALLITEMS', Configuration::get('JMSTESTIMONIALS_ALLITEMS')),
		'JMSTESTIMONIALS_ITEM' => Tools::getValue('JMSTESTIMONIALS_ITEM', Configuration::get('JMSTESTIMONIALS_ITEM')),
		);
	}
	
	public function renderTestimonialsList()	
	{
		$Testimonials = $this->getlistprove();
		foreach ($Testimonials as $key => $Testimonialss) 
			$Testimonials[$key]['status'] = $this->displayStatus($Testimonialss['id_testimonial'], $Testimonialss['active']);
		$this->context->smarty->assign(
			array(
				'link' => $this->context->link,
				'Testimonials' => $Testimonials,
				'image_url' =>$this->_path.'views/img/'
			)
		);
		return $this->display(__FILE__, 'jmstestimonialslist.tpl');
	}
	
	public function getFieldsValues()
	{
		$fields = array();
		if (Tools::isSubmit('id_testimonial'))
		{
			$Testimonials = new JmsTestimonialsInfo((int)Tools::getValue('id_testimonial'));				
			$fields['id_testimonial'] 	= (int)Tools::getValue('id_testimonial', $Testimonials->id);
		}
		else
			$Testimonials = new JmsTestimonialsInfo();
		$fields['active'] = Tools::getValue('active', $Testimonials->active);
		$fields['posttime'] = Tools::getValue('posttime', $Testimonials->posttime);
		$fields['author'] = Tools::getValue('author', $Testimonials->author);
		$fields['image'] = Tools::getValue('image', $Testimonials->image);
		$fields['has_picture'] = true;
		$languages = Language::getLanguages(false);	
		foreach ($languages as $lang)
		{
			$fields['office'][$lang['id_lang']] = Tools::getValue('office_'.(int)$lang['id_lang'], $Testimonials->office[$lang['id_lang']]);
			$fields['comment'][$lang['id_lang']] = Tools::getValue('comment_'.(int)$lang['id_lang'], $Testimonials->comment[$lang['id_lang']]);
		}
		return $fields;
	}
	
	private function _postValidation()
	{
		$errors = array();
		if (Tools::isSubmit('status_id_testimonial'))
		{
			if (!Validate::isInt(Tools::getValue('id_testimonial')))
				$errors[] = $this->l('Invalid Item');
		}
		elseif (Tools::isSubmit('submitInfo'))
		{
			/* If edit : checks id_item */
			if (Tools::isSubmit('id_testimonial'))
			{
				if (!Validate::isInt(Tools::getValue('id_testimonial')) && !$this->TestimonialsExists(Tools::getValue('id_testimonial')))
					$errors[] = $this->l('Invalid id_item');
			}
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('author')) > 255)
					$errors[] = $this->l('The author is too long.');
				if (Tools::strlen(Tools::getValue('office_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The office is too long.');
				if (Tools::strlen(Tools::getValue('comment'.$language['id_lang'])) > 6000)
					$errors[] = $this->l('The comment is too long.');
				if (Tools::getValue('image_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename');
				if (Tools::getValue('image_old_'.$language['id_lang']) != null && !Validate::isFileName(Tools::getValue('image_old_'.$language['id_lang'])))
					$errors[] = $this->l('Invalid filename');
			}
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('author')) == 0)
				$errors[] = $this->l('The author is not set.');
				$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('office_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The office is not set.');
			if (Tools::getValue('posttime') == null)
				$errors[] = $this->l('The DateTime is not set.');
		}
		elseif (Tools::isSubmit('delete_id_tes') && (!Validate::isInt(Tools::getValue('delete_id_tes')) || !$this->TestimonialsExists((int)Tools::getValue('delete_id_tes'))))
			$errors[] = $this->l('Invalid id_item');
		
		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));
			$this->_html .= $this->renderForm();	
			return false;
		}
		return true;
	}
	
	private function _postProcess()
	{
		$errors = array();
		if (Tools::isSubmit('submitConfig'))
		{
			$res = Configuration::updateValue('JMSTESTIMONIALS_IMAGE', (int)(Tools::getValue('JMSTESTIMONIALS_IMAGE')));
			$res &= Configuration::updateValue('JMSTESTIMONIALS_DATETIME', (int)(Tools::getValue('JMSTESTIMONIALS_DATETIME')));
			$res &= Configuration::updateValue('JMSTESTIMONIALS_HEIGHT', (int)(Tools::getValue('JMSTESTIMONIALS_HEIGHT')));
			$res &= Configuration::updateValue('JMSTESTIMONIALS_OFFICE', (int)(Tools::getValue('JMSTESTIMONIALS_OFFICE')));
			$res &= Configuration::updateValue('JMSTESTIMONIALS_ITEM', (int)(Tools::getValue('JMSTESTIMONIALS_ITEM')));
			$res &= Configuration::updateValue('JMSTESTIMONIALS_ALLITEMS', (int)(Tools::getValue('JMSTESTIMONIALS_ALLITEMS')));		
			$res &= Configuration::updateValue('JMSTESTIMONIALS_THUMBWIDTH', (int)(Tools::getValue('JMSTESTIMONIALS_THUMBWIDTH')));
			$res &= Configuration::updateValue('JMSWEEKMENU_THUMBHEIGHT', (int)(Tools::getValue('JMSWEEKMENU_THUMBHEIGHT')));
			
			$this->clearCache();
			if (!$res)
				$errors[] = $this->displayError($this->l('The configuration could not be updated.'));
			else
				Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=6&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=config');
		}
		elseif (Tools::isSubmit('submitInfo'))
		{
			if (Tools::getValue('id_testimonial'))
			{
				$tes = new JmsTestimonialsInfo((int)Tools::getValue('id_testimonial'));
				if (!Validate::isLoadedObject($tes))
				{
					$this->_html .= $this->displayError($this->l('Invalid id_testimonial'));
					return;
				}
			}
			else
				$tes = new JmsTestimonialsInfo();
			/* Sets active */
			$tes->active = (int)Tools::getValue('active');
			$tes->posttime = Tools::getValue('posttime');
			$tes->author = Tools::getValue('author');
			/* Uploads image and sets testimonials */
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
					$this->CreateThumb(dirname(__FILE__).'/views/img', Tools::encrypt($_FILES['image']['name'].$salt).'.'.$type, Configuration::get('JMS_IMAGEGALLERY_LIGHTBOX_HEIGHT'), Configuration::get('JMS_IMAGEGALLERY_LIGHTBOX_WIDTH'), 'resized_', 0);

					
					//delete old img
					$old_img = Tools::getValue('image_old');
					if ($old_img && file_exists(dirname(__FILE__).'/views/img/'.$old_img))
                        @unlink(dirname(__FILE__).'/views/img/'.$old_img);
                        @unlink(dirname(__FILE__).'/views/img/resized_'.$old_img);	
				}
				elseif (Tools::getValue('image_old') != '')
					$tes->image = Tools::getValue('image_old');
			/* Sets each langue fields */
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				$tes->office[$language['id_lang']] = Tools::getValue('office_'.$language['id_lang']);
				$tes->comment[$language['id_lang']] = strip_tags(Tools::getValue('comment_'.$language['id_lang']));
				
			}
			
			/* Processes if no errors  */
			if (!$errors)
			{				
				/* Adds */
				if (!Tools::getValue('id_testimonial'))
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
				elseif (Tools::isSubmit('submitInfo') && Tools::getValue('id_testimonial'))
					Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=4&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
			}
		}
		elseif (Tools::isSubmit('status_id_testimonial'))
		{
			$this->changeStatus( tools::getValue('status_id_testimonial'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=5&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
		}
		elseif (Tools::isSubmit('delete_id_tes'))
		{
			$tes = new JmsTestimonialsInfo((int)Tools::getValue('delete_id_tes'));
			$res = $tes->delete();
			$this->clearCache();
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true).'&conf=1&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&view=items');
		}
		$this->clearCache();
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
	
	public function getlist($active)
	{
		$this->context = Context::getContext();		
		$id_lang = $this->context->language->id;		
		$filter = '';
		if	($active != -1)		
			$filter = ' AND pb.`active` = '.$active;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'jmstestimonials	pb
			LEFT JOIN '._DB_PREFIX_.'jmstestimonials_lang pbl ON (pb.`id_testimonial` = pbl.`id_testimonial`)
			WHERE pbl.`id_lang` = '.(int)$id_lang.
			$filter.
			' ORDER BY pbl.`id_testimonial`'.
			' LIMIT 0,'.Configuration::get('JMSTESTIMONIALS_ALLITEMS')
			);
	}
	
	public function getlistprove($state = -1)
	{
		$this->context = Context::getContext();		
		$id_lang = $this->context->language->id;
		$filter = '';
		if	($state != -1)		
			$filter = ' AND pb.`active` = '.$state;
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT *
			FROM '._DB_PREFIX_.'jmstestimonials	pb
			LEFT JOIN '._DB_PREFIX_.'jmstestimonials_lang pbl ON (pb.`id_testimonial` = pbl.`id_testimonial`)
			WHERE pbl.`id_lang` = '.(int)$id_lang.
			$filter.
			' ORDER BY pbl.`id_testimonial`'
			);
	}
	
	public function TestimonialsExists($id_testimonial)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT `id_testimonial` FROM '._DB_PREFIX_.'jmstestimonials WHERE `id_testimonial` = "'.$id_testimonial.'"
		');
	}
	
	public function clearCache()
	{
		$this->_clearCache('jmstestimonials.tpl');
	}
	
	public function displayStatus($id_product_status, $active)
	{
		$title = ((int)$active == 0 ? $this->l('Disabled') : $this->l('Enabled'));
		$icon = ((int)$active == 0 ? 'icon-remove' : 'icon-check');
		$class = ((int)$active == 0 ? 'btn-danger' : 'btn-success');
		$html = '<a class="btn '.$class.'" href="'.AdminController::$currentIndex.
			'&configure='.$this->name.'
				&token='.Tools::getAdminTokenLite('AdminModules').'
				&changeStatus&status_id_testimonial='.(int)$id_product_status.'" title="'.$title.'"><i class="'.$icon.'"></i> '.$title.'</a>';
		return $html;
	}
	
	public function changeStatus($id_product)
	{
		$active = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT `active` FROM '._DB_PREFIX_.'jmstestimonials
		WHERE `id_testimonial` = "'.$id_product.'"
		');
		$change_active = ($active[0]['active'] == 0 ? $active[0]['active'] = 1 : $active[0]['active'] = 0);
		$abc = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute('
		UPDATE '._DB_PREFIX_.'jmstestimonials  SET `active` = "'.$change_active.'" WHERE `id_testimonial` = "'.$id_product.'" 
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
	
	public function headerHTML()
	{
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
						$.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?", order);
						}
					});
				$mySlides.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			});
			$(function() {
				var $myLayers = $("#layers");
				$myLayers.sortable({
					opacity: 0.6,
					cursor: "move",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateLayersOrdering";						
						$.post("'.$this->context->shop->physical_uri.$this->context->shop->virtual_uri.'modules/'.$this->name.'/ajax_'.$this->name.'.php?", order);		
						var temp = $(this).sortable("serialize").substring(9,200);	
						var layerids = temp.split("&layers[]=");
						var i = 1;
						jQuery.each(layerids, function(index, value) {
							$("#caption_" + value).css("z-index",i);
							i++;
						});
					}
				});
				$myLayers.hover(function() {
					$(this).css("cursor","move");
					},
					function() {
					$(this).css("cursor","auto");
				});
			});
		</script>';
		return $html;
	}
	public function hookHome() 
	{
		$Testimonials = $this->getlist(1);
		$this->context->controller->addCSS($this->_path.'views/css/style.css', 'all');	
		$this->context->smarty->assign(
			array(	
				'show_image' => Configuration::get('JMSTESTIMONIALS_IMAGE'),
				'show_date' => Configuration::get('JMSTESTIMONIALS_DATETIME'),
				'show_office' => Configuration::get('JMSTESTIMONIALS_OFFICE'),
				'jmsitems' => Configuration::get('JMSTESTIMONIALS_ITEM'),
				'link' => $this->context->link,
				'Testimonials' => $Testimonials,
				'image_url' => $this->_path.'views/img/'
				)
			);
		return $this->display(__FILE__, 'jmstestimonials.tpl');
	}	
}
?>
