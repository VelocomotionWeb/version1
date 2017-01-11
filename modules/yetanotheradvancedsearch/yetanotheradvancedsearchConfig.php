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

class YetAnotherAdvancedSearchConfig {

	/**
	 * @var YetAnotherAdvancedSearchConfig
	 * @access private
	 * @static
	 */
	private static $instance = null;

	/**
	 * Keep object config
	 * @var type
	 */
	private $config = null;

	/**
	 * Méthode qui crée l'unique instance de la classe
	 * si elle n'existe pas encore puis la retourne.
	 *
	 * @param void
	 * @return YetAnotherAdvancedSearchConfig
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance))
			self::$instance = new YetAnotherAdvancedSearchConfig();
		return self::$instance;
	}

	/**
	 * Get Config Key
	 * @param type $key
	 * @return type
	 */
	public function getConfig($key)
	{
		if ($this->config == null)
			$this->config = YetAnotherAdvancedSearchModel::loadConfig();
		if (array_key_exists($key, $this->config))
			return $this->config[$key];
		return null;
	}

	/**
	 * Set Config Key
	 * @param type $key
	 * @param type $value
	 */
	public function setConfig($key, $value)
	{
		if ($this->config == null)
			$this->config = YetAnotherAdvancedSearchModel::loadConfig();
		if (array_key_exists($key, $this->config))
			YetAnotherAdvancedSearchModel::updateConfig($key, $value);
		else
			YetAnotherAdvancedSearchModel::insertConfig($key, $value);
		$this->config[$key] = $value;
	}
}
