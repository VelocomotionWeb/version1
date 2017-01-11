<?php
/**
* Copyright (c) 2014 - Leny GRISEL
*
*  @author    Leny GRISEL <email@lenygrisel.com>
*  @copyright 2014 Leny GRISEL
*  @license   Leny GRISEL - All rights reserved.
*  International Registered Trademark & Property of Leny GRISEL
*/

class YetAnotherAdvancedSearchModel extends ObjectModel {

	/** number of field values to switch to comboboxes instead of link */
	const NB_FIELD_VALUES_FOR_COMBO = 8;

	public $id_criteria;
	public $id_criteria_field;
	public $position;
	public $layout;

	/**
	 * @see ObjectModel::$definition
	 */
	public static $definition = array(
			'table' => 'yaas_criteria',
			'primary' => 'id_criteria',
			'fields' => array(
				'id_criteria' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
				'id_shop' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
				'id_criteria_field' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
				'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
				'layout' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
				'sort_type' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => false),
				'allow_multiple' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true)
			),
		);

	/**
	 * Drop Tables
	 * @return type
	 */
	public static function dropTables()
	{
		$sql = 'DROP TABLE IF EXISTS
			`'._DB_PREFIX_.'yaas_tmp_criteria_field_value_lang`,
			`'._DB_PREFIX_.'yaas_tmp_criteria_field_value`,
			`'._DB_PREFIX_.'yaas_tmp_criteria_field_lang`,
			`'._DB_PREFIX_.'yaas_tmp_criteria_field`,
			`'._DB_PREFIX_.'yaas_tmp_categories`,
			`'._DB_PREFIX_.'yaas_criteria_field_value_lang`,
			`'._DB_PREFIX_.'yaas_criteria_field_value`,
			`'._DB_PREFIX_.'yaas_criteria_field_lang`,
			`'._DB_PREFIX_.'yaas_criteria_config`,
			`'._DB_PREFIX_.'yaas_criteria_cache`,
			`'._DB_PREFIX_.'yaas_criteria`,
						`'._DB_PREFIX_.'yaas_criteria_lang`,
						`'._DB_PREFIX_.'yaas_criteria_field`,
						`'._DB_PREFIX_.'yaas_categories`;';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create tables of type "field"
	 * @return type
	 */
	public static function createAllFieldTables()
	{
		return self::createFieldTable() &&
			self::createFieldLangTable() &&
			self::createFieldValueTable() &&
			self::createFieldValueLangTable() &&
						self::createCategoriesTable();
	}

	/**
	 * Create all tables
	 * @param type $yaas
	 * @return type
	 */
	public static function createTables($yaas)
	{
		return (
				self::createAllFieldTables() &&
				self::createTable() &&
				self::createLangTable() &&
				self::createConfigTable() &&
				self::createCacheTable() &&
				self::populateFieldTable($yaas) &&
				self::renameFieldTable() &&
				self::populateConfigTable() &&
				self::populateTable()
			);
	}

	/**
	 * Clean tables of type "field"
	 * @param type $tmp
	 * @return type
	 */
	public static function cleanFieldTable($tmp = false)
	{
		$sql = 'DROP TABLE IF EXISTS ';
		$sql .= _DB_PREFIX_.'yaas_'.($tmp ? 'tmp_' : '').'criteria_field_value_lang,';
		$sql .= _DB_PREFIX_.'yaas_'.($tmp ? 'tmp_' : '').'criteria_field_value,';
		$sql .= _DB_PREFIX_.'yaas_'.($tmp ? 'tmp_' : '').'criteria_field_lang,';
		$sql .= _DB_PREFIX_.'yaas_'.($tmp ? 'tmp_' : '').'criteria_field,';
		$sql .= _DB_PREFIX_.'yaas_'.($tmp ? 'tmp_' : '').'categories;';
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Rename field tables
	 * @return type
	 */
	public static function renameFieldTable()
	{
		$sql = 'RENAME TABLE ';
		$sql .= _DB_PREFIX_.'yaas_tmp_criteria_field TO '._DB_PREFIX_.'yaas_criteria_field,';
		$sql .= _DB_PREFIX_.'yaas_tmp_criteria_field_lang TO '._DB_PREFIX_.'yaas_criteria_field_lang,';
		$sql .= _DB_PREFIX_.'yaas_tmp_criteria_field_value TO '._DB_PREFIX_.'yaas_criteria_field_value,';
		$sql .= _DB_PREFIX_.'yaas_tmp_criteria_field_value_lang TO '._DB_PREFIX_.'yaas_criteria_field_value_lang,';
		$sql .= _DB_PREFIX_.'yaas_tmp_categories TO '._DB_PREFIX_.'yaas_categories;';
		return self::cleanFieldTable() && Db::getInstance()->execute($sql);
	}

	/**
	 * Create main criteria table
	 * (choice of the user)
	 * @return type
	 */
	public static function createTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_criteria`(
			`id_criteria` int(10) unsigned NOT null auto_increment,
			`id_shop` int(10) unsigned NOT null,
			`id_criteria_field` int(10) unsigned NOT null,
			`layout` int(2) unsigned NOT null,
			`sort_type` int(2) null default 0,
			`allow_multiple` int(1) unsigned NOT null,
			`expanded` int(1) unsigned NOT null default \'1\',
			`position` int(10) unsigned NOT null default \'0\',
			PRIMARY KEY (`id_criteria`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create lang criteria table
	 * @return type
	 */
	public static function createLangTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_criteria_lang`(
			`id_criteria` int(10) unsigned NOT null auto_increment,
			`id_lang` int(10) unsigned NOT null,
			`title` varchar(150),
			PRIMARY KEY (`id_criteria`, `id_lang`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create internal config table
	 * @return type
	 */
	public static function createConfigTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_criteria_config`(
			`id_criteria_config` int(10) unsigned NOT null,
			`value` varchar(50) NOT null default \'\',
			PRIMARY KEY (`id_criteria_config`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create internal cache table
	 * @return type
	 */
	public static function createCacheTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_criteria_cache`(
			`id_criteria_cache` int(10) unsigned NOT null,
			`custom_key` varchar(250) NOT null default \'\',
			`id_lang` int(10) unsigned NOT null,
			`value` LONGTEXT NOT null default \'\',
			PRIMARY KEY (`id_criteria_cache`, `id_lang`, `custom_key`)
			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}


	/**
	 * Create the main field table
	 * @return type
	 */
	public static function createFieldTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_tmp_criteria_field`(
			`id_criteria_field` int(10) unsigned NOT null auto_increment,
			`id_criteria_type` int(10) unsigned NOT null,
			`id_shop` int(10) unsigned NOT null,
			`custom` varchar(40) null default \'\',
			`layout` int(2) null default 0,
			`hash` varchar(40) null default 0,
			PRIMARY KEY (`id_criteria_field`),
			UNIQUE INDEX `yaas_cf_hash` (`hash`)

			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create field lang table
	 * @return type
	 */
	public static function createFieldLangTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_tmp_criteria_field_lang`(
			`id_criteria_field_lang` int(10) unsigned NOT null auto_increment,
			`id_lang` int(10) unsigned NOT null,
			`id_criteria_field` int(10) unsigned NOT null,
			`name` varchar(40) NOT null default \'\',
			PRIMARY KEY (`id_criteria_field_lang`),
			FOREIGN KEY (`id_criteria_field`) REFERENCES `'._DB_PREFIX_.'yaas_tmp_criteria_field` (`id_criteria_field`)

			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create field value table
	 * @return type
	 */
	public static function createFieldValueTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_tmp_criteria_field_value`(
			`id_criteria_field_value` int(10) unsigned NOT null auto_increment,
			`id_criteria_field` int(10) unsigned NOT null,
			`id_internal` int(10) null,
			`id_shop` int(10) unsigned NOT null,
			`custom` varchar(40) null default \'\',
			`count` int(6) null default 0,
			PRIMARY KEY (`id_criteria_field_value`),
			FOREIGN KEY (`id_criteria_field`) REFERENCES `'._DB_PREFIX_.'yaas_tmp_criteria_field` (`id_criteria_field`)

			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create field value lang table
	 * @return type
	 */
	public static function createFieldValueLangTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_tmp_criteria_field_value_lang`(
			`id_criteria_field_value_lang` int(10) unsigned NOT null auto_increment,
			`id_lang` int(10) unsigned NOT null,
			`id_criteria_field_value` int(10) unsigned NOT null,
			`name` varchar(40) NOT null default \'\',
			PRIMARY KEY (`id_criteria_field_value_lang`),
			FOREIGN KEY (`id_criteria_field_value`) REFERENCES `'._DB_PREFIX_.'yaas_tmp_criteria_field_value` (`id_criteria_field_value`)

			) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Create categories table.
	 */
	public static function createCategoriesTable()
	{
		$sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'yaas_tmp_categories`(
		`id_category` int(10) unsigned NOT null,
		`id_children` varchar(1000) NOT null,
		PRIMARY KEY (`id_category`),
		FOREIGN KEY (`id_category`) REFERENCES `'._DB_PREFIX_.'category` (`id_category`)

		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Get a cache value by its cache key.
	 * @param type $cache_key
	 * @param type $id_lang
	 */
	public static function getCache($cache_key, $id_lang, $custom_key = '')
	{
		$sql = 'SELECT ycc.`value`
			FROM `'._DB_PREFIX_.'yaas_criteria_cache` ycc
			WHERE ycc.`id_criteria_cache` = \''.pSQL($cache_key).'\' AND ycc.`id_lang` = '.(int)$id_lang.'	
						AND ycc.`custom_key` = \''.$custom_key.'\'';
		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Set a cache Value.
	 * @param type $cache_key
	 * @param type $id_lang
	 * @param type $cache_value
	 */
	public static function setCache($cache_key, $id_lang, $cache_value, $custom_key = '')
	{
		// do not escape via pSQL : we want the html here
		$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_criteria_cache` (`id_criteria_cache`, `custom_key`, `id_lang`, `value`)
			VALUES('.(int)$cache_key.', \''.pSQL($custom_key).'\', '.(int)$id_lang.', \''.$cache_value.'\')';

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Clean cache.
	 */
	public static function cleanCache()
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'yaas_criteria_cache`;';
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Get criteria by id
	 * @param type $id_criteria
	 * @return type
	 */
	public static function getCriteriaById($id_criteria)
	{
		$sql = 'SELECT yc.`id_criteria`, yc.`position`, yc.`id_criteria_field`, yc.`layout`, yc.`sort_type`, yc.`allow_multiple`, yc.`expanded`
			FROM `'._DB_PREFIX_.'yaas_criteria` yc
			WHERE yc.id_criteria = '.(int)$id_criteria;
		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Get criteria langs by id
	 * @param type $id_criteria
	 * @return type
	 */
	public static function getCriteriaLangsById($id_criteria)
	{
		$sql = 'SELECT ycl.`id_lang`, ycl.`title`
			FROM `'._DB_PREFIX_.'yaas_criteria_lang` ycl
						WHERE ycl.id_criteria = '.(int)$id_criteria;
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get criteria lang by id criteria and id lang.
	 *
	 * @param type $id_criteria
	 * @param type $id_lang
	 * @return type
	 */
	public static function getCriteriaLangByIdAndLang($id_criteria, $id_lang)
	{
		$sql = 'SELECT ycl.`id_lang`, ycl.`title`
			FROM `'._DB_PREFIX_.'yaas_criteria_lang` ycl
						WHERE ycl.id_criteria = '.(int)$id_criteria.' AND ycl.id_lang = '.(int)$id_lang;
		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Get all criteria
	 * @param type $yaas
	 * @return type
	 */
	public static function getAllCriteria($yaas)
	{
		$sql = 'SELECT yc.`id_criteria`, yc.`expanded`, yc.`position`, yc.`id_criteria_field`, ycf.`id_criteria_type`,
			ycfl.`name`, ycf.`custom`, yc.`layout`, yc.`sort_type`, yc.`allow_multiple`
			FROM `'._DB_PREFIX_.'yaas_criteria` yc
			LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field` ycf
			ON (yc.`id_criteria_field` = ycf.`id_criteria_field`)
			LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field_lang` ycfl
			ON (ycf.`id_criteria_field` = ycfl.`id_criteria_field`)
			WHERE yc.`id_shop` = '.(int)Context::getContext()->shop->id.'
			AND ycfl.`id_lang` = '.(int)Context::getContext()->language->id.' order by yc.`position` ';

		$fields = Db::getInstance()->executeS($sql);
		$new_fields = array();
		foreach ($fields as $field)
		{
			if ($yaas != null)
			{
				$field['display'] = $field['name'].' ('.$yaas->translateType($field['id_criteria_type']).')';
				$field['display_layout'] = $yaas->translate('Layout_'.$field['layout']);
				$field['display_sort_type'] = $yaas->translate('SortType_'.$field['sort_type']);
			}
			$new_fields[] = $field;
		}
		return $new_fields;
	}

	/**
	 * Insert a new criterion
	 * @param type $id_criteria_field
	 * @param type $shop_id
	 * @param type $position
	 * @param type $layout
	 * @param type $sort_type
	 * @param type $allow_multiple
	 * @return boolean
	 */
	public static function insertCriteria($id_criteria_field, $shop_id, $position, $layout, $sort_type, $allow_multiple, $expanded, $titles = null)
	{
		$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_criteria` (`id_criteria_field`, `id_shop`, `position`, `layout`, `sort_type`, `allow_multiple`, `expanded`)
			VALUES('.(int)$id_criteria_field.', '.(int)$shop_id.', '.(int)$position.', '.(int)$layout.', '.(int)$sort_type.', '.
			(int)$allow_multiple.', '.(int)$expanded.')';

		$id = false;
		if (Db::getInstance()->execute($sql))
			$id = Db::getInstance()->Insert_ID();

		if ($titles !== null)
			$id = $id && self::insertOrUpdateCriteriaLang($id, $titles);

		return $id;
	}

	/**
	 * Update a criterion
	 * @param type $id_criteria
	 * @param type $id_criteria_field
	 * @param type $position
	 * @param type $layout
	 * @param type $sort_type
	 * @param type $allow_multiple
	 * @param type $expanded
	 */
	public static function updateCriteria($id_criteria, $id_criteria_field, $position, $layout, $sort_type, $allow_multiple, $expanded, $titles = null)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'yaas_criteria`
			SET
			`id_criteria_field` = '.(int)$id_criteria_field.',
			`position` = '.(int)$position.',
			`layout` = '.(int)$layout.',
			`sort_type` = '.(int)$sort_type.',
			`allow_multiple` = '.(int)$allow_multiple.',
			`expanded` = '.(int)$expanded.'
			WHERE `id_criteria` = '.(int)$id_criteria;

		$response = Db::getInstance()->execute($sql);

		if ($titles !== null)
			$response = $response && self::insertOrUpdateCriteriaLang($id_criteria, $titles);

		return $response;
	}

	/**
	 * Insert or update criteria langs.
	 *
	 * @param type $id_criteria
	 * @param type $titles
	 */
	public static function insertOrUpdateCriteriaLang($id_criteria, $titles)
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'yaas_criteria_lang`
		WHERE `id_criteria` = '.(int)$id_criteria;

		$response = Db::getInstance()->execute($sql);
		foreach ($titles as $id_lang => $title)
			if (!empty($title))
			{
				$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_criteria_lang`(`id_criteria`, `id_lang`, `title`)
						VALUES ('.(int)$id_criteria.', '.(int)$id_lang.', \''.pSQL($title).'\')';

				$response = $response && Db::getInstance()->execute($sql);
			}

		return $response;
	}

	/**
	 * Delete a criterion
	 * @param type $id_criteria
	 */
	public static function deleteCriteria($id_criteria)
	{
		$sql = 'DELETE FROM `'._DB_PREFIX_.'yaas_criteria`
			WHERE `id_criteria` = '.(int)$id_criteria;

		return Db::getInstance()->execute($sql);
	}

	/**
	 * Insert a field in the main criteria table (user choice)
	 * @param type $id_type
	 * @param type $id_shop
	 * @param type $layout
	 * @param type $hash_name
	 * @param type $custom
	 * @return boolean
	 */
	public static function insertField($id_type, $id_shop, $layout, $hash_name, $custom = null)
	{
		$hash = sha1($id_type.$id_shop.$layout.($custom != null ? $custom : '').$hash_name);
		$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_tmp_criteria_field` (`id_criteria_type`, `custom`, `id_shop`, `layout`, `hash`)
			VALUES('.(int)$id_type.', \''.pSQL($custom).'\', "'.(int)$id_shop.'", "'.(int)$layout.'", "'.pSQL($hash).'")';
		if (Db::getInstance()->execute($sql))
			return Db::getInstance()->Insert_ID();
		return false;
	}

	/**
	 * Insert the translation of a criterion (user choice)
	 * @param type $id_criteria_field
	 * @param type $id_lang
	 * @param type $name
	 * @return boolean
	 */
	public static function insertFieldLang($id_criteria_field, $id_lang, $name)
	{
		$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_tmp_criteria_field_lang` (`id_criteria_field`, `id_lang`, `name`)
			VALUES('.(int)$id_criteria_field.', '.(int)$id_lang.', "'.pSQL($name).'")';

		if (Db::getInstance()->execute($sql))
			return Db::getInstance()->Insert_ID();
		return false;
	}

	/**
	 * Insert the category and his children.
	 * @param type $id_category
	 * @param type $id_children
	 * @return boolean
	 */
	public static function insertCategory($id_category, $id_children)
	{
		$sql = 'INSERT IGNORE INTO `'._DB_PREFIX_.'yaas_tmp_categories` (`id_category`, `id_children`)
			VALUES('.(int)$id_category.', "'.pSQL($id_children).'");';

		if (Db::getInstance()->execute($sql))
			return Db::getInstance()->Insert_ID();
		return false;
	}

	/**
	 * Insert a field value (value of a criterion)
	 * @param type $id_criteria_field
	 * @param type $custom
	 * @param type $count
	 * @param type $id_internal
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function insertFieldValue($id_criteria_field, $custom, $count, $id_internal, $id_shop)
	{
		$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_tmp_criteria_field_value` (`id_criteria_field`, `custom`, `count`, `id_internal`, `id_shop`)
			VALUES('.(int)$id_criteria_field.', "'.pSQL($custom).'", "'.(int)$count.'", "'.(int)$id_internal.'", "'.(int)$id_shop.'")';

		if (Db::getInstance()->execute($sql))
			return Db::getInstance()->Insert_ID();
		return false;
	}

	/**
	 * Insert the translation of a field value (value of a criterion)
	 * @param type $id_criteria_field_value
	 * @param type $id_lang
	 * @param type $name
	 * @return boolean
	 */
	public static function insertFieldValueLang($id_criteria_field_value, $id_lang, $name)
	{
		$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_tmp_criteria_field_value_lang` (`id_criteria_field_value`, `id_lang`, `name`)
			VALUES('.(int)$id_criteria_field_value.', '.(int)$id_lang.', "'.pSQL($name).'")';
		if (Db::getInstance()->execute($sql))
			return Db::getInstance()->Insert_ID();
		return false;
	}

	/**
	 * Return all the system langs
	 * @return type
	 */
	public static function getLangs()
	{
		$sql = 'SELECT l.`id_lang`, l.`iso_code`, l.`language_code`
			FROM `'._DB_PREFIX_.'lang` l';
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Return all the system shops
	 * @return type
	 */
	public static function getShops()
	{
		$sql = 'SELECT l.`id_shop`, l.`id_shop_group`
			FROM `'._DB_PREFIX_.'shop` l';
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get a Config parameter
	 * @param type $config_param
	 * @return type
	 */
	public static function getConfig($config_param)
	{
		$sql = 'SELECT cc.`value`
			FROM `'._DB_PREFIX_.'yaas_criteria_config` cc
			WHERE cc.`id_criteria_config`='.(int)$config_param;
		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Insert a config parameter
	 * @param type $id_config
	 * @param type $value_config
	 * @return type
	 */
	public static function insertConfig($id_config, $value_config)
	{
		$sql = 'INSERT INTO `'._DB_PREFIX_.'yaas_criteria_config` (`id_criteria_config`, `value`)
			VALUES('.(int)$id_config.', \''.pSQL($value_config).'\')';
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Update a config parameter
	 * @param type $config_param
	 * @param type $value
	 */
	public static function updateConfig($config_param, $value)
	{
		$sql = 'UPDATE `'._DB_PREFIX_.'yaas_criteria_config`
			SET	 value=\''.pSQL($value).'\' WHERE `id_criteria_config`='.(int)$config_param;
		return Db::getInstance()->execute($sql);
	}

	/**
	 * Load all the config
	 * @return type
	 */
	public static function loadConfig()
	{
		$sql = 'SELECT cc.`id_criteria_config`, cc.`value`
			FROM `'._DB_PREFIX_.'yaas_criteria_config` cc';
		$results = Db::getInstance()->executeS($sql);
		$arr = array();
		foreach ($results as $result)
		$arr[$result['id_criteria_config']] = $result['value'];
		return $arr;
	}

	/**
	 * Get the max position for a criterion
	 * @return type
	 */
	public static function getMaxPosition()
	{
		$sql = 'SELECT MAX(position)+1
			FROM `'._DB_PREFIX_.'yaas_criteria`';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Update the criteria positions
	 * @param type $id_criteria
	 * @param type $position
	 * @param type $new_position
	 */
	public static function updateCriteriaPositions($id_criteria, $position, $new_position)
	{
		$query = 'UPDATE `'._DB_PREFIX_.'yaas_criteria`
				SET `position` = '.(int)$new_position.'
				WHERE `position` = '.(int)$position;
		$sub_query = 'UPDATE `'._DB_PREFIX_.'yaas_criteria`
					SET `position` = '.(int)$position.'
					WHERE `id_criteria` = '.(int)$id_criteria;
		return Db::getInstance()->execute($query)
			&& Db::getInstance()->execute($sub_query);
	}

	/**
	 * Populate the config table
	 * @return type
	 */
	public static function populateConfigTable()
	{
		return self::insertConfig(CriteriaConfigEnum::URL_UPDATE, Tools::substr(md5(uniqid(rand(), true)), 0, 50))
			&& self::insertConfig(CriteriaConfigEnum::UPDATE_TIMESTAMP, 'true')
			&& self::insertConfig(CriteriaConfigEnum::LAST_UPDATE, time())
			&& self::insertConfig(CriteriaConfigEnum::COLOR, '#333333')
			&& self::insertConfig(CriteriaConfigEnum::ACTIVE_COLOR, '#ff0000')
			&& self::insertConfig(CriteriaConfigEnum::DISPLAY_COUNT, 'visible')
			&& self::insertConfig(CriteriaConfigEnum::SCROLL_TOP, 'true')
			&& self::insertConfig(CriteriaConfigEnum::DISPLAY_REINIT, 'true')
			&& self::insertConfig(CriteriaConfigEnum::IGNORE_CUSTOM, 'true')
			&& self::insertConfig(CriteriaConfigEnum::USE_VAT, 'false')
						&& self::insertConfig(CriteriaConfigEnum::USE_CURRENT_CATEGORY_AS_FILTER, 'false');
	}

	/**
	 * Criteria by default, when installing.
	 * @return boolean
	 */
	public static function populateTable()
	{
		$shops = self::getShops();
		$result = true;
		foreach ($shops as $shop)
		{
			$id_shop = $shop['id_shop'];
			$sql = 'SELECT cf.`id_criteria_field`, cf.`layout`, count(cfv.id_criteria_field_value) as count
				FROM `'._DB_PREFIX_.'yaas_criteria_field` cf
				LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field_value` cfv
				ON cfv.id_criteria_field = cf.id_criteria_field WHERE cf.`id_shop`='.(int)$id_shop.'
				GROUP BY cf.`id_criteria_field`';
			$fields = Db::getInstance()->executeS($sql);
			$position = 0;
			foreach ($fields as $field)
			{
				$layout = $field['layout'];
				$allow_multiple = 1;

				// important parenthesis
				$combination = (CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO);
				if ($layout == (int)$combination)
				{
					if ($field['count'] > self::NB_FIELD_VALUES_FOR_COMBO)
					{
						$layout = CriteriaLayoutEnum::L_COMBO;
						$allow_multiple = 0;
					}
					else $layout = CriteriaLayoutEnum::L_LINK;
				}
				if (!self::insertCriteria($field['id_criteria_field'], $id_shop, $position, $layout,
					CriteriaSortTypeEnum::NO_SORT, $allow_multiple, 1))
				{
					$result = false;
					break;
				}
				$position++;
			}
		}
		return $result;
	}

	/**
	 * Keep old references
	 * @return type
	 */
	public static function keepOldReferences()
	{
		$sql = 'SELECT yc.`id_criteria`, ycf.`hash`
			FROM `'._DB_PREFIX_.'yaas_criteria` yc
			LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field` ycf
			ON (yc.`id_criteria_field` = ycf.`id_criteria_field`)';
		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Restore old references
	 * @param type $references
	 * @return boolean
	 */
	public static function updateReferences($references)
	{
		foreach ($references as $reference)
		{
			$sql = 'SELECT ycf.`id_criteria_field`
				FROM `'._DB_PREFIX_.'yaas_criteria_field` ycf
				WHERE ycf.hash=\''.pSQL($reference['hash']).'\'';
			$result = Db::getInstance()->getValue($sql);
			if ($result)
			{
				$sql = 'UPDATE `'._DB_PREFIX_.'yaas_criteria` yc
					SET yc.id_criteria_field=\''.$result.'\' WHERE yc.id_criteria=\''.(int)$reference['id_criteria'].'\'';
				Db::getInstance()->execute($sql);
			}
			else
			{
				$sql = 'DELETE FROM `'._DB_PREFIX_.'yaas_criteria` WHERE id_criteria=\''.(int)$reference['id_criteria'].'\'';
				Db::getInstance()->execute($sql);
			}
		}
		return true;
	}

	/**
	 * Launch the reindexation process
	 * @param type $yaas
	 * @return boolean
	 */
	public static function reindex($yaas)
	{
		// prevent to launch twice if alread launched and lastUpdate < 1day
		$current = time();
		$aday = 86400; // 24 x 60 x 60 (s)
		$last_update = YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::LAST_UPDATE);
		if ('true' == YetAnotherAdvancedSearchConfig::getInstance()->getConfig(CriteriaConfigEnum::CURRENTLY_REINDEXING)
			&& (($current - $last_update) <= $aday))
			return false;

		YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::CURRENTLY_REINDEXING, 'true');

		$old_references = self::keepOldReferences();
		$response = $old_references != null && self::cleanFieldTable(true)
			&& self::createAllFieldTables()
			&& self::populateFieldTable($yaas)
			&& self::renameFieldTable()
			&& self::updateReferences($old_references)
			&& self::cleanCache()
			&& self::updateConfig(CriteriaConfigEnum::LAST_UPDATE, time());

		if (self::getConfig(CriteriaConfigEnum::REFRESH_CACHE_WITH_REINDEX) == 'true')
		{
			if (self::getConfig(CriteriaConfigEnum::USE_CURRENT_CATEGORY_AS_FILTER) == 'true')
				$response = $response && self::prefilterByCategories(true);
			else
				$response = $response && self::prefilterByCategories(false);
		}
		YetAnotherAdvancedSearchConfig::getInstance()->setConfig(CriteriaConfigEnum::CURRENTLY_REINDEXING, 'false');
		return $response;
	}

	/**
	 * Find possible groups for group.
	 *
	 * @param type $group_id
	 * @param type $sub_groups
	 */
	private static function findPossibleGroupsForGroup($group_id, $sub_groups)
	{
		$possible_groups = array();
		if (count($sub_groups) > 0)
		{
			$sub_possibilities = self::findPossibleGroups($sub_groups);
			foreach ($sub_possibilities as $sub_possibility)
				$possible_groups[] = $group_id.','.$sub_possibility;
		}
		return $possible_groups;
	}

	/**
	 * Find possible groups.
	 *
	 * @param type $groups
	 * @return array
	 */
	private static function findPossibleGroups($groups)
	{
		$possible_groups = array();
		$idx = 1;
		foreach ($groups as $group)
		{
			$group_id = (int)$group['id_group'];
			$sub_groups = array_slice($groups, $idx);
			$subarray = self::findPossibleGroupsForGroup($group_id, $sub_groups);
			$possible_groups[] = $group_id; // add self
			$possible_groups = array_merge($possible_groups, $subarray);
			$idx++;
		}
		return $possible_groups;
	}


	/**
	 * Prefilter by categories.
	 */
	private static function prefilterByCategories($use_categories)
	{
		$response = true;
		$yaas = new YetAnotherAdvancedSearch();

		// get langs
		$langs = self::getLangs();

		// get all possible groups combinations
		$groups = Db::getInstance()->executeS('SELECT DISTINCT(g.`id_group`) FROM '._DB_PREFIX_.'group g order by g.`id_group` ASC');
		$possible_groups = self::findPossibleGroups($groups);

		// prefilter for no-category
		$response = $response && self::prefilterByCategory($yaas, null, $langs, $possible_groups);

		if ($use_categories)
		{

			// search in criteria fields for categories values
			$sql_category_ids = 'SELECT DISTINCT(ycfv.id_internal) FROM `'._DB_PREFIX_.'yaas_criteria_field_value` ycfv
					INNER JOIN `'._DB_PREFIX_.'yaas_criteria_field` ycf ON ycf.id_criteria_field = ycfv.id_criteria_field
					WHERE ycf.`id_criteria_type` = '.CriteriaTypeEnum::CATEGORY;

			// get categories
			$category_ids = Db::getInstance()->executeS($sql_category_ids);
			foreach ($category_ids as $category_id)
				$response = $response && self::prefilterByCategory($yaas, $category_id['id_internal'], $langs, $possible_groups);
		}

		return $response;
	}

	/**
	 * Prefilter by given category.
	 *
	 * @param type $category_id
	 */
	private static function prefilterByCategory($yaas, $category_id, $langs, $possible_groups)
	{
		$response = true;
		foreach ($langs as $lang)
			foreach ($possible_groups as $groups)
				foreach (Shop::getShops() as $shop)
				{
					$id_shop = $shop['id_shop'];

					// find all possible groups
					$html = $yaas->generateHookLeftContent($groups, $category_id, $lang['iso_code'], $id_shop);
					$custom_key = 's'.$id_shop.'-c'.$category_id.'-g'.$groups;
					$response = $response && YetAnotherAdvancedSearchModel::setCache(CriteriaCacheKeyEnum::MENU_CONTENT, $lang['id_lang'], $html, $custom_key);
				}

		return $response;
	}

	/**
	 * Populate the field table
	 * @param type $yaas
	 * @return type
	 */
	public static function populateFieldTable($yaas)
	{
		// find if in context..
		$shops = self::getShops();
		$langs = self::getLangs();
		$result = true;

		// create new fields
		foreach ($shops as $shop)
		{
			$id_shop = $shop['id_shop'];
			if ($result)
			{
				$result = self::populateFieldTableWithFeatures($id_shop)
				&& self::populateFieldTableWithAttributes($id_shop)
				&& self::populateFieldTableWithCategories($yaas, $langs, $id_shop)
				&& self::populateFieldTableWithPrices($yaas, $langs, $id_shop)
				&& self::populateFieldTableWithAvailabilities($yaas, $langs, $shop)
				&& self::populateFieldTableWithManufacturers($yaas, $langs, $id_shop)
				&& self::populateFieldTableWithConditions($yaas, $langs, $id_shop)
				&& self::populateFieldTableWithWeights($yaas, $langs, $id_shop)
				&& self::populateFieldTableWithSuppliers($yaas, $langs, $id_shop);
			}
		}
		return $result;
	}

	/**
	 * Populate the field table with categories
	 * @param type $yaas
	 * @param type $langs
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithCategories($yaas, $langs, $id_shop)
	{
		$result = true;
		$id = self::insertField(CriteriaTypeEnum::CATEGORY,
				$id_shop, CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO, 'one-category');
		foreach ($langs as $lang)
		{
			if (!self::insertFieldLang($id, $lang['id_lang'],
				$yaas->translate('Category', $lang)))
				$result = false;
			else
			{
				$sql_root_categories = 'SELECT DISTINCT(c.id_category) FROM `'._DB_PREFIX_.'category` c
					  WHERE c.`is_root_category` = 1';

				$root_categories = Db::getInstance()->executeS($sql_root_categories);
				foreach ($root_categories as $root_category)
				{
					$id_parent = $root_category['id_category'];
					$sub_categories = array();
					if (!self::populateFieldValueTableWithCategories(
							$id, $lang, $id_shop, $id_parent, $sub_categories))
					{
						$result = false;
						break;
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table with categories
	 * @param type $id
	 * @param type $lang
	 * @param type $id_shop
	 * @param type $id_parent
	 * @return boolean
	 */
	public static function populateFieldValueTableWithCategories($id, $lang, $id_shop, $id_parent, &$sub_categories)
	{
		$result = true;
		$sql = 'SELECT distinct(c.`id_category`), c.`id_parent`, cl.`name`, c.`level_depth`, c.`active`
			FROM `'._DB_PREFIX_.'category_shop` cs
			LEFT JOIN `'._DB_PREFIX_.'category` c
			ON (cs.`id_category` = c.`id_category`)
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
			ON (cl.`id_category` = c.`id_category`)
			WHERE cs.`id_shop` = '.(int)$id_shop.' AND cl.`id_lang`='.(int)$lang['id_lang'].'
			AND c.`id_parent` = '.(int)$id_parent;

		$categories = Db::getInstance()->executeS($sql);
		$new_sub_categories = array();
		foreach ($categories as $category)
		{
			if ($category['active'] == 1)
			{
				$count = self::countFieldValueProductWithCategories(
					$category['id_category'], $id_shop);
				$id_field_value = self::insertFieldValue(
					$id, $category['level_depth'], $count, $category['id_category'], $id_shop);
				if (!($id_field_value && self::insertFieldValueLang(
					$id_field_value, $lang['id_lang'], $category['name'])
					&& self::populateFieldValueTableWithCategories(
					$id, $lang, $id_shop, $category['id_category'], $new_sub_categories)))
				{
					$result = false;
					break;
				}
				if (!in_array($category['id_category'], $new_sub_categories))
					$new_sub_categories[] = $category['id_category'];
			}
			// this ELSE add children (comment if we don't want children)
			else
				self::populateFieldValueTableWithCategories(
					$id, $lang, $id_shop, $category['id_category'], $new_sub_categories);
		}
		$sub_categories = array_merge($sub_categories, $new_sub_categories);
		self::insertCategory($id_parent, join(',', $new_sub_categories));
		return $result;
	}

	/**
	 * Count field values for categories
	 * @param type $id_category
	 * @param type $id_shop
	 * @return type
	 */
	public static function countFieldValueProductWithCategories($id_category, $id_shop)
	{
		$sql = 'SELECT COUNT(DISTINCT(cp.id_product))
			FROM `'._DB_PREFIX_.'category_product` cp
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ps.id_product = cp.id_product
			WHERE cp.`id_category` = '.(int)$id_category.' AND ps.active=1 AND ps.id_shop = '.(int)$id_shop;

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Populate the field table with prices
	 * @param type $yaas
	 * @param type $langs
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithPrices($yaas, $langs, $id_shop)
	{
		$result = true;

		if (self::getConfig(CriteriaConfigEnum::USE_VAT) == 'true')
			$sql = 'SELECT MAX(ps.`price` + ps.`price` * t.`rate` / 100) as max_price, MIN(ps.`price` + ps.`price` * t.`rate` / 100) as min_price
				FROM `'._DB_PREFIX_.'product_shop` ps
				LEFT JOIN `'._DB_PREFIX_.'tax_rules_group` trg ON (trg.id_tax_rules_group = ps.id_tax_rules_group AND trg.active = 1)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (tr.id_tax_rules_group = trg.id_tax_rules_group)
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.id_tax = tr.id_tax AND t.active = 1)
				WHERE ps.`id_shop` = '.(int)$id_shop.' AND ps.active=1';
		else
			$sql = 'SELECT MAX(ps.`price`) as max_price, MIN(ps.`price`) as min_price
				FROM `'._DB_PREFIX_.'product_shop` ps WHERE ps.`id_shop` = '.(int)$id_shop.' AND ps.active=1';

		$prices = Db::getInstance()->getRow($sql);
		$id = self::insertField(CriteriaTypeEnum::PRICE,
				$id_shop, CriteriaLayoutEnum::L_SLIDE, 'one-price');

		foreach ($langs as $lang)
		{
			if (!self::insertFieldLang($id, $lang['id_lang'],
					$yaas->translate('Price', $lang))
					|| !self::populateFieldValueTableWithPrices(
					$prices['min_price'], $lang['id_lang'], $id,
					CriteriaSubTypeEnum::PRICE_MIN, $id_shop)
					|| !self::populateFieldValueTableWithPrices(
					$prices['max_price'], $lang['id_lang'],
					$id, CriteriaSubTypeEnum::PRICE_MAX, $id_shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table with prices
	 * @param type $value
	 * @param type $id_lang
	 * @param type $id
	 * @param type $id_internal
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldValueTableWithPrices($value, $id_lang, $id, $id_internal, $id_shop)
	{
		$id_field_value = self::insertFieldValue($id, null, -1, $id_internal, $id_shop);
		return $id_field_value
			&& self::insertFieldValueLang($id_field_value, $id_lang, $value);
	}

	/**
	 * Populate the field table with weights
	 * @param type $yaas
	 * @param type $langs
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithWeights($yaas, $langs, $id_shop)
	{
		$result = true;
		$sql = 'SELECT MAX(p.`weight`) as max_weight, MIN(p.`weight`) as min_weight
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ps.id_product = p.id_product
			WHERE ps.`id_shop` = '.(int)$id_shop.' AND ps.active=1';
		$weights = Db::getInstance()->getRow($sql);
		$id = self::insertField(CriteriaTypeEnum::WEIGHT, $id_shop, CriteriaLayoutEnum::L_SLIDE, 'one-weight');
		foreach ($langs as $lang)
		{
			if (!self::insertFieldLang(
					$id, $lang['id_lang'], $yaas->translate('Weight', $lang))
					|| !self::populateFieldValueTableWithWeights(
					$weights['min_weight'], $lang['id_lang'], $id,
					CriteriaSubTypeEnum::WEIGHT_MIN, $id_shop)
					|| !self::populateFieldValueTableWithWeights(
					$weights['max_weight'], $lang['id_lang'],
					$id, CriteriaSubTypeEnum::WEIGHT_MAX, $id_shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table with weights
	 * @param type $value
	 * @param type $id_lang
	 * @param type $id
	 * @param type $id_internal
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldValueTableWithWeights($value, $id_lang, $id, $id_internal, $id_shop)
	{
		$id_field_value = self::insertFieldValue($id, null, -1, $id_internal, $id_shop);
		return $id_field_value && self::insertFieldValueLang($id_field_value, $id_lang, $value);
	}

	/**
	 * Populate the field table with availabilities
	 * @param type $yaas
	 * @param type $langs
	 * @param type $shop
	 * @return boolean
	 */
	public static function populateFieldTableWithAvailabilities($yaas, $langs, $shop)
	{
		$result = true;
		$id = self::insertField(CriteriaTypeEnum::AVAILABILITY,
				$shop['id_shop'], CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO, 'one-availability');
		foreach ($langs as $lang)
		{
			if (!self::insertFieldLang($id, $lang['id_lang'],
					$yaas->translate('Availability', $lang))
					|| !self::populateFieldValueTableWithAvailabilities(
					$yaas, $lang, $id, CriteriaSubTypeEnum::AVAILABILITY_AVAILABLE, $shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table with availabilities
	 * @param type $yaas
	 * @param type $lang
	 * @param type $id
	 * @param type $id_internal
	 * @param type $shop
	 * @return type
	 */
	public static function populateFieldValueTableWithAvailabilities($yaas, $lang, $id, $id_internal, $shop)
	{
		$count = self::countFieldValueProductWithAvailabilities($shop);
		$id_field_value = self::insertFieldValue($id, null, $count, $id_internal, $shop['id_shop']);
		return $id_field_value && self::insertFieldValueLang(
				$id_field_value, $lang['id_lang'], $yaas->translate('In Stock', $lang));
	}

	/**
	 * Count field values for availabilities
	 * @param type $shop
	 * @return type
	 */
	public static function countFieldValueProductWithAvailabilities($shop)
	{
		$sql = 'SELECT COUNT(DISTINCT(p.id_product))
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
			ON (p.`id_product` = ps.`id_product`)
			WHERE p.`available_for_order` = 1 AND ps.`id_shop` = '.(int)$shop['id_shop'].'
			AND ps.`active` = 1';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Populate the field table with manufacturers
	 * @param type $yaas
	 * @param type $langs
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithManufacturers($yaas, $langs, $id_shop)
	{
		$result = true;
		$id = self::insertField(CriteriaTypeEnum::MANUFACTURER,
				$id_shop, CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO, 'one-manufacturer');
		foreach ($langs as $lang)
		{
			if (!self::insertFieldLang($id, $lang['id_lang'],
					$yaas->translate('Manufacturer', $lang))
					|| !self::populateFieldValueTableWithManufacturers(
					$lang, $id, $id_shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table with manufacturers
	 * @param type $lang
	 * @param type $id
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldValueTableWithManufacturers($lang, $id, $id_shop)
	{
		$result = true;
		$sql = 'SELECT ms.`id_manufacturer`, m.`name`
			FROM `'._DB_PREFIX_.'manufacturer_shop` ms
			INNER JOIN `'._DB_PREFIX_.'manufacturer` m
			ON (ms.`id_manufacturer` = m.`id_manufacturer`)
			WHERE ms.`id_shop` = '.(int)$id_shop.' AND m.`active` = 1';
		$manufacturers = Db::getInstance()->executeS($sql);
		foreach ($manufacturers as $manufacturer)
		{
			$count = self::countFieldValueProductWithManufacturers($manufacturer, $id_shop);
			$id_field_value = self::insertFieldValue($id, null, $count, $manufacturer['id_manufacturer'], $id_shop);
			if (!$id_field_value)
			{
				$result = false;
				break;
			}
			if (!self::insertFieldValueLang($id_field_value, $lang['id_lang'], $manufacturer['name']))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Count field values for manufacturers
	 * @param type $manufacturer
	 * @param type $id_shop
	 * @return type
	 */
	public static function countFieldValueProductWithManufacturers($manufacturer, $id_shop)
	{
		$sql = 'SELECT COUNT(DISTINCT(p.id_product))
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
			ON (p.`id_product` = ps.`id_product`)
			WHERE p.`id_manufacturer` = '.(int)$manufacturer['id_manufacturer'].' AND ps.`id_shop` = '.(int)$id_shop.'
			AND ps.`active` = 1';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Populate the field table with conditions
	 * @param type $yaas
	 * @param type $langs
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithConditions($yaas, $langs, $id_shop)
	{
		$result = true;
		$id = self::insertField(CriteriaTypeEnum::CONDITION,
				$id_shop, CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO, 'one-condition');
		foreach ($langs as $lang)
		{
			if (!self::insertFieldLang($id, $lang['id_lang'],
					$yaas->translate('Condition', $lang))
					|| !self::populateFieldValueTableWithConditions(
					$lang, $yaas, $id, $id_shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table with conditions
	 * @param type $lang
	 * @param type $yaas
	 * @param type $id
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldValueTableWithConditions($lang, $yaas, $id, $id_shop)
	{
		$result = true;
		$idx = 0;
		$conditions = YetAnotherAdvancedSearch::$condition_types;
		foreach ($conditions as $condition)
		{
			$count = self::countFieldValueProductWithConditions($condition, $id_shop);
			$id_field_value = self::insertFieldValue($id, null, $count, $idx, $id_shop);
			if (!$id_field_value)
			{
				$result = false;
				break;
			}
			if (!self::insertFieldValueLang($id_field_value,
					$lang['id_lang'], $yaas->translate('Condition_'.$condition, $lang)))
			{
				$result = false;
				break;
			}
			$idx++;
		}
		return $result;
	}

	/**
	 * Count field values for conditions
	 * @param type $condition
	 * @param type $id_shop
	 * @return type
	 */
	public static function countFieldValueProductWithConditions($condition, $id_shop)
	{
		$sql = 'SELECT COUNT(DISTINCT(p.id_product))
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
			ON (p.`id_product` = ps.`id_product`)
			WHERE p.`condition` = \''.pSQL($condition).'\' AND ps.`id_shop` = '.(int)$id_shop.'
			AND ps.`active` = 1';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Populate the field table for attributes
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithAttributes($id_shop)
	{
		$result = true;
		$sql = 'SELECT ag.`id_attribute_group`, ag.`group_type`
			FROM `'._DB_PREFIX_.'attribute_group_shop` ags
			LEFT JOIN `'._DB_PREFIX_.'attribute_group` ag
			ON (ags.`id_attribute_group` = ag.`id_attribute_group`)
			WHERE ags.`id_shop` = '.(int)$id_shop;
		$attributes = Db::getInstance()->executeS($sql);
		foreach ($attributes as $attribute)
		{
			$id = self::insertField(CriteriaTypeEnum::ATTRIBUTE,
					$id_shop, CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO, 'several-attributes-'.
					$attribute['id_attribute_group'], $attribute['group_type']);
			if (!self::populateFieldLangTableWithAttributes($attribute, $id)
					|| !self::populateFieldValueTableWithAttributes($attribute, $id, $id_shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field lang table for attributes
	 * @param type $attribute
	 * @param type $id
	 * @return boolean
	 */
	public static function populateFieldLangTableWithAttributes($attribute, $id)
	{
		$result = true;
		$sql = 'SELECT agl.`id_lang`, agl.`public_name` FROM `'._DB_PREFIX_.
			'attribute_group_lang` agl WHERE agl.`id_attribute_group` = '.(int)$attribute['id_attribute_group'];
		$attribute_langs = Db::getInstance()->executeS($sql);
		foreach ($attribute_langs as $attribute_lang)
		{
			if (!self::insertFieldLang($id, $attribute_lang['id_lang'], $attribute_lang['public_name']))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table for attributes
	 * @param type $attribute
	 * @param type $id
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldValueTableWithAttributes($attribute, $id, $id_shop)
	{
		$result = true;
		$sql = 'SELECT a.`id_attribute`, a.`color`
			FROM `'._DB_PREFIX_.'attribute_shop` ass
			LEFT JOIN `'._DB_PREFIX_.'attribute` a
			ON (a.`id_attribute` = ass.`id_attribute`)
			WHERE a.`id_attribute_group` = '.(int)$attribute['id_attribute_group'].' AND ass.`id_shop` = '.(int)$id_shop;

		$attribute_values = Db::getInstance()->executeS($sql);

		foreach ($attribute_values as $attribute_value)
		{
			$count = self::countFieldValueProductWithAttributes($attribute_value['id_attribute'], $id_shop);
			$id_field_value = self::insertFieldValue(
			$id, $attribute_value['color'], $count, $attribute_value['id_attribute'], $id_shop);
			if (!$id_field_value)
			{
				$result = false;
				break;
			}
			if (!self::populateFieldValueLangTableWithAttributes($attribute_value, $id_field_value))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Count field values for attributes
	 * @param type $id_attribute
	 * @param type $id_shop
	 * @return type
	 */
	public static function countFieldValueProductWithAttributes($id_attribute, $id_shop)
	{
		$sql = 'SELECT COUNT(DISTINCT(pa.id_product))
			FROM `'._DB_PREFIX_.'product_attribute` pa
			INNER JOIN `'._DB_PREFIX_.'product_attribute_combination` pac
			ON (pac.`id_product_attribute` = pa.`id_product_attribute`)
			INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas
			ON (pas.`id_product_attribute` = pa.`id_product_attribute`)
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ps.id_product = pa.id_product
			WHERE pac.`id_attribute` = '.(int)$id_attribute.'
			AND pas.`id_shop` = '.(int)$id_shop.' AND ps.id_shop='.(int)$id_shop.' AND ps.active=1';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Populate field value lang table for attributes
	 * @param type $attribute_value
	 * @param type $id
	 * @return boolean
	 */
	public static function populateFieldValueLangTableWithAttributes($attribute_value, $id)
	{
		$result = true;
		$sql = 'SELECT al.`id_lang`, al.`name` FROM `'._DB_PREFIX_.'attribute_lang` al WHERE al.`id_attribute` = '.(int)$attribute_value['id_attribute'];
		$attribute_value_langs = Db::getInstance()->executeS($sql);
		foreach ($attribute_value_langs as $attribute_value_lang)
		{
			if (!self::insertFieldValueLang($id, $attribute_value_lang['id_lang'], $attribute_value_lang['name']))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field table with suppliers
	 * @param type $yaas
	 * @param type $langs
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithSuppliers($yaas, $langs, $id_shop)
	{
		$result = true;
		$id = self::insertField(CriteriaTypeEnum::SUPPLIER,
				$id_shop, CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO, 'one-supplier');
		foreach ($langs as $lang)
		{
			if (!self::insertFieldLang($id, $lang['id_lang'],
					$yaas->translate('Supplier', $lang))
					|| !self::populateFieldValueTableWithSuppliers(
					$lang, $id, $id_shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value table with suppliers
	 * @param type $lang
	 * @param type $id
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldValueTableWithSuppliers($lang, $id, $id_shop)
	{
		$result = true;
		$sql = 'SELECT ss.`id_supplier`, s.`name`
			FROM `'._DB_PREFIX_.'supplier_shop` ss
			INNER JOIN `'._DB_PREFIX_.'supplier` s
			ON (ss.`id_supplier` = s.`id_supplier`)
			WHERE ss.`id_shop` = '.(int)$id_shop.' AND s.`active` = 1';
		$suppliers = Db::getInstance()->executeS($sql);
		foreach ($suppliers as $supplier)
		{
			$count = self::countFieldValueProductWithSuppliers($supplier, $id_shop);
			$id_field_value = self::insertFieldValue($id, null, $count, $supplier['id_supplier'], $id_shop);
			if (!$id_field_value)
			{
				$result = false;
				break;
			}
			if (!self::insertFieldValueLang($id_field_value, $lang['id_lang'], $supplier['name']))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Count field values for suppliers
	 * @param type supplier
	 * @param type $id_shop
	 * @return type
	 */
	public static function countFieldValueProductWithSuppliers($supplier, $id_shop)
	{
		$sql = 'SELECT COUNT(DISTINCT(p.id_product))
			FROM `'._DB_PREFIX_.'product` p
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
			ON (p.`id_product` = ps.`id_product`)
			WHERE p.`id_supplier` = '.(int)$supplier['id_supplier'].' AND ps.`id_shop` = '.(int)$id_shop.'
			AND ps.`active` = 1';

		return Db::getInstance()->getValue($sql);
	}


	/**
	 * Populate the field table for features
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldTableWithFeatures($id_shop)
	{
		$result = true;
		$sql = 'SELECT f.`id_feature`
			FROM `'._DB_PREFIX_.'feature_shop` fs
			LEFT JOIN `'._DB_PREFIX_.'feature` f
			ON (f.`id_feature` = fs.`id_feature`)
			WHERE fs.id_shop = '.(int)$id_shop;

		$features = Db::getInstance()->executeS($sql);
		foreach ($features as $feature)
		{
			$id = self::insertField(CriteriaTypeEnum::FEATURE, $id_shop,
					CriteriaLayoutEnum::L_LINK | CriteriaLayoutEnum::L_COMBO, 'several-features-'.$feature['id_feature']);
			if (!self::populateFieldLangTableWithFeatures($feature, $id)
					|| !self::populateFieldValueTableWithFeatures($feature, $id, $id_shop))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field lang table for features
	 * @param type $feature
	 * @param type $id
	 * @return boolean
	 */
	public static function populateFieldLangTableWithFeatures($feature, $id)
	{
		$result = true;
		$sql = 'SELECT fl.`id_lang`, fl.`name` FROM `'._DB_PREFIX_.'feature_lang` fl WHERE fl.`id_feature` = '.(int)$feature['id_feature'];
		$feature_langs = Db::getInstance()->executeS($sql);
		foreach ($feature_langs as $feature_lang)
		{
			if (!self::insertFieldLang($id, $feature_lang['id_lang'], $feature_lang['name']))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Populate the field value for features
	 * @param type $feature
	 * @param type $id
	 * @param type $id_shop
	 * @return boolean
	 */
	public static function populateFieldValueTableWithFeatures($feature, $id, $id_shop)
	{
		$result = true;
		$sql = 'SELECT fv.`id_feature_value`, fv.`custom` FROM `'._DB_PREFIX_.
			'feature_value` fv WHERE fv.`id_feature` = '.(int)$feature['id_feature'];
		$feature_values = Db::getInstance()->executeS($sql);
		foreach ($feature_values as $feature_value)
		{
			$count = self::countFieldValueProductWithFeatures(
				$feature_value['id_feature_value'], $id_shop);
			$id_field_value = self::insertFieldValue($id, $feature_value['custom'],
				$count, $feature_value['id_feature_value'], $id_shop);
			if (!$id_field_value)
			{
				$result = false;
				break;
			}
			if (!self::populateFieldValueLangTableWithFeatures($feature_value, $id_field_value))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Count field values for features
	 * @param type $id_feature_value
	 * @param type $id_shop
	 * @return type
	 */
	public static function countFieldValueProductWithFeatures($id_feature_value, $id_shop)
	{
		$sql = 'SELECT COUNT(fp.`id_product`)
			FROM `'._DB_PREFIX_.'feature_product` fp
			INNER JOIN `'._DB_PREFIX_.'feature_shop` fs
			ON (fs.`id_feature` = fp.`id_feature`)
			LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON ps.id_product = fp.id_product
			WHERE fp.`id_feature_value` = '.(int)$id_feature_value.'
			AND fs.`id_shop` = '.(int)$id_shop.' AND ps.id_shop='.(int)$id_shop.' AND ps.active=1';

		return Db::getInstance()->getValue($sql);
	}

	/**
	 * Populate field value lang table for features
	 * @param type $feature_value
	 * @param type $id
	 * @return boolean
	 */
	public static function populateFieldValueLangTableWithFeatures($feature_value, $id)
	{
		$result = true;
		$sql = 'SELECT fvl.`id_lang`, fvl.`value` FROM `'._DB_PREFIX_.'feature_value_lang`
			fvl WHERE fvl.`id_feature_value` = '.(int)$feature_value['id_feature_value'];
		$feature_value_langs = Db::getInstance()->executeS($sql);
		foreach ($feature_value_langs as $feature_value_lang)
		{
			if (!self::insertFieldValueLang($id, $feature_value_lang['id_lang'], $feature_value_lang['value']))
			{
				$result = false;
				break;
			}
		}
		return $result;
	}

	/**
	 * Get a field
	 * @param type $id_criteria_field
	 * @return type
	 */
	public static function getField($id_criteria_field)
	{
		$sql = 'SELECT ycf.`id_criteria_field`, ycf.`id_criteria_type`
			FROM `'._DB_PREFIX_.'yaas_criteria_field` ycf
			WHERE ycf.`id_criteria_field` = '.(int)$id_criteria_field.'
			AND ycf.`id_shop` = '.(int)Context::getContext()->shop->id;

		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Get the field corresponding to the given category_id.
	 *
	 * @param type $category_id
	 * @return type
	 */
	public static function getFieldForCategory($category_id)
	{
		$sql = 'SELECT ycf.`id_criteria_field`, ycf.`id_criteria_type`
		    FROM `'._DB_PREFIX_.'yaas_criteria_field_value` ycfv
		    INNER JOIN `'._DB_PREFIX_.'yaas_criteria_field` ycf ON ycf.id_criteria_field = ycfv.id_criteria_field
		    WHERE ycfv.`id_internal` = '.(int)$category_id.'
		    AND ycf.`id_shop` = '.(int)Context::getContext()->shop->id.'
		    AND ycf.`id_criteria_type` = '.CriteriaTypeEnum::CATEGORY;

		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Get a field value
	 * @param type $id_criteria_field_value
	 * @return type
	 */
	public static function getFieldValue($id_criteria_field_value)
	{
		$sql = 'SELECT ycfv.`custom`, ycfvl.`name`, ycfv.`count`, ycfv.`id_criteria_field_value`, ycfv.`id_criteria_field`, ycfv.`id_internal`
			FROM `'._DB_PREFIX_.'yaas_criteria_field_value` ycfv
			LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field_value_lang` ycfvl
			ON (ycfv.`id_criteria_field_value` = ycfvl.`id_criteria_field_value`) WHERE ycfv.`id_criteria_field_value` = '.$id_criteria_field_value.'
			AND ycfvl.`id_lang` = '.(int)Context::getContext()->language->id.'
			AND ycfv.`id_shop` = '.(int)Context::getContext()->shop->id;

		return Db::getInstance()->getRow($sql);
	}

	/**
	 * Get all field values for a criteria field, used by front
	 * @param type $id_criteria_field
	 * @return type
	 */
	public static function getFieldValues($id_criteria_field)
	{
		$sql = 'SELECT ycfv.`custom`, ycfvl.`name`, ycfv.`count`,
			ycfv.`id_criteria_field_value`, ycfv.`id_criteria_field`, ycfv.`id_internal`, ycfv.`custom`
			FROM `'._DB_PREFIX_.'yaas_criteria_field_value` ycfv
			LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field_value_lang` ycfvl
			ON (ycfv.`id_criteria_field_value` = ycfvl.`id_criteria_field_value`)
			WHERE ycfv.`id_criteria_field` = '.(int)$id_criteria_field.'
			AND ycfvl.`id_lang` = '.(int)Context::getContext()->language->id.'
			AND ycfv.id_shop = '.(int)Context::getContext()->shop->id;

		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get all the field values for a category, called by front
	 * @param type $id_criteria_field
	 * @param type $groups
	 * @return type
	 */
	public static function getFieldValuesForCategory($id_criteria_field, $groups)
	{
		$sql = 'SELECT ycfv.`custom`, ycfvl.`name`, ycfv.`count`, ycfv.`id_criteria_field_value`,
			ycfv.`id_criteria_field`, ycfv.`id_internal`, ycfv.`custom`
			FROM `'._DB_PREFIX_.'yaas_criteria_field_value` ycfv
			LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field_value_lang` ycfvl
			ON (ycfv.`id_criteria_field_value` = ycfvl.`id_criteria_field_value`)
			LEFT JOIN `'._DB_PREFIX_.'category_group` cg
			ON (ycfv.`id_internal` = cg.`id_category`)
			WHERE ycfv.`id_criteria_field` = '.(int)$id_criteria_field.'
			AND ycfvl.`id_lang` = '.(int)Context::getContext()->language->id.'
			AND ycfv.id_shop = '.(int)Context::getContext()->shop->id.'
			AND cg.`id_group` IN  ('.pSQL($groups).')';

		return Db::getInstance()->executeS($sql);
	}

	/**
	 * Get all criteria fields
	 * @param type $yaas
	 * @return string
	 */
	public static function getCriteriaFields($yaas)
	{
		$sql = 'SELECT ycf.`id_criteria_field`, ycf.`id_criteria_type`, ycfl.`name`
			FROM `'._DB_PREFIX_.'yaas_criteria_field` ycf
			LEFT JOIN `'._DB_PREFIX_.'yaas_criteria_field_lang` ycfl
			ON (ycf.`id_criteria_field` = ycfl.`id_criteria_field`)
			WHERE ycfl.`id_lang` = '.(int)Context::getContext()->language->id.'
			AND ycf.`id_shop` = '.(int)Context::getContext()->shop->id;

		$fields = Db::getInstance()->executeS($sql);
		$new_fields = array();
		foreach ($fields as $field)
		{
			$field['display'] = $field['name'].' ('.$yaas->translateType($field['id_criteria_type']).')';
			$new_fields[] = $field;
		}
		return $new_fields;
	}

	/**
	 * JSON to get the corresponding between types and layouts.
	 * @return type
	 */
	public static function getJsonCorresponding()
	{
		$sql = 'SELECT ycf.`id_criteria_field` as id, ycf.`layout` as layout
			FROM `'._DB_PREFIX_.'yaas_criteria_field` ycf
			WHERE ycf.`id_shop` = '.(int)Context::getContext()->shop->id;
		$fields = Db::getInstance()->executeS($sql);
		return Tools::jsonEncode($fields);
	}

}
