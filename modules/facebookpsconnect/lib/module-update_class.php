<?php
/**
 * module-update_class.php file defines method for updating the module
 */

class BT_FpcModuleUpdate
{
	/**
	 * @var $aErrors : store errors
	 */
	public $aErrors = array();

	/**
	 * Magic Method __construct
	 *
	 * @category update collection
	 */
	public function __construct()
	{

	}


	/**
	 * run() method execute required function
	 * @param $aParam
	 */
	public function run(array $aParam = null)
	{
		// get type
		$aParam['sType'] = empty($aParam['sType'])? 'tables' : $aParam['sType'];

		switch ($aParam['sType']) {
			case 'tables' : // use case - update tables
			case 'fields' : // use case - update fields
			case 'hooks' : // use case - update hooks
			case 'templates' : // use case - update templates
				// execute match function
				call_user_func_array(array($this, '_update' . ucfirst($aParam['sType'])), array($aParam));
				break;
			default :
				break;
		}
	}


	/**
	 * _updateTables() method update tables if required
	 *
	 * @param array $aParam
	 */
	private function _updateTables(array $aParam)
	{
		// set transaction
		Db::getInstance()->Execute('BEGIN');

		if (!empty($GLOBALS[_FPC_MODULE_NAME . '_SQL_UPDATE']['table'])) {
			// loop on each elt to update SQL
			foreach ($GLOBALS[_FPC_MODULE_NAME . '_SQL_UPDATE']['table'] as $sTable => $sSqlFile) {
				// execute query
				$bResult = Db::getInstance()->ExecuteS('SHOW TABLES LIKE "' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_'. $sTable .'"');

				// if empty - update
				if (empty($bResult)) {
					require_once(_FPC_PATH_CONF . 'install.conf.php');
					require_once(_FPC_PATH_LIB_INSTALL . 'install-ctrl_class.php');

					// use case - KO update
					if (!BT_InstallCtrl::run('install', 'sql', _FPC_PATH_SQL . $sSqlFile)) {
						$this->aErrors[] = array('table' => $sTable, 'file' => $sSqlFile);
					}
				}
			}
		}

		if (empty($this->aErrors)) {
			Db::getInstance()->Execute('COMMIT');
		}
		else {
			Db::getInstance()->Execute('ROLLBACK');
		}
	}


	/**
	 * _updateFields() method update fields if required
	 *
	 * @param array $aParam
	 */
	private function _updateFields(array $aParam)
	{
		// set transaction
		Db::getInstance()->Execute('BEGIN');

		if (!empty($GLOBALS[_FPC_MODULE_NAME . '_SQL_UPDATE']['field'])) {
			// loop on each elt to update SQL
			foreach ($GLOBALS[_FPC_MODULE_NAME . '_SQL_UPDATE']['field'] as $sFieldName => $aOption) {
				// execute query
				$aResult = Db::getInstance()->ExecuteS('SHOW COLUMNS FROM ' .  _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_'. $aOption['table'] . ' LIKE "' . $aOption['field'] .'"');

				// use case - column exists but we need to test and change the column's definition
				if (!empty($aResult)
					&& !empty($aOption['check'])
					&& !empty($aOption['type'])
					&& !empty($aOption['value'])
				) {
					if (!empty($aResult[0])) {
						if ($aResult[0]['Type'] != $aOption['value']) {
							$aResult = array();
						}
					}
				}

				// if empty - update
				if (empty($aResult)) {
					require_once(_FPC_PATH_CONF . 'install.conf.php');
					require_once(_FPC_PATH_LIB_INSTALL . 'install-ctrl_class.php');

					// use case - KO update
					if (!BT_InstallCtrl::run('install', 'sql', _FPC_PATH_SQL . $aOption['file'])) {
						$aErrors[] = array('field' => $aOption['field'], 'linked' => $aOption['table'], 'file' => $aOption['file']);
					}
				}
			}
		}

		if (empty($this->aErrors)) {
			Db::getInstance()->Execute('COMMIT');
		}
		else {
			Db::getInstance()->Execute('ROLLBACK');
		}
	}

	/**
	 * _updateHooks() method update hooks if required
	 *
	 * @category admin collection
	 * @param array $aParam
	 */
	private function _updateHooks(array $aParam)
	{
		require_once(_FPC_PATH_CONF . 'install.conf.php');
		require_once(_FPC_PATH_LIB_INSTALL . 'install-ctrl_class.php');

		// use case - hook register ko
		if (!BT_InstallCtrl::run('install', 'config', array('bHookOnly' => true))) {
			$this->aErrors[] = array('table' => 'ps_hook_module', 'file' => ModuleTemplate::$oModule->l('register hooks KO'));
		}
	}


	/**
	 * _updateTemplates() method update templates if required
	 *
	 * @param array $aParam
	 */
	private function _updateTemplates(array $aParam)
	{
		require_once(_FPC_PATH_LIB_COMMON . 'dir-reader.class.php');

		// get templates files
		$aTplFiles = BT_DirReader::create()->run(array('path' => _FPC_PATH_TPL, 'recursive' => true, 'extension' => 'tpl', 'subpath' => true));

		if (!empty($aTplFiles)) {
			global $smarty;

			if (method_exists($smarty, 'clearCompiledTemplate')) {
				$smarty->clearCompiledTemplate();
			}
			elseif (method_exists($smarty, 'clear_compiled_tpl')) {
				foreach ($aTplFiles as $aFile) {
					$smarty->clear_compiled_tpl($aFile['filename']);
				}
			}
		}
	}


	/**
	 * getErrors() method returns errors
	 *
	 * @return array
	 */
	public function getErrors()
	{
		return (
			$this->aErrors
		);
	}

	/**
	 * create() method manages singleton
	 *
	 * @return array
	 */
	public static function create()
	{
		static $oModuleUpdate;

		if (null === $oModuleUpdate) {
			$oModuleUpdate = new BT_FpcModuleUpdate();
		}
		return $oModuleUpdate;
	}
}