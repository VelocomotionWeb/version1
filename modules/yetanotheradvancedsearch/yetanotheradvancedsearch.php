<?php
/**
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*/

if (!defined('_PS_VERSION_') || !defined('_CAN_LOAD_FILES_'))
	exit;

/**
 * Criteria type enumeration
 */
class CriteriaTypeEnum {
	const FEATURE = 1;
	const ATTRIBUTE = 2;
	const PRICE = 3;
	const CATEGORY = 4;
	const AVAILABILITY = 5;
	const MANUFACTURER = 6;
	const CONDITION = 7;
	const WEIGHT = 8;
	const SUPPLIER = 9;
}

/**
 * Criteria subtype enumeration
 */
class CriteriaSubTypeEnum {
	const PRICE_MIN = 1;
	const PRICE_MAX = 2;
	const AVAILABILITY_AVAILABLE = 3;
	const WEIGHT_MIN = 4;
	const WEIGHT_MAX = 5;
}

/**
 * Criteria config enumeration
 */
class CriteriaConfigEnum {
	const URL_UPDATE = 1;
	const UPDATE_TIMESTAMP = 2;
	const LAST_UPDATE = 3;
	const COLOR = 4;
	const ACTIVE_COLOR = 5;
	const DISPLAY_COUNT = 6;
	const SCROLL_TOP = 7;
	const DISPLAY_REINIT = 8;
	const IGNORE_CUSTOM = 9;
	const USE_VAT = 10;
	const USE_CURRENT_CATEGORY_AS_FILTER = 11;
	const REFRESH_CACHE_WITH_REINDEX = 12;
	const CURRENTLY_REINDEXING = 13;
}

/**
 * Bit to bit criteria Layout Enumeration
 * 1,2,4,8,16..
 */
class CriteriaLayoutEnum {
	const L_LINK = 1;
	const L_COMBO = 2;
	const L_SLIDE = 4;
}

/**
 * Bit to bit criteria Layout Enumeration
 * 1,2,4,8,16..
 */
class CriteriaSortTypeEnum {
	const NO_SORT = 1;
	const ASC = 2;
	const DESC = 3;
}

/**
 * Cache KEY.
 */
class CriteriaCacheKeyEnum {
	const MENU_CONTENT = 1;
}


include_once(dirname(__FILE__).'/yetanotheradvancedsearchManager.php');
include_once(dirname(__FILE__).'/yetanotheradvancedsearchModel.php');
include_once(dirname(__FILE__).'/yetanotheradvancedsearchConfig.php');
include_once(dirname(__FILE__).'/controllers/front/search.php');


/**
 * Yet Another Advanced Search - Main Class.
 */
class YetAnotherAdvancedSearch extends Module {

