<?php
/**
 * admin-update_class.php file defines method to add or update content for basic settings / FILL ALL update data type
 */



class BT_AdminUpdate implements BT_IAdmin
{
	/**
	 * Magic Method __construct
	 */
	private function __construct()
	{

	}

	/**
	 * Magic Method __destruct
	 */
	public function __destruct()
	{

	}

	/**
	 * run() method update all tabs content of admin page
	 * 
	 * @param array $aParam
	 * @return array
	 */
	public function run(array $aParam = null)
	{
		// set variables
		$aDisplayInfo = array();

		// get type
		$aParam['sType'] = empty($aParam['sType'])? '' : $aParam['sType'];

		switch ($aParam['sType']) {
			case 'basic'		: // use case - update snippets settings
			case 'connector'    : // use case - update connector settings
			case 'hook'         : // use case - update hook
				// execute match function
				$aDisplayInfo = call_user_func_array(array($this, '_update' . ucfirst($aParam['sType'])), array($aParam));
				break;
			default :
				break;
		}

		return $aDisplayInfo;
	}

	/**
	 * _updateBasic() method update basic settings
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _updateBasic(array $aPost)
	{
		// clean headers
		@ob_end_clean();

		// set
		$aUpdate = array();

		try {
			// use case - check display fancy popin for asking to associate FB account with PS
			$bDisplayAskFbPopin = (Tools::getIsset(strtolower(_FPC_MODULE_NAME) . 'DisplayFbPopin') && Tools::getValue(strtolower(_FPC_MODULE_NAME) . 'DisplayFbPopin') == 'true')? true : false;

			if (!Configuration::updateValue(_FPC_MODULE_NAME . '_DISPLAY_FB_POPIN', $bDisplayAskFbPopin)) {
				throw new Exception(FacebookPsConnect::$oModule->l('An error occurred during FB association popin update', 'admin-update_class') . '.', 110);
			}

			// use case - check display block
			$bDisplayBlock = (Tools::getIsset(strtolower(_FPC_MODULE_NAME) . 'DisplayBlock') && Tools::getValue(strtolower(_FPC_MODULE_NAME) . 'DisplayBlock') == 'true')? true : false;

			if (!Configuration::updateValue(_FPC_MODULE_NAME . '_DISPLAY_BLOCK', $bDisplayBlock)) {
				throw new Exception(FacebookPsConnect::$oModule->l('An error occurred during block display update', 'admin-update_class') . '.', 111);
			}

			// use case - check display account information block
			$bDisplayBlockInfoAccount = (Tools::getIsset(strtolower(_FPC_MODULE_NAME) . 'DisplayBlockInfoAccount') && Tools::getValue(strtolower(_FPC_MODULE_NAME) . 'DisplayBlockInfoAccount') == 'true')? true : false;

			if (!Configuration::updateValue(_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_ACCOUNT', $bDisplayBlockInfoAccount)) {
				throw new Exception(FacebookPsConnect::$oModule->l('An error occurred during block display update', 'admin-update_class') . '.', 112);
			}

			// use case - check display cart information block
			$bDisplayBlockInfoCart = (Tools::getIsset(strtolower(_FPC_MODULE_NAME) . 'DisplayBlockInfoCart') && Tools::getValue(strtolower(_FPC_MODULE_NAME) . 'DisplayBlockInfoCart') == 'true')? true : false;

			if (!Configuration::updateValue(_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_CART', $bDisplayBlockInfoCart)) {
				throw new Exception(FacebookPsConnect::$oModule->l('An error occurred during block display update', 'admin-update_class') . '.', 113);
			}

			// use case - set  default customer group
			if (Tools::getIsset(strtolower(_FPC_MODULE_NAME) . 'DefaultGroup')) {
				$iDefaultCustGroup = Tools::getValue(strtolower(_FPC_MODULE_NAME) . 'DefaultGroup');

				if (is_numeric($iDefaultCustGroup)) {
					if (!Configuration::updateValue(_FPC_MODULE_NAME . '_DEFAULT_CUSTOMER_GROUP', $iDefaultCustGroup)) {
						throw new Exception(FacebookPsConnect::$oModule->l('An error occurred during default customer group update', 'admin-update_class') . '.', 114);
					}
				}
				else {
					throw new Exception(FacebookPsConnect::$oModule->l('Default customer group is not a numeric', 'admin-update_class') . '.', 115);
				}
			}
			// use case - set  default API request method
			if (Tools::getIsset(strtolower(_FPC_MODULE_NAME) . 'ApiRequestType')) {
				$iDefaultApiMethod = Tools::getValue(strtolower(_FPC_MODULE_NAME) . 'ApiRequestType');

				if (!empty($iDefaultApiMethod)) {
					if (!Configuration::updateValue(_FPC_MODULE_NAME . '_API_REQUEST_METHOD', $iDefaultApiMethod)) {
						throw new Exception(FacebookPsConnect::$oModule->l('An error occurred during default API request method update', 'admin-update_class') . '.', 116);
					}
				}
			}

			// use case - if OPC activate, update block's text below connectors' button
			if (Tools::getIsset(strtolower(_FPC_MODULE_NAME) . 'DisplayBlockInfoCart')) {
				$bDisplayBlockOpc = Tools::getValue(strtolower(_FPC_MODULE_NAME) . 'DisplayBlockInfoCart') == 'true'? true : false;

				if (!Configuration::updateValue(_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_CART', $bDisplayBlockOpc)) {
					throw new Exception(FacebookPsConnect::$oModule->l('An error occurred during display block cart update', 'admin-update_class') . '.', 117);
				}

			}
		}
		catch (Exception $e) {
            $aUpdate['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
		}

		// get configuration options
		BT_FPCModuleTools::getConfiguration();

		// require admin configure class - to factorise
		require_once(_FPC_PATH_LIB_ADMIN . 'admin-display_class.php');

		// get run of admin display in order to display first page of admin with basic settings updated
		$aData = BT_AdminDisplay::create()->run(array('sType' => 'basic'));

		// use case - empty error and updating status
        $aData['assign'] = array_merge($aData['assign'], array(
			'iActiveTab'    => 1,
			'bUpdate'       => (empty($aUpdate['aErrors']) ? true : false),
		), $aUpdate);

		// destruct
		unset($aUpdate);

		return $aData;
	}

	/**
	 * _updateConnector() method update configuration of connector
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _updateConnector(array $aPost)
	{
		// set
		$aAssign = array();

		// clean
		@ob_end_clean();

		// use case - check connector ID
		if (!isset($aPost['iConnectorId'])
			|| (isset($aPost['iConnectorId'])
			&& !array_key_exists($aPost['iConnectorId'], $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']))
		) {
			throw new Exception('Connector ID isn\'t valid', 160);
		}

		// set
		$aData = array();

		// use case - active elt
		if (!empty($aPost['activeConnector'])) {
			$aData['activeConnector'] = strip_tags($aPost['activeConnector']);
		}
		// use case - app ID elt
		if (!empty($aPost['id'])) {
			$aData['id'] = strip_tags($aPost['id']);
		}
		// use case - secret elt
		if (!empty($aPost['secret'])) {
			$aData['secret'] = strip_tags($aPost['secret']);
		}
		// use case - callback elt
		if (!empty($aPost['callback'])) {
			$aData['callback'] = strip_tags($aPost['callback']);
		}
		// use case - scope elt
		if (!empty($aPost['scope'])) {
			$aData['scope'] = strip_tags($aPost['scope']);
		}
		// use case - permissions elt
		if (isset($aPost['permissions'])) {
			$aData['permissions'] = $aPost['permissions'] == 'on' ? true : false;
		}
		// use case - developer key elt
		if (!empty($aPost['developerKey'])) {
			$aData['developerKey'] = strip_tags($aPost['developerKey']);
		}
		// use case - permissions elt
		if (!empty($aPost['permissions'])) {
			$aData['permissions'] = strip_tags($aPost['permissions']);
		}

		// use case - display elt
		if (!empty($aPost['display'])) {
			$aData['display'] = strip_tags($aPost['display']);
		}

		// update connector data
		if (!Configuration::updateValue(_FPC_MODULE_NAME . '_' . strtoupper($aPost['iConnectorId']), serialize($aData))) {
			throw new Exception('Update data of connector "' . $aPost['iConnectorId'] . '" doesn\'t work', 168);
		}

		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_CONFIRM, 'assign' => $aAssign)
		);
	}


	/**
	 * _updateHook() method update hook
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _updateHook(array $aPost)
	{
		// clean headers
		@ob_end_clean();

		// set
		$aAssign = array();

		// use case - check hook ID
		if (!isset($aPost['sHookId'])
			|| (isset($aPost['sHookId'])
			&& !array_key_exists($aPost['sHookId'], $GLOBALS[_FPC_MODULE_NAME . '_ZONE']))
		) {
			throw new Exception('Hook ID isn\'t valid', 170);
		}
		// use case - check connector list
		elseif (!isset($aPost['sConnectorList'])) {
			throw new Exception('Connector list isn\'t valid', 171);
		}
		else {
			// set variable
			$aTmpConnector = array();

			if (!empty($aPost['sConnectorList']))
			{
				// get list of added connector to the hook
				$aConnectorList = explode(',', $aPost['sConnectorList']);

				// loop on each connector in order to verify id
				foreach ($aConnectorList as $sId) {
					if (!array_key_exists($sId, $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'])) {
						throw new Exception('"' . $sId . '"' . FacebookPsConnect::$oModule->l('Connector isn\'t valid'), 172);
					}
					else {
						// get data by connector
						$aTmpConnector[$sId] = $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'][$sId]['title'];
					}
				}
			}
			// update hook data
			if (!Configuration::updateValue(_FPC_MODULE_NAME . '_' . strtoupper($aPost['sHookId']), serialize($aTmpConnector))) {
				throw new Exception('Update data of hook "' . $aPost['sHookId'] . '" doesn\'t work', 173);
			}
			unset($aTmpConnector);
			unset($aConnectorList);
		}
		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_CONFIRM, 'assign' => $aAssign)
		);
	}


	/**
	 * create() method set singleton
	 * 
	 * @category admin collection
	 * @param
	 * @return obj
	 */
	public static function create()
	{
		static $oUpdate;

		if (null === $oUpdate) {
			$oUpdate = new BT_AdminUpdate();
		}
		return $oUpdate;
	}
}