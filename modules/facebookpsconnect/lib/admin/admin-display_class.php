<?php
/**
 * admin-display_class.php file defines method to display content tabs of admin page
 */


class BT_AdminDisplay implements BT_IAdmin
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
	 * run() method display all configured data admin tabs
	 * @param array $aParam
	 * @return array
	 */
	public function run(array $aParam = null)
	{
		// set variables
		$aDisplayInfo = array();

		// get type
		$aParam['sType'] = empty($aParam['sType'])? 'tabs' : $aParam['sType'];

		switch ($aParam['sType']) {
			case 'tabs' :               // use case - display first page with all tabs
			case 'basic' :              // use case - display basic settings page
			case 'connectors' :         // use case - display connector settings page
			case 'connectorForm' :      // use case - display connector form
			case 'hooks' :              // use case - display hook settings page
			case 'hookForm' :           // use case - display hook form
			case 'curlssl' :            // use case - display curl ssl
			case 'SystemHealth' :       // use case - display system health settings page
				// execute match function
			$aDisplayInfo = call_user_func_array(array($this, '_display' . ucfirst($aParam['sType'])), array($aParam));
				break;
			default :
				break;
		}
		// use case - generic assign
		if (!empty($aDisplayInfo)) {
			$aDisplayInfo['assign'] = array_merge($aDisplayInfo['assign'], $this->_assign());
		}

		return $aDisplayInfo;
	}

	/**
	 * _assign() method assigns transverse data
	 *
	 * @return array
	 */
	private function _assign()
	{
		$bVersion15_16 = false;

		if (version_compare(_PS_VERSION_, '1.6', '>')) {
			$bVersion15_16 = true;
		}

		// set smarty variables
		return (
			array (
				'sURI' 			    => BT_FPCModuleTools::truncateUri(array('&iPage', '&sAction')),
				'aQueryParams' 	    => $GLOBALS[_FPC_MODULE_NAME . '_REQUEST_PARAMS'],
				'iDefaultLang' 	    => intval(FacebookPsConnect::$iCurrentLang),
				'sDefaultLang' 	    => FacebookPsConnect::$sCurrentLang,
				'sHeaderInclude'    => BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_HEADER),
				'sErrorInclude'     => BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_ERROR),
				'sConfirmInclude'   => BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_CONFIRM),
				'bVerion15_16'      => $bVersion15_16,
				'bTwitterActif'     => (!empty($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['twitter']['data']['activeConnector'])) ? 1 : 2,
				'iApiRequestMethod' => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_API_REQUEST_METHOD'],
			)
		);
	}

	/**
	 * _displayTabs() method displays admin's first page with all tabs
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displayTabs(array $aPost)
	{
		// set smarty variables
		$aAssign = array(
			'sDocUri'           => _MODULE_DIR_ . _FPC_MODULE_SET_NAME . '/',
			'sDocName'          => 'readme_' . ((FacebookPsConnect::$sCurrentLang == 'fr')? 'fr' : 'en') . '.pdf',
			'sCurrentIso'		=> Language::getIsoById(FacebookPsConnect::$iCurrentLang),
			'sContactUs'        => 'http://www.businesstech.fr/' . ((FacebookPsConnect::$sCurrentLang == 'fr')? 'fr/contactez-nous' : 'en/contact-us'),
			'sTs'				=> time(),
			'bAddJsCss'		    => true,
			'bHideConfiguration'=> BT_FPCWarning::create()->bStopExecution,
		);

		// use case - get display data of basic settings
		$aData = $this->_displayBasic($aPost);

		$aAssign = array_merge($aAssign, $aData['assign']);

		// use case - get display data of connector settings
		$aData = $this->_displayConnectors($aPost);

		$aAssign = array_merge($aAssign, $aData['assign']);

		// use case - get display data of hook settings
		$aData = $this->_displayHooks($aPost);

		$aAssign = array_merge($aAssign, $aData['assign']);

		// use case - get display data of stats settings
		$aData = $this->_displaySystemHealth($aPost);

		$aAssign = array_merge($aAssign, $aData['assign']);

		// use case - get display data of stats settings
		$aData = $this->_displayPrerequisitesCheck($aPost);

		$aAssign = array_merge($aAssign, $aData['assign']);

		// assign all included templates files
		$aAssign['sBasicsInclude']      = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_BASIC_SETTINGS);
		$aAssign['sConnectorInclude']   = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_CONNECTOR_SETTINGS);
		$aAssign['sHookInclude']        = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_HOOK_SETTINGS);
		$aAssign['sSystemHealthInclude']= BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_SYS_HEALTH_SETTINGS);
		$aAssign['sPrerequisitesCheck'] = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_PREREQUISITES_CHECK_SETTINGS);
		$aAssign['iTestSsl'] = FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_SSL_TEST_TODO'];
		$aAssign['iCurlSslCheck'] = FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_TEST_CURl_SSL'];

		// set css and js use
		$GLOBALS[_FPC_MODULE_NAME . '_USE_JS_CSS']['bUseJqueryUI'] = true;

		return (
			array(
				'tpl'		=> _FPC_TPL_ADMIN_PATH . _FPC_TPL_BODY,
				'assign'	=> array_merge($aAssign, $GLOBALS[_FPC_MODULE_NAME . '_USE_JS_CSS']),
			)
		);
	}

	/**
	 * _displayBasic() method displays snippets settings
	 * 
	 * @category admin collection
	 * @see
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displayBasic(array $aPost)
	{
		$aAssign = array();

		if (FacebookPsConnect::$sQueryMode == 'xhr') {
			// clean header
			@ob_end_clean();
		}

		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$aAssign['bVerion15_16'] = true;
		}

		// set smarty variables
		$aAssign = array(
			'bDisplayAskFacebook'   => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_FB_POPIN'],
			'bDisplayBlock'         => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_BLOCK'],
			'bDisplayBlockInfoAccount' => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_ACCOUNT'],
			'bDisplayBlockInfoCart' => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_CART'],
			'bOnePageCheckOut'      => Configuration::get('PS_ORDER_PROCESS_TYPE'),
			'iDefaultCustomerGroup' => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DEFAULT_CUSTOMER_GROUP'],
			'sApiRequestType'       => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_API_REQUEST_METHOD'],
			'aApiCallMethod'        => array(array('type' => 'fopen', 'name' => FacebookPsConnect::$oModule->l('Native PHP file_get_contents', 'admin-display_class'),'active' => ini_get('allow_url_fopen')), array('type' => 'curl',  'name' => FacebookPsConnect::$oModule->l('PHP cURL library mode', 'admin-display_class'), 'active' => function_exists('curl_init'))),
		);

		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$aAssign['aGroups'] = Group::getGroups(FacebookPsConnect::$iCurrentLang, FacebookPsConnect::$iShopId);
		}
		else {
			$aAssign['aGroups'] = Group::getGroups(FacebookPsConnect::$iCurrentLang);
		}

		return (
			array(
				'tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_BASIC_SETTINGS, 'assign' => $aAssign,
			)
		);
	}

	/**
	 * _displayConnectors() method displays connectors list
	 *
	 * @category admin collection
	 * @see
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displayConnectors(array $aPost)
	{
		// set
		$aAssign = array();

		if (FacebookPsConnect::$sQueryMode == 'xhr') {
			// clean header
			@ob_end_clean();
		}

		// unserialize connector data
		BT_FPCModuleTools::getConnectorData(true);

		// set smarty variables
		$aAssign = array(
			'aConnectors' 	    => $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'],
			'iDefaultLang' 	    => intval(FacebookPsConnect::$iCurrentLang),
			'aDefaultLang' 	    => Language::getLanguage(intval(FacebookPsConnect::$iCurrentLang)),
			'bVersion15_16'     => (version_compare(_PS_VERSION_, '1.5', '>')? true : false),

		);

		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_CONNECTOR_SETTINGS, 'assign' => $aAssign)
		);
	}


	/**
	 * _displayConnectorForm() method displays connector form
	 *
	 * @category admin collection
	 * @see Language::getLanguage()
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displayConnectorForm(array $aPost)
	{
		// set
		$aAssign = array();

		// clean header
		@ob_end_clean();

		// get connector id
		$iConnectorId = Tools::getValue('iConnectorId');

		// use case - only configure with good connector id
		if ($iConnectorId && array_key_exists($iConnectorId, $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'])) {
			// get unserialized connector data
			BT_FPCModuleTools::unserializeData($iConnectorId, 'connector');

			// set smarty variables
			$aAssign = array(
				'iConnectorId'      => $iConnectorId,
				'iDefaultLang' 	    => intval(FacebookPsConnect::$iCurrentLang),
				'aDefaultLang' 	    => Language::getLanguage(intval(FacebookPsConnect::$iCurrentLang)),
				'aConnector' 	    => $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'][$iConnectorId],
				'sCbkUri'           => BT_FPCModuleTools::detectHttpUri(_FPC_MODULE_URL . $iConnectorId . '-callback.php', Configuration::get('PS_SHOP_DOMAIN')),
				'iTestCurlSsl'      => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_TEST_CURl_SSL'],
				'iApiRequestMethod' => FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_API_REQUEST_METHOD'],
			);

			// set tpl to include
			$aAssign['sTplToInclude'] = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_ADMIN_PATH . _FPC_TPL_CONNECTOR_PATH . $aAssign['aConnector']['adminTpl']);

			// clean footer
			FacebookPsConnect::$sQueryMode = 'xhr';
		}

		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_CONNECTOR_BODY, 'assign' => $aAssign)
		);
	}

	/**
	 * _displayHooks() method displays hooks list
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displayHooks(array $aPost)
	{
		if (FacebookPsConnect::$sQueryMode == 'xhr') {
			// clean header
			@ob_end_clean();
		}

		$bVersion15_16 = false;
		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$bVersion15_16 = true;
		}

		// unserialize hook data
		BT_FPCModuleTools::getHookData();

		// set smarty variables
		$aAssign = array(
			'aHooks' 	        => $GLOBALS[_FPC_MODULE_NAME . '_ZONE'],
			'iDefaultLang' 	    => intval(FacebookPsConnect::$iCurrentLang),
			'bVersion15_16'       => $bVersion15_16,
		);

		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_HOOK_SETTINGS, 'assign' => $aAssign)
		);
	}

	/**
	 * _displayHookForm() method displays hook form
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displayHookForm(array $aPost)
	{
		// set
		$aAssign = array();

		// clean header
		@ob_end_clean();

		// get hook id
		$sHookId = Tools::getValue('sHookId');

		// use case - only configure with good connector id
		if ($sHookId && array_key_exists($sHookId, $GLOBALS[_FPC_MODULE_NAME . '_ZONE'])) {
			// get unserialized connector data
			BT_FPCModuleTools::unserializeData($sHookId, 'hook');

			// set smarty variables
			$aAssign = array(
				'sHookId'           => $sHookId,
				'iDefaultLang' 	    => intval(FacebookPsConnect::$iCurrentLang),
				'bOneSet'           => BT_FPCModuleTools::getConnectorData(false, true),
				'aHook' 	        => $GLOBALS[_FPC_MODULE_NAME . '_ZONE'][$sHookId],
				'aConnectors'       => $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'],
			);

			// set current widget
			$aAssign['aHook'] = $GLOBALS[_FPC_MODULE_NAME . '_ZONE'][$sHookId];

			// clean footer
			FacebookPsConnect::$sQueryMode = 'xhr';
		}

		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_HOOK_FORM, 'assign' => $aAssign)
		);
	}

	/**
	 * _displaySystemHealth() method displays system health information
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displaySystemHealth(array $aPost)
	{
		$aAssign = array();
		$aAssign['iCurrentLang'] = intval(FacebookPsConnect::$iCurrentLang);

		// set
		$sIsoCode = FacebookPsConnect::$sCurrentLang;

		if($sIsoCode !== 'fr') {
			$sIsoCode = 'en';
		}

		$aModules = array(
			'facebookpsshoptab' => array(
				'active'    => true,
				'min'       => '3.3.2',
				'name'      => 'Facebook Ps Shop Tab',
				'img'       => _FPC_URL_IMG . 'admin/fb-ps-shop-tab.jpg',
				'addons'    => 'http://addons.prestashop.com/'.$sIsoCode.'/social-commerce-facebook-prestashop-modules/1048-facebook-ps-shop-tab.html'
			),
			'facebookpsessentials' => array(
				'active'    => true,
				'min'       => '2.3.0',
				'name'      => 'Facebook Ps Essentials',
				'img'       => _FPC_URL_IMG . 'admin/fb-ps-essentials.jpg',
				'addons'    => 'http://addons.prestashop.com/'.$sIsoCode.'/social-commerce-facebook-prestashop-modules/5025-facebook-ps-essentials-facebook-like-twitter-etc.html'
			),
		);

		unset($sIsoCode);

		foreach ($aModules as $sName => $aModule) {
			$aParams = $aModule;

			if (($oModule = BT_FPCModuleTools::isInstalled($sName, array(), true)) !== false) {
				// installed ok + min version
				$aParams['installed'] = true;
				$aParams['minVersion'] = version_compare($oModule->version, $aModule['min'], '>=')? true : false;
			}
			else {
				$aParams['installed'] = false;
			}
			$aAssign['aModules'][$sName] = $aParams;
		}


		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_SYS_HEALTH_SETTINGS, 'assign' => $aAssign)
		);
	}

	/**
	 * _displayCurlSsl() method displays result test Curl with Ssl
	 *  Set Global for Test the result Curl SSL
	 *
	 * @category admin collection
	 * @see
	 *
	 * @param array $aPost
	 * @return array
	 */
	private function _displayCurlSsl(array $aPost)
	{
		//set
		$aAssign = array();

		//clean header
		@ob_end_clean();

		//init curl connexion
		$ch = curl_init('https://google.fr');

		//transfer test
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// exec curl
		curl_exec($ch);

		// error test and set error message
		if (curl_errno($ch) == 1)
		{
			$aAssign['iCurlSslCheck'] = false;
			Configuration::updateValue(_FPC_MODULE_NAME . '_TEST_CURl_SSL', 1);
		}
		else
		{
			$aAssign['iCurlSslCheck'] = true;
			Configuration::updateValue(_FPC_MODULE_NAME . '_TEST_CURl_SSL', 2);
			Configuration::updateValue(_FPC_MODULE_NAME . '_SSL_TEST_TODO', 0);
		}

		//close curl connexion
		curl_close($ch);

		//clean footer
		FacebookPsConnect::$sQueryMode = 'xhr';

		return(
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_CURL_SSL, 'assign' => $aAssign)
		);
	}

	/**
	 * _displayPrerequisitesCheck() method displays prerequisites check
	 *
	 * @param array $aPost
	 * @return array
	 */

	private function _displayPrerequisitesCheck(array $aPost)
	{
		$aAssign = array(
			'sValidImgUrl'      => _FPC_URL_IMG . 'admin/icon-valid.png',
			'sInvalidImgUrl'    => _FPC_URL_IMG . 'admin/icon-invalid.png',
			'sCheckCurlInit'    => BT_FPCWarning::create()->run('function', 'curl_init'),
			'sCheckAllowUrl'    => BT_FPCWarning::create()->run('directive', 'allow_url_fopen'),
			'sCheckGroup'       => BT_FPCWarning::create()->run('configuration', '_DEFAULT_CUSTOMER_GROUP'),
		);

		return (
			array('tpl' => _FPC_TPL_ADMIN_PATH . _FPC_TPL_PREREQUISITES_CHECK_SETTINGS, 'assign' => $aAssign)
		);
	}

	/**
	 * create() method set singleton
	 *
	 * @return obj
	 */
	public static function create()
	{
		static $oDisplay;

		if ( null === $oDisplay) {
			$oDisplay = new BT_AdminDisplay();
		}
		return $oDisplay;
	}
}