	public static $condition_types = array('new', 'used', 'refurbished');
	private $translation_cache = array();
	private $config = null;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		if (version_compare(_PS_VERSION_, '1.6.0', '>=') === true)
			$this->bootstrap = true;
		$this->name = 'yetanotheradvancedsearch';
		$this->tab = 'search_filter';
		$this->version = '1.0.0';
		$this->author = 'Leny GRISEL';
		$this->module_key = '649b53bae2954796624aac91414137b8';
		$this->need_instance = 0;
		$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.7');
		parent::__construct();
		$this->displayName = $this->l('YAAS - Yet Another Advanced Search');
		$this->description = $this->l('Another clean, easy to configure, and ajax-oriented advanced search engine.');

	}

	/**
	 * Internal and specific translation management
	 * @param type $key
	 * @param type $lang
	 * @return type
	 */
	public function translate($key, $lang = null)
	{
		if ($lang == null) $iso_code = $this->context->language->iso_code;
		else $iso_code = $lang['iso_code'];

		if (!array_key_exists($iso_code, $this->translation_cache))
		{
			$file = _PS_MODULE_DIR_.$this->name.'/translations/'.$iso_code.'.php';
			if (file_exists($file))
			{

				// PS doesn't allow to specify translation ! (it doesn't work !)
				// we do I/O, but no other solution right now..
				$data = Tools::file_get_contents($file);
				$data = explode("\n", $data);
				$lang_array = array();
				$pattern = '$_MODULE[\'<{yetanotheradvancedsearch}prestashop>yetanotheradvancedsearch_';
				$nb_lines = count($data);
				for ($line = 0; $line < $nb_lines; $line++)
				{
					$dataline = trim($data[$line]);
					if (strpos($dataline, $pattern) !== false)
					{
						$subkey = trim(Tools::substr($dataline, Tools::strlen($pattern), strpos($dataline, ']') - Tools::strlen($pattern) - 1));
						$p = strpos($dataline, '=') + 1;
						$sub = trim(Tools::substr($dataline, $p, Tools::strlen($dataline) - 1 - $p));
						$sub = Tools::substr($sub, 1, Tools::strlen($sub) - 2);
						$lang_array[$subkey] = str_replace('\\\'', '\'', $sub);
					}
				}
				$this->translation_cache[$iso_code] = $lang_array;
			}
		}

		// try to use the cache
		if (array_key_exists($iso_code, $this->translation_cache))
		{
			$lang_array = $this->translation_cache[$iso_code];
			$md5 = md5($key);
			if (array_key_exists($md5, $lang_array))
				return $lang_array[$md5];
		}

		// by default
		return $this->l($key);
	}

	/**
	 * Translate the "type" of criteria
	 * @param type $type_id
	 * @param type $lang
	 * @return type
	 */
	public function translateType($type_id, $lang = null)
	{
		switch ($type_id)
		{
		case CriteriaTypeEnum::ATTRIBUTE:
			return $this->translate('Attribute', $lang);
		case CriteriaTypeEnum::FEATURE:
			return $this->translate('Feature', $lang);

			// all other are considered as 'Product'
		default:
			return $this->translate('Product', $lang);
		}
	}

	/**
	 * Install Method
	 * @return boolean
	 */
	public function install()
	{
		if (!parent::install() || !$this->registerHook('displayHeader') || !$this->registerHook('displayLeftColumn'))
			return false;
		if (!YetAnotherAdvancedSearchModel::createTables($this))
			return false;
		return true;
	}

	/**
	 * Uninstall Method
	 * @return boolean
	 */
	public function uninstall()
	{
		if (!parent::uninstall())
			return false;
		if (!YetAnotherAdvancedSearchModel::DropTables())
			return false;
		return true;
	}

	/**
	 * Hook for the header
	 */
	public function hookDisplayHeader()
	{
		$this->context->controller->addCSS(($this->_path).'views/css/yetanotheradvancedsearch.css', 'all');
		$this->context->controller->addCSS(($this->_path).'views/css/jquery.slider.min.css', 'all');

		$this->context->controller->addJS(($this->_path).'views/js/jquery-ui-1.10.3.custom.min.js', 'all');
		$this->context->controller->addJS(($this->_path).'views/js/yetanotheradvancedsearch.js', 'all');
		$this->context->controller->addJS(($this->_path).'views/js/jquery.slider.min.js', 'all');
		$this->context->controller->addJS(($this->_path).'views/js/jquery.cookie.js', 'all');

		// make sure this is loaded
		if (Configuration::get('PS_COMPARATOR_MAX_ITEM'))
			$this->context->controller->addJS(_THEME_JS_DIR_.'products-comparison.js');
	}

	/**
	 * Hook for the left column
	 * @param type $params
	 * @return type
	 */
	public function hookDisplayLeftColumn()
	{
		// TODO: why ajax request call this ?!
		if (Tools::getValue('c') != null)
			return;

		$id_lang = Context::getContext()->language->id;
		$shop_id = Context::getContext()->shop->id;

		// add a suffix, if we are on a category
		$category_id = null;
		$cat_param = Tools::getValue('id_category');
		if (isset($cat_param) && YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_CURRENT_CATEGORY_AS_FILTER) == 'true')
			$category_id = $cat_param;

		// cache by groups
		$groups = Customer::getGroupsStatic((int)$this->context->customer->id);
		sort($groups);
		$groups = implode(',', $groups);

		$custom_key = 's'.$shop_id.'-c'.$category_id.'-g'.$groups;
		$hook_left_content = YetAnotherAdvancedSearchModel::getCache(CriteriaCacheKeyEnum::MENU_CONTENT, $id_lang, $custom_key);
		if ($hook_left_content == null)
		{
						$hook_left_content = $this->generateHookLeftContent($groups, $category_id, null, $shop_id);
			YetAnotherAdvancedSearchModel::setCache(CriteriaCacheKeyEnum::MENU_CONTENT, $id_lang, $hook_left_content, $custom_key);
		}
		return $hook_left_content;
	}

	/**
	 * Hook of the right column
	 * @param type $params
	 * @return type
	 */
	public function hookDisplayRightColumn($params)
	{
		return $this->hookDisplayLeftColumn($params);
	}

	/**
	 *
	 * @param type $iso_code
	 */
	private function changeSmartyLanguage($iso_code)
	{
		$this->context->language = new Language((int)Language::getIdByIso($iso_code));
		$this->context->smarty->assign(array(
			'iso' => $this->context->language->iso_code,
			'iso_user' => $this->context->language->iso_code,
			'lang_iso' => $this->context->language->iso_code,
			'full_language_code' => $this->context->language->language_code,
		));
	}

	/**
	 * Generate key from field.
	 *
	 * @param type $field
	 * @param type $internal_id
	 * @return type
	 */
	private function generateKeyFromField($field, $type_id = null, $internal_id = null)
	{
		if ($internal_id === null)
			$internal_id = $field['id_internal'];
		if ($type_id === null)
			$type_id = $field['id_criteria_type'];
		return $type_id.'-'.$field['id_criteria_field'].'v'.$internal_id;
	}

	/**
	 * Generate Hook Left Content
	 * @return type
	 */
	public function generateHookLeftContent($groups, $category_id = null, $iso_code = null, $shop_id = null)
	{
		$kept_language = $this->context->language->iso_code;
		$kept_shop = $this->context->shop;
		if ($iso_code !== null)
			$this->changeSmartyLanguage($iso_code);
		if ($shop_id !== null)
			$this->context->shop = new Shop($shop_id);

		$id_lang = Context::getContext()->language->id;

		$criteria = YetAnotherAdvancedSearchModel::getAllCriteria($this);
		$content_by_criterion = array();

		// if there is a category specified, apply a filter
		$menu = null;
		$menu_as_map = array();
		$default_filter = '';
		$min_max_values = null;
		if ($category_id !== null)
		{
			$field = YetAnotherAdvancedSearchModel::getFieldForCategory($category_id);
			if ($field)
			{
				$default_filter = $this->generateKeyFromField($field, null, $category_id);
				$all_simplified_products = YetAnotherAdvancedSearchManager::doSimplifiedRequest($default_filter);
				$menu = YetAnotherAdvancedSearchManager::updateMenu($all_simplified_products);
				$min_max_values = YetAnotherAdvancedSearchManager::getPriceAndWeightMinMax($all_simplified_products);
				foreach ($menu as $menu_entry)
					$menu_as_map[$menu_entry['name']] = $menu_entry['count'];
			}
		}

		// SPECIAL CASE FOR CATEGORIES GROUP,
		// OK WITH CACHE, Wich Takes GROUP ID
		$enriched_criteria = array();
		foreach ($criteria as $key => $criterion)
		{
				$enriched_criteria[$key] = $criterion;

				// enrich criterion
				$criteria_langs = YetAnotherAdvancedSearchModel::getCriteriaLangByIdAndLang($criterion['id_criteria'], $id_lang);
				$title = $criteria_langs['title'];
				if ($title !== null)
					$enriched_criteria[$key]['title_override'] = $title;

				if ($criterion['id_criteria_type'] == CriteriaTypeEnum::CATEGORY)
					$field_values_from_db = YetAnotherAdvancedSearchModel::getFieldValuesForCategory($criterion['id_criteria_field'], $groups);
				else
					$field_values_from_db = YetAnotherAdvancedSearchModel::getFieldValues($criterion['id_criteria_field']);

				$field_values = array();

				// SPECIAL CASE : min max values
				// with filter by active category
				if ($min_max_values !== null && ($criterion['id_criteria_type'] == CriteriaTypeEnum::PRICE
									|| $criterion['id_criteria_type'] == CriteriaTypeEnum::WEIGHT))
				{

						$field_values = $field_values_from_db;

						if ($criterion['id_criteria_type'] == CriteriaTypeEnum::PRICE)
						{
							$field_values[0]['name'] = $min_max_values['min_price'];
							$field_values[1]['name'] = $min_max_values['max_price'];

						}
						else
						{
							$field_values[0]['name'] = $min_max_values['min_weight'];
							$field_values[1]['name'] = $min_max_values['max_weight'];
						}

				}
				else
					foreach ($field_values_from_db as $field_value)
					{
						if ($menu_as_map != null)
						{
							$key = $this->generateKeyFromField($field_value, $criterion['id_criteria_type'], null);
							if (array_key_exists($key, $menu_as_map))
							{
								$count = $menu_as_map[$key];
								if ($count > 0)
								{
									$field_value['count'] = $count;
									$field_values[] = $field_value;
								}
							}
						}
						else
							$field_values[] = $field_value;
					}

				if ($criterion['sort_type'] == CriteriaSortTypeEnum::ASC)
						asort($field_values);
				else if ($criterion['sort_type'] == CriteriaSortTypeEnum::DESC)
						arsort($field_values);

				$this->context->smarty->assign(array(
						'criterion' => $criterion,
						'field_values' => $field_values,
						'ignoreCustom' => YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::IGNORE_CUSTOM),
						'yaas' => $this
				));

				// depending on the layout
				if ($criterion['layout'] == CriteriaLayoutEnum::L_SLIDE)
				{
						if ($criterion['id_criteria_type'] == CriteriaTypeEnum::PRICE)
								$this->context->smarty->assign(array('symbol' => $this->context->currency->getSign()));
						else
								$this->context->smarty->assign(array('symbol' => Configuration::get('PS_WEIGHT_UNIT')));
						$content_by_criterion[$criterion['id_criteria']] = $this->display(__FILE__, 'fieldslide.tpl');
				}
				else if ($criterion['layout'] == CriteriaLayoutEnum::L_COMBO)
						$content_by_criterion[$criterion['id_criteria']] = $this->display(__FILE__, 'fieldcombo.tpl');
				else
						$content_by_criterion[$criterion['id_criteria']] = $this->display(__FILE__, 'fieldlink.tpl');
		}

		$this->context->smarty->assign(
				array(
						'criteria' => $enriched_criteria,
						'contentByCriterion' => $content_by_criterion,
						'search_link' => $this->context->link->getModuleLink('yetanotheradvancedsearch', 'search'),
						'waiter_img' => $this->_path.'views/img/476658.gif',
						'color' => YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::COLOR),
						'active_color' => YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::ACTIVE_COLOR),
						'display_count' => YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::DISPLAY_COUNT),
						'display_reinit' => YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::DISPLAY_REINIT),
						'scroll_top' => YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::SCROLL_TOP),
						'default_filter' => $default_filter
				)
		);

		$html = $this->display(__FILE__, 'yetanotheradvancedsearch.tpl');

		if ($iso_code !== null)
			$this->changeSmartyLanguage($kept_language);

		if ($shop_id !== null)
			$this->context->shop = $kept_shop;

		return $html;
	}

	/**
	 * Admin Area
	 */
	public function getContent()
	{
		$this->_html = '';
		$this->postProcess();
		if (Tools::isSubmit('addCriteria') || Tools::isSubmit('editCriteria'))
			$this->displayAddForm();
		elseif (Tools::isSubmit('reindex'))
		{
			YetAnotherAdvancedSearchModel::reindex($this);
			Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.
				'&token='.Tools::getAdminTokenLite('AdminModules').
				'&reindexConfirmation&module_name='.$this->name);
		}
		else $this->displayForm();
		return $this->_html;
	}

	/**
	 * Init the form
	 * @return \HelperForm
	 */
	private function initForm()
	{
		$helper = new HelperForm();
				$helper->languages = $this->context->controller->getLanguages();
				$helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
		$helper->module = $this;
		$helper->name_controller = 'yetanotheradvancedsearch';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->languages = $this->context->controller->_languages;
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&module_name='.$this->name;
		$helper->default_form_language = $this->context->controller->default_form_language;
		$helper->allow_employee_form_lang = $this->context->controller->allow_employee_form_lang;
		$helper->toolbar_scroll = true;
		$helper->show_toolbar = true;
		$helper->toolbar_btn = $this->initToolbar();
		return $helper;
	}

	/**
	 * Init the toolbar
	 * @return type
	 */
	public function initToolbar()
	{
		$current_index = AdminController::$currentIndex;
		$token = Tools::getAdminTokenLite('AdminModules');
		$back = Tools::safeOutput(Tools::getValue('back', ''));
		if (!isset($back) || empty($back))
			$back = $current_index.'&amp;configure='.$this->name.'&token='.$token.'&module_name='.$this->name;
		switch ($this->_display)
		{
		case 'add':
			$this->toolbar_btn['cancel'] = array(
				'href' => $back,
				'desc' => $this->l('Cancel')
			);
			$this->toolbar_btn['save'] = array(
				'href' => '#',
				'desc' => $this->l('Save')
			);
			break;
		case 'edit':
			$this->toolbar_btn['cancel'] = array(
				'href' => $back,
				'desc' => $this->l('Cancel')
			);
			$this->toolbar_btn['save'] = array(
				'href' => '#',
				'desc' => $this->l('Save')
			);
			break;
		case 'index':
			$this->toolbar_btn['new'] = array(
				'href' => $current_index.'&amp;configure='.$this->name.
				'&amp;token='.$token.'&amp;addCriteria&amp;module_name='.$this->name,
				'desc' => $this->l('Add new criterion')
			);
			$this->toolbar_btn['preview'] = array(
				'href' => $current_index.'&amp;configure='.$this->name.
				'&amp;token='.$token.'&amp;reindex&amp;module_name='.$this->name,
				'desc' => $this->l('Reindexing'),
			);
			break;
		default:
			break;
		}
		return $this->toolbar_btn;
	}

	/**
	 * Main Form
	 */
	protected function displayForm()
	{
				$this->_display = 'index';
		$helper = $this->initForm();

		$this->fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Criteria Configuration'),
				'image' => _PS_ADMIN_IMG_.'tab-tools.gif'
			),
			'input' => array(
				array(
					'type' => 'criteria',
					'name' => 'criteria',
					// problem with {l= in form.tpl ; we use this object so 0=>$this
					'values' => array(0 => $this, 1 => YetAnotherAdvancedSearchModel::getAllCriteria($this))
				)
			),
			'submit' => array(
				'name' => 'addCriteria',
				'title' => $this->l('Add new criterion'),
				'class' => 'button',
				'icon' => false
			)
		);

		$desc_update = 'If active, automatically updates the database once a day. ';
		$desc_update .= 'You don\'t need a CRON job, and it doesn\'t really ';
		$desc_update .= 'slow down user requests (parrallel processing).';

		// add 2nd form
		$this->fields_form[1]['form'] = array(
			'legend' => array(
				'title' => $this->l('General Configuration'),
				'image' => _PS_ADMIN_IMG_.'tab-tools.gif'
			),
			'input' => array(
				array(
					'type' => 'checkbox',
					'label' => $this->l('Update By Timestamp'),
					'name' => 'update_timestamp',
					'desc' => $this->l($desc_update),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				),
				array(
					'type' => 'color',
					'label' => $this->l('Default color'),
					'name' => 'color',
					'desc' => $this->l('Default color of the criteria')
				),
				array(
					'type' => 'color',
					'label' => $this->l('Active color'),
					'name' => 'active_color',
					'desc' => $this->l('Color of the selected criteria')
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Display counters'),
					'name' => 'display_count',
					'desc' => $this->l('Displays the counters to the right of the criteria'),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Display "Reinit"'),
					'name' => 'display_reinit',
					'desc' => $this->l('Displays the Reinit Button under the search criteria'),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Scroll top'),
					'name' => 'scroll_top',
					'desc' => $this->l('Scroll top the window after searching'),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Ignore Custom'),
					'name' => 'ignore_custom',
					'desc' => $this->l('For "Features" only : don\'t display custom ones.'),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Use VAT'),
					'name' => 'use_vat',
					'desc' => $this->l('Use VAT for price filtering'),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Current Category as filter'),
					'name' => 'use_current_category',
					'desc' => $this->l('Use current Category as filter?'),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				),
				array(
					'type' => 'checkbox',
					'label' => $this->l('Refresh cache with reindexing'),
					'name' => 'refresh_cache_with_reindex',
					'desc' => $this->l('Reindexing will be slower, but your future users will not recharge the cache'),
					'values' => array(
						'query' => array(
							array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
						),
						'id' => 'id',
						'name' => 'name'
					),
				)
			),
			'submit' => array(
				'name' => 'submitGeneralConfiguration',
				'title' => $this->l('Save	'),
				'class' => 'button'
			)
		);

		// init fields
		$this->fields_value['update_timestamp_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::UPDATE_TIMESTAMP) == 'true') ? 1 : 0;
		$this->fields_value['color'] =
			YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::COLOR);
		$this->fields_value['active_color'] =
			YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::ACTIVE_COLOR);
		$this->fields_value['display_count_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::DISPLAY_COUNT) == 'visible') ? 1 : 0;
		$this->fields_value['display_reinit_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::DISPLAY_REINIT) == 'true') ? 1 : 0;
		$this->fields_value['scroll_top_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::SCROLL_TOP) == 'true') ? 1 : 0;
		$this->fields_value['ignore_custom_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::IGNORE_CUSTOM) == 'true') ? 1 : 0;
		$this->fields_value['use_vat_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_VAT) == 'true') ? 1 : 0;
		$this->fields_value['use_current_category_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_CURRENT_CATEGORY_AS_FILTER) == 'true') ? 1 : 0;
		$this->fields_value['refresh_cache_with_reindex_on'] =
			(YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::REFRESH_CACHE_WITH_REINDEX) == 'true') ? 1 : 0;

		// add cron information
		$cron = $this->context->link->getModuleLink('yetanotheradvancedsearch', 'reindex');
		if (strpos($cron, 'token=') === false)
		{
			$sep = '?';
			if (strpos($cron, '?') !== false)
				$sep = '&';
			$cron .= $sep.'token='.YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::URL_UPDATE);
		}
		$this->fields_form[2]['form'] = array(
			'legend' => array(
							'title' => $this->l('General Information'),
							'image' => _PS_ADMIN_IMG_.'information.png'
						),
			'input' => array(
				array(
					'type' => 'cron',
					'name' => 'cron',
					'values' => array(0 => $this, 1 => $cron)
				)
			),
			'submit' => array(
				'name' => 'reindex',
				'title' => $this->l('Reindexing'),
				'class' => 'button',
				'icon' => false
			)
		);

		$helper->submit_action = '';
		$helper->title = $this->l('YAAS - Yet Another Advanced Search');
		$helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
		$this->_html .= $helper->generateForm($this->fields_form);
	}

	/**
	 * Display the add/edit criterion form
	 * @return type
	 */
	protected function displayAddForm()
	{
				$languages = $this->context->controller->getLanguages();
		$this->context->controller->addJS(($this->_path).'views/js/yetanotheradvancedsearch-admin.js', 'all');
		if (Tools::isSubmit('editCriteria') && Tools::getValue('id_criteria'))
		{
			$this->_display = 'edit';
			$id_criteria = (int)Tools::getValue('id_criteria');
			$criteria = YetAnotherAdvancedSearchModel::getCriteriaById($id_criteria);
		}
		else $this->_display = 'add';

		$input = array();
		$input[] = array(
			'type' => 'select',
			'label' => $this->l('Criterion :'),
			'name' => 'id_criteria_field',
			'options' => array(
				'query' => YetAnotherAdvancedSearchModel::getCriteriaFields($this),
				'id' => 'id_criteria_field',
				'name' => 'display'
			),
			'desc' => $this->l('The object of the criterion')
		);
		$input[] = array(
			'type' => 'select',
			'label' => $this->l('Display :'),
			'name' => 'id_layout',
			'options' => array(
				'query' => array(
					array('id_layout' => CriteriaLayoutEnum::L_LINK, 'display' => $this->l('Layout_'.CriteriaLayoutEnum::L_LINK)),
					array('id_layout' => CriteriaLayoutEnum::L_COMBO, 'display' => $this->l('Layout_'.CriteriaLayoutEnum::L_COMBO)),
					array('id_layout' => CriteriaLayoutEnum::L_SLIDE, 'display' => $this->l('Layout_'.CriteriaLayoutEnum::L_SLIDE))
				),
				'id' => 'id_layout',
				'name' => 'display'
			),
			'desc' => $this->l('The type of display')
		);
		$input[] = array(
			'type' => 'select',
			'label' => $this->l('Sort?'),
			'name' => 'id_sort_type',
			'desc' => $this->l('How to sort?'),
			'options' => array(
				'query' => array(
					array('id_sort_type' => CriteriaSortTypeEnum::NO_SORT, 'display' => $this->l('SortType_'.CriteriaSortTypeEnum::NO_SORT)),
					array('id_sort_type' => CriteriaSortTypeEnum::ASC, 'display' => $this->l('SortType_'.CriteriaSortTypeEnum::ASC)),
					array('id_sort_type' => CriteriaSortTypeEnum::DESC, 'display' => $this->l('SortType_'.CriteriaSortTypeEnum::DESC))
				),
				'id' => 'id_sort_type',
				'name' => 'display'
			)
		);
		$input[] = array(
			'type' => 'checkbox',
			'label' => $this->l('Allow multiple'),
			'name' => 'allow_multiple_f',
			'desc' => $this->l('If active, several sub-criteria can be selected'),
			'values' => array(
				'query' => array(
					array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
				),
				'id' => 'id',
				'name' => 'name'
			)
		);
		$input[] = array(
			'type' => 'checkbox',
			'label' => $this->l('Expanded'),
			'name' => 'expanded',
			'desc' => $this->l('Is the criterion expanded by default ?'),
			'values' => array(
				'query' => array(
					array('id' => 'on', 'name' => $this->l('true / false'), 'val' => '1'),
				),
				'id' => 'id',
				'name' => 'name'
			),
		);
		$input[] = array(
			'type' => 'text',
			'label' => $this->l('criterion title'),
			'name' => 'title',
			'size' => 50,
			'required' => false,
			'lang' => true,
			'desc' => $this->l('You can override the default title of the criterion. Let blank if not.')
		);
		$input[] = array(
			'type' => 'corresponding',
			'name' => 'corresponding',
			'values' => YetAnotherAdvancedSearchModel::getJsonCorresponding()
		);
		$input[] = array(
			'type' => 'hidden',
			'name' => 'editingYaas'
		);
		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend' => array(
				'title' => isset($criteria) ? $this->l('Edit the Criterion.') : $this->l('New Criterion.'),
				'image' => isset($criteria) ? _PS_ADMIN_IMG_.'edit.gif' : _PS_ADMIN_IMG_.'add.gif'
			),
			'input' => $input,
			'submit' => array(
				'name' => 'submitCriteria',
				'title' => $this->l('Save	'),
				'class' => 'button'
			)
		);
		if (isset($criteria))
		{
			$this->fields_value['id_criteria_field'] = (int)$criteria['id_criteria_field'];
			$this->fields_value['id_layout'] = (int)$criteria['layout'];
			$this->fields_value['id_sort_type'] = (int)$criteria['sort_type'];
			$this->fields_value['allow_multiple_f_on'] = (int)$criteria['allow_multiple'];
			$this->fields_value['expanded_on'] = (int)$criteria['expanded'];
			$this->fields_value['editingYaas'] = 'true';

			// set by language
			$criteria_langs = YetAnotherAdvancedSearchModel::getCriteriaLangsById($id_criteria);
			$this->fields_value['title'] = array();
			foreach ($languages as $language)
			{
				$title = '';
				foreach ($criteria_langs as $criteria_lang)
					if ($criteria_lang['id_lang'] === $language['id_lang'])
					{
						$title = $criteria_lang['title'];
						break;
					}
				$this->fields_value['title'][$language['id_lang']] = $title;
			}
		}
		else
		{
			$this->fields_value['id_criteria_field'] = -1;
			$this->fields_value['id_layout'] = CriteriaLayoutEnum::L_LINK;
			$this->fields_value['id_sort_type'] = CriteriaSortTypeEnum::NO_SORT;
			$this->fields_value['allow_multiple_f_on'] = true;
			$this->fields_value['expanded_on'] = true;
			$this->fields_value['editingYaas'] = 'false';

			// set by language
			$this->fields_value['title'] = array();
			foreach ($languages as $language)
				$this->fields_value['title'][$language['id_lang']] = '';
		}

		$helper = $this->initForm();
		if (isset($id_criteria))
		{
			$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name.'&id_criteria='.$id_criteria.'&module_name='.$this->name;
			$helper->submit_action = 'editCriteria';
		}
		else
			$helper->submit_action = 'addCriteria';

		$helper->fields_value = isset($this->fields_value) ? $this->fields_value : array();
		$this->_html .= $helper->generateForm($this->fields_form);
	}

	/**
	 * Change the positions of the table elements
	 */
	protected function changePosition()
	{
		if (!Validate::isInt(Tools::getValue('new_position')) ||
				(Tools::getValue('way') != 0 && Tools::getValue('way') != 1))
			Tools::displayError();

		$this->_html .= 'position changed!';
		$position = (int)Tools::getValue('new_position');
		$id_criteria = (int)Tools::getValue('id_criteria');

		if (Tools::getValue('way') == 0)
			$new_position = $position + 1;
		else if (Tools::getValue('way') == 1)
			$new_position = $position - 1;

		YetAnotherAdvancedSearchModel::updateCriteriaPositions($id_criteria, $position, $new_position);
		Tools::redirectAdmin('index.php?tab=AdminModules&configure='.$this->name.
			'&token='.Tools::getAdminTokenLite('AdminModules')).'&module_name='.$this->name;
	}

	/**
	 * Post validation of the form data
	 * @return boolean
	 */
	protected function postValidation()
	{
				$this->_errors = array();
		if (Tools::isSubmit('submitGeneralConfiguration'))
		{
			if (!Validate::isInt(Tools::getValue('update_timestamp_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isColor(Tools::getValue('color')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isColor(Tools::getValue('active_color')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isInt(Tools::getValue('display_count_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isInt(Tools::getValue('display_reinit_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isInt(Tools::getValue('scroll_top_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isInt(Tools::getValue('ignore_custom_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isInt(Tools::getValue('use_vat_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
						if (!Validate::isInt(Tools::getValue('use_current_category_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
						if (!Validate::isInt(Tools::getValue('refresh_cache_with_reindex_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
		}
		else if (Tools::isSubmit('submitCriteria') || Tools::isSubmit('editCriteria'))
		{
			if (!Validate::isInt(Tools::getValue('id_criteria_field')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isInt(Tools::getValue('allow_multiple_f_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			if (!Validate::isInt(Tools::getValue('expanded_on')))
				$this->_errors[] = $this->l('Invalid Criteria Value.');
			$id_layout = Tools::getValue('id_layout');
			if (!Validate::isInt($id_layout))
				if ($id_layout != CriteriaLayoutEnum::L_LIST &&
					$id_layout != CriteriaLayoutEnum::L_COMBO && $id_layout != CriteriaLayoutEnum::L_SLIDE)
					$this->_errors[] = $this->l('Invalid Criteria Value.');
			$id_sort_type = Tools::getValue('id_sort_type');
			if (!Validate::isInt($id_sort_type))
				if ($id_sort_type != CriteriaSortTypeEnum::NO_SORT &&
					$id_sort_type != CriteriaSortTypeEnum::ASC && $id_sort_type != CriteriaSortTypeEnum::DESC)
					$this->_errors[] = $this->l('Invalid Criteria Value.');
		}
		else if (Tools::isSubmit('deleteCriteria') && !Validate::isInt(Tools::getValue('id_criteria')))
			$this->_errors[] = $this->l('Invalid id_criteria');
		if (count($this->_errors))
		{
			foreach ($this->_errors as $err)
			$this->_html .= '<div class="alert error">'.$err.'</div>';
			return false;
		}
		return true;
	}

	/**
	 * Get title values.
	 *
	 * @return type
	 */
	private function getTitleValues()
	{
		$titles = array();
		$languages = $this->context->controller->getLanguages();

		foreach ($languages as $language)
		{
			$id_lang = $language['id_lang'];
			$titles[$id_lang] = Tools::getValue('title_'.$id_lang);
		}

		return $titles;
	}

	/**
	 * Process the form data
	 * @return boolean
	 */
	private function postProcess()
	{
		$clean_cache = true;

		if ($this->postValidation() == false)
			return false;

		$this->_errors = array();
		if (Tools::isSubmit('submitCriteria'))
		{
				$id_criteria_field = (int)Tools::getvalue('id_criteria_field');
				if (Tools::isSubmit('addCriteria'))
				{
						$position = YetAnotherAdvancedSearchModel::getMaxPosition();
						$allow_multiple = (int)Tools::getValue('allow_multiple_f_on');
						$expanded = (int)Tools::getValue('expanded_on');
						$id_layout = Tools::getValue('id_layout');
						$id_sort_type = Tools::getValue('id_sort_type');
						$titles = self::getTitleValues();
						$id_criteria = YetAnotherAdvancedSearchModel::insertCriteria($id_criteria_field,
								(int)Context::getContext()->shop->id, $position, $id_layout, $id_sort_type, $allow_multiple, $expanded, $titles);
				}
				elseif (Tools::isSubmit('editCriteria'))
				{
						$id_criteria = Tools::getvalue('id_criteria');
						$old_criteria = YetAnotherAdvancedSearchModel::getCriteriaById($id_criteria);
						$allow_multiple = (int)Tools::getValue('allow_multiple_f_on');
						$expanded = (int)Tools::getValue('expanded_on');
						$id_layout = Tools::getValue('id_layout');
						$id_sort_type = Tools::getValue('id_sort_type');
						$titles = self::getTitleValues();
						YetAnotherAdvancedSearchModel::updateCriteria($id_criteria, $id_criteria_field,
								$old_criteria['position'], $id_layout, $id_sort_type, $allow_multiple, $expanded, $titles);
				}
				if (Tools::isSubmit('addCriteria'))
						$redirect = 'addCriteriaConfirmation';
				elseif (Tools::isSubmit('editCriteria'))
						$redirect = 'editCriteriaConfirmation';

				Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.'&token='.
				Tools::getAdminTokenLite('AdminModules').'&'.
						$redirect.'&module_name='.$this->name);
				if (count($this->_errors))
						foreach ($this->_errors as $err)
								$this->_html .= '<div class="alert error">'.$err.'</div>';

		}
		elseif (Tools::isSubmit('deleteCriteria') && Tools::getValue('id_criteria'))
		{
				$id_criteria = Tools::getvalue('id_criteria');
				if ($id_criteria)
				{
						YetAnotherAdvancedSearchModel::deleteCriteria((int)$id_criteria);
						Tools::redirectAdmin(AdminController::$currentIndex.'&configure='.$this->name.
								'&token='.Tools::getAdminTokenLite('AdminModules').
								'&deleteCriteriaConfirmation&module_name='.$this->name);
				}
				else
						$this->_html .= $this->displayError($this->l('Error: You are trying to delete a non-existing Criterion. '));

		}
		elseif (Tools::isSubmit('submitGeneralConfiguration'))
		{

			// activate by timestamp
			$update_timestamp = (string)Tools::getValue('update_timestamp_on');
			if ($update_timestamp == 1)
					YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::UPDATE_TIMESTAMP, 'true');
			else
					YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::UPDATE_TIMESTAMP, 'false');

			// color
			$color = (string)Tools::getValue('color');
			if (Validate::isColor($color))
					YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::COLOR, $color);

			// active color
			$active_color = (string)Tools::getValue('active_color');
			if (Validate::isColor($active_color)) YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::ACTIVE_COLOR, $active_color);

			// display counters
			$display_count = (int)Tools::getValue('display_count_on');
			if ($display_count == 1) YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::DISPLAY_COUNT, 'visible');
			else YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::DISPLAY_COUNT, 'hidden');

			// display reinit
			$display_reinit = (int)Tools::getValue('display_reinit_on');
			if ($display_reinit == 1) YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::DISPLAY_REINIT, 'true');
			else YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::DISPLAY_REINIT, 'false');

			// scroll top ?
			$scroll_top = (int)Tools::getValue('scroll_top_on');
			if ($scroll_top == 1) YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::SCROLL_TOP, 'true');
			else YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::SCROLL_TOP, 'false');

			// ignore custom ?
			$ignore_custom = (int)Tools::getValue('ignore_custom_on');
			if ($ignore_custom == 1) YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::IGNORE_CUSTOM, 'true');
			else YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::IGNORE_CUSTOM, 'false');

			// use vat ?
			$use_vat = (int)Tools::getValue('use_vat_on');
			if ($use_vat == 1) YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::USE_VAT, 'true');
			else YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::USE_VAT, 'false');

			// use current category as filter ?
			$use_current_category = (int)Tools::getValue('use_current_category_on');
			if ($use_current_category == 1) YetAnotherAdvancedSearchConfig::getInstance()->setConfig(
				CriteriaConfigEnum::USE_CURRENT_CATEGORY_AS_FILTER, 'true');
			else YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::USE_CURRENT_CATEGORY_AS_FILTER, 'false');

			// refresh cache with reindex ?
			$refresh_cache_with_reindex = (int)Tools::getValue('refresh_cache_with_reindex_on');
			if ($refresh_cache_with_reindex == 1)
				YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::REFRESH_CACHE_WITH_REINDEX, 'true');
			else YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::REFRESH_CACHE_WITH_REINDEX, 'false');

			$this->_html .= $this->displayConfirmation($this->l('General configuration updated.'));
		}
		elseif (Tools::isSubmit('id_criteria') && Tools::isSubmit('way') && Tools::isSubmit('new_position'))
				$this->changePosition();
		elseif (Tools::isSubmit('addCriteriaConfirmation'))
				$this->_html .= $this->displayConfirmation($this->l('Criterion added.'));
		elseif (Tools::isSubmit('editCriteriaConfirmation'))
				$this->_html .= $this->displayConfirmation($this->l('Criterion modified.'));
		elseif (Tools::isSubmit('deleteCriteriaConfirmation'))
				$this->_html .= $this->displayConfirmation($this->l('Criterion deleted.'));
		elseif (Tools::isSubmit('reindexConfirmation'))
		{
			$clean_cache = false; // already cleaned
			$this->_html .= $this->displayConfirmation($this->l('Reindexing ok.'));
		}
		if ($clean_cache)
		{
			YetAnotherAdvancedSearchModel::cleanCache();
			if (YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::USE_CURRENT_CATEGORY_AS_FILTER) == 'true'
					&& YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::REFRESH_CACHE_WITH_REINDEX) == 'true')
				$this->adminDisplayWarning($this->l('Think to reindex in last operation, to update the cache by category.'));
		}
	}
}

?>
