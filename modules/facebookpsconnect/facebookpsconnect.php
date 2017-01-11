<?php
/**
 * facebookpsconnect.php file defines main class of module
 *
 * @author Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
 * @version 2.0.3
 * @uses    Please read included installation and configuration instructions (PDF format)
 * @see     lib/install
 *          => i-install.php => interface
 *          => install-ctrl_class.php => controller, manage factory with config or sql install object
 *          => install.config classes => manage install / uninstall of config values (register hook)
 *      lib/admin
 *          => i-admin.php => interface
 *          => admin-ctrl_class.php => controller, manage factory with configure or update admin object
 *          => display and update admin classes => manage display of admin form and make action of updating config like (add, edit, delete, update, ... see PHP Doc in class)
 *      lib/hook
 *          => i-hook_class.php => interface
 *          => hook-ctrl_class.php => controller, manage strategy with hook object. Like this, you can add hook easily with declare a new file class
 *          => hook-home_class.php => manage displaying content on your home page
 *      lib/module-dao_class.php
 *          D A O = Data Access Object => manage all sql queries
 *      lib/module-tools_class.php
 *          declare all transverse functions which are unclassifiable in specific class
 *      lib/warnings_class.php
 *          manage all displaying warnings when module isn't already configured after installation
 * @date    09/11/2015
 */

if (!defined('_PS_VERSION_')) {
	exit(1);
}

class FacebookPsConnect extends Module
{
	/**
	 * @var array $aConfiguration : array of set configuration
	 */
	public static $aConfiguration = array();

	/**
	 * @var int $iCurrentLang : store id of default lang
	 */
	public static $iCurrentLang = null;

	/**
	 * @var int $sCurrentLang : store iso of default lang
	 */
	public static $sCurrentLang = null;

	/**
	 * @var obj $oCookie : store cookie obj
	 */
	public static $oCookie = null;

	/**
	 * @var obj $oModule : obj module itself
	 */
	public static $oModule = array();

	/**
	 * @var string $sQueryMode : query mode - detect XHR
	 */
	public static $sQueryMode = null;

	/**
	 * @var string $sBASE_URI : base of URI in prestashop
	 */
	public static $sBASE_URI = null;

	/**
	 * @var array $aErrors : array get error
	 */
	public $aErrors = null;

	/**
	 * @var int $iShopId : shop id used for 1.5 and for multi shop
	 */
	public static $iShopId = 1;

	/**
	 * Magic Method __construct assigns few information about module and instantiate parent class
	 * @author Business Tech (www.businesstech.fr) - Contact: http://www.businesstech.fr/en/contact-us
	 */
	public function __construct()
	{
		// hack for older version than 1 4 5 1
		if (is_file(dirname(__FILE__) . '/conf/common.conf.php')) {
			require_once(dirname(__FILE__) . '/conf/common.conf.php');
		}
		else {
			require_once(_PS_MODULE_DIR_ . 'facebookpsconnect/conf/common.conf.php');
		}

		require_once(_FPC_PATH_LIB . 'warning_class.php');
		require_once(_FPC_PATH_LIB . 'module-tools_class.php');

		// use case - get context
		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			self::$iShopId = Context::getContext()->shop->id;
		}

		// get current lang
		self::$iCurrentLang = BT_FPCModuleTools::getCookieObj()->id_lang;
		// get current iso lang
		self::$sCurrentLang = BT_FPCModuleTools::getLangIso();
		// get cookie obj
		self::$oCookie = BT_FPCModuleTools::getCookieObj();

		$this->name = 'facebookpsconnect';
		$this->module_key = 'ffcbc0b08d66e0afb7ed1ed27e0f1492';
		$this->tab = 'social_networks';
		$this->version = '2.0.3';
		$this->author = 'Business Tech';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName      = $this->l('Facebook Connect');
		$this->description      = $this->l('Let your customer easily log in via Facebook, Paypal, Amazon, Google or Twitter');
		$this->confirmUninstall = $this->l('Are you sure you want to remove it ? Your Facebook PS Connect will no longer work. Be careful, all your configuration and your data will be lost');
		
		// stock itself obj
		self::$oModule = $this;

		// update module version
		$GLOBALS[_FPC_MODULE_NAME . '_CONFIGURATION'][_FPC_MODULE_NAME . '_MODULE_VERSION'] = $this->version;

		// set base of URI
		self::$sBASE_URI = $this->_path;

		// set title of hooks & connectors
		$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['header']['title'] = $this->l('Header');
		$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['top']['title'] = $this->l('Top');
		$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['account']['title'] = $this->l('Customer account');
		$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['left']['title'] = $this->l('Left Column');
		$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['right']['title'] = $this->l('Right Column');
		$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['footer']['title'] = $this->l('Footer');
		$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['authentication']['title'] = $this->l('Authentication page');
		$GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['facebook']['title'] = $this->l('Facebook sign in');
		$GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['twitter']['title'] = $this->l('Twitter sign in');
		$GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['google']['title'] = $this->l('Google sign in');
		$GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['paypal']['title'] = $this->l('Paypal sign in');
		$GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['amazon']['title'] = $this->l('Amazon sign in');


		if (version_compare(_PS_VERSION_,'1.6.0','<')) {
			$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['right']['title'] = $this->l('Right Column');
			$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['blockUser']['title'] = $this->l('Block Info User');
		}

		// get configuration options
		BT_FPCModuleTools::getConfiguration();

		// get call mode - Ajax or dynamic - used for clean headers and footer in ajax request
		self::$sQueryMode = Tools::getValue('sMode');
	}

	/**
	 * install() method installs all mandatory structure (DB or Files) => sql queries and update values and hooks registered
	 *
	 * @return bool
	 */
	public function install()
	{
		require_once(_FPC_PATH_CONF . 'install.conf.php');
		require_once(_FPC_PATH_LIB_INSTALL . 'install-ctrl_class.php');

		// set return
		$bReturn = true;

		if (!parent::install()
			|| !BT_InstallCtrl::run('install', 'sql', _FPC_PATH_SQL . _FPC_INSTALL_SQL_FILE)
			|| !BT_InstallCtrl::run('install', 'config')
		) {
			$bReturn = false;
		}

		return $bReturn;
	}

	/**
	 * uninstall() method uninstalls all mandatory structure (DB or Files)
	 *
	 * @return bool
	 */
	public function uninstall()
	{
		require_once(_FPC_PATH_CONF . 'install.conf.php');
		require_once(_FPC_PATH_LIB_INSTALL . 'install-ctrl_class.php');

		// set return
		$bReturn = true;

		if (!parent::uninstall()
			|| !BT_InstallCtrl::run('uninstall', 'sql', _FPC_PATH_SQL . _FPC_UNINSTALL_SQL_FILE)
			|| !BT_InstallCtrl::run('uninstall', 'config')
		) {
			$bReturn = false;
		}

		return $bReturn;
	}

	/**
	 * getContent() method manages all data in Back Office
	 *
	 * @return string
	 */
	public function getContent()
	{
		require_once(_FPC_PATH_CONF . 'admin.conf.php');
		require_once(_FPC_PATH_LIB_ADMIN . 'admin-ctrl_class.php');

		// set
		$aUpdateModule = array();

		try {
			// update new module keys
			BT_FPCModuleTools::updateConfiguration();

			// get configuration options
			BT_FPCModuleTools::getConfiguration();

			// set js msg translation
			BT_FPCModuleTools::translateJsMsg();

			// instantiate admin controller object
			$oAdmin = new BT_AdminCtrl();

			// defines type to execute
			// use case : no key sAction sent in POST mode (no form has been posted => first page is displayed with admin-display.class.php)
			// use case : key sAction sent in POST mode (form or ajax query posted ).
			$sAction = (!Tools::getIsset('sAction') || (Tools::getIsset('sAction') && 'display' == Tools::getValue('sAction')))? (Tools::getIsset('sAction')? Tools::getValue('sAction') : 'display') : Tools::getValue('sAction');

			// make module update only in case of display general admin page
			if ($sAction == 'display' && !Tools::getIsset('sType')) {
				// update module if necessary
				$aUpdateModule = $this->_updateModule();
			}


			// execute good action in admin
			// only displayed with key : tpl and assign in order to display good smarty template
			$aDisplay = $oAdmin->run($sAction, array_merge($_GET, $_POST));

			// free memory
			unset($oAdmin);

			if (!empty($aDisplay)) {
				$aDisplay['assign'] = array_merge($aDisplay['assign'], array('aUpdateErrors' => $aUpdateModule, 'oJsTranslatedMsg' => BT_FPCModuleTools::jsonEncode($GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']), 'bAddJsCss' => true));

				// get content
				$sContent = $this->displayModule($aDisplay['tpl'], $aDisplay['assign']);

				if (!empty(self::$sQueryMode)) {
					echo $sContent;
				}
				else {
					return $sContent;
				}
			}
			else {
				throw new Exception('action returns empty content', 110);
			}
		}
		catch (Exception $e) {
			$this->aErrors[] = array('msg' => $e->getMessage(), 'code' => $e->getCode());

			// get content
			$sContent = $this->displayErrorModule();

			if (!empty(self::$sQueryMode)) {
				echo $sContent;
			}
			else {
				return $sContent;
			}
		}
		// exit clean with XHR mode
		if( !empty(self::$sQueryMode)) {
			exit(0);
		}
	}

	/**
	 * hookHeader() method displays customized module content on header
	 *
	 * @return string
	 */
	public function hookHeader()
	{
		return (
			$this->_execHook('display', 'header')
		);
	}

	/**
	 * hookDisplayHeader() method displays customized module content on header
	 *
	 * @return string
	 */
	public function hookDisplayHeader()
	{
		return (
			$this->_execHook('display', 'header')
		);
	}


	/**
	 * hookTop() method displays customized module content on top
	 *
	 * @return string
	 */
	public function hookTop()
	{
		return (
			$this->_execHook('display', 'top')
		);
	}


	/**
	 * hookDisplayTop() method displays customized module content on top
	 *
	 * @return string
	 */
	public function hookDisplayTop()
	{
		return (
			$this->_execHook('display', 'top')
		);
	}

	/**
	 * hookDisplayLeftColumn() method displays snippets for product page on left column
	 *
	 * @return string
	 */
	public function hookDisplayLeftColumn()
	{
		return (
			$this->_execHook('display', 'left')
		);
	}

	/**
	 * hookLeftColumn() method displays snippets for product page on left column
	 *
	 * @return string
	 */
	public function hookLeftColumn()
	{
		return (
			$this->_execHook('display', 'left')
		);
	}


	/**
	 * hookDisplayRightColumn() method displays snippets for product page on right column
	 *
	 * @return string
	 */
	public function hookDisplayRightColumn()
	{
		return (
			$this->_execHook('display', 'right')
		);
	}

	/**
	 * hookRightColumn() method displays snippets for product page on right column
	 *
	 * @return string
	 */
	public function hookRightColumn()
	{
		return (
			$this->_execHook('display', 'right')
		);
	}


	/**
	 * hookFooter() method displays customized module content on footer
	 *
	 * @return string
	 */
	public function hookFooter()
	{
		return (
			$this->_execHook('display', 'footer')
		);
	}

	/**
	 * hookDisplayFooter() method displays customized module content on footer
	 *
	 * @return string
	 */
	public function hookDisplayFooter()
	{
		return (
			$this->_execHook('display', 'footer')
		);
	}


	/**
	 * hookDisplayCustomerAccount() method displays PS customer account
	 *
	 * @return string
	 */
	public function hookDisplayCustomerAccount()
	{
		return (
			$this->_execHook('display', 'account')
		);
	}


	/**
	 * hookCustomerAccount() method displays PS customer account
	 *
	 * @return string
	 */
	public function hookCustomerAccount()
	{
		return (
			$this->_execHook('display', 'account')
		);
	}


	/**
	 * displayCustomerAccountForm() method displays PS customer account form
	 *
	 * @return string
	 */
	public function displayCustomerAccountForm()
	{
		return (
			$this->_execHook('display', 'authentication')
		);
	}


	/**
	 * createAccountForm() method displays PS customer account form
	 *
	 * @return string
	 */
	public function createAccountForm()
	{
		return (
			$this->_execHook('display', 'authentication')
		);
	}


	/**
	 * HookConnectorCallback() method exec social callback
	 *
	 * @param array array
	 * @return string
	 */
	public function HookConnectorCallback(array $aParams)
	{
		return (
			$this->_execHook('action', 'callback', $aParams)
		);
	}

	/**
	 * HookConnectorCallback() method connect social connector
	 *
	 * @param array array
	 * @return string
	 */
	public function HookConnectorConnect(array $aParams)
	{
		return (
			$this->_execHook('action', 'connect', $aParams)
		);
	}

	/**
	 * HookCustomerAssociation() method update customer facebook association preferences
	 *
	 * @param array array
	 * @return string
	 */
	public function HookCustomerAssociation(array $aParams)
	{
		return (
			$this->_execHook('action', 'updateCustomer', $aParams)
		);
	}

	/**
	 * HookCustomerEmail() method update customer e-mail
	 *
	 * @param array array
	 * @return string
	 */
	public function HookCustomerEmail(array $aParams)
	{
		return (
			$this->_execHook('action', 'updateEmail', $aParams)
		);
	}

	/**
	 * HookSocialCollector() method connect social connector
	 *
	 * @param array array
	 * @return string
	 */
	public function HookSocialCollector($sType, array $aParams)
	{
		if (array_key_exists($sType, $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'])) {
			return (
				$this->_execHook('action', 'collect', array_merge(array('type' => $sType), $aParams))
			);
		}
	}

	/**
	 * _execHook() method displays selected hook content
	 *
	 * @param string $sHookType
	 * @param array $aParams
	 * @return string
	 */
	private function _execHook($sHookType, $sAction,  array $aParams = array())
	{
		// include
		require_once(_FPC_PATH_CONF . 'hook.conf.php');
		require_once(_FPC_PATH_LIB_HOOK . 'hook-ctrl_class.php');

		try {
			// define which hook class is executed in order to display good content in good zone in shop
			$oHook = new BT_FPCHookCtrl($sHookType, $sAction);

			// displays good block content
			$aDisplay = $oHook->run($aParams);

			// free memory
			unset($oHook);

			//execute good action in admin
			//only displayed with key : tpl and assign in order to display good smarty template
			if (!empty($aDisplay)) {
				return (
					$this->displayModule($aDisplay['tpl'], $aDisplay['assign'])
				);
			}
			else {
				throw new Exception('Choosen hook returns empty content', 110);
			}
		}
		catch (Exception $e) {
			$this->aErrors[] = array('msg' => $e->getMessage(), 'code' => $e->getCode());

			return (
				$this->displayErrorModule()
			);
		}
	}


	/**
	 * setErrorHandler() method manages module error
	 */
	public function setErrorHandler($iErrno, $sErrstr, $sErrFile, $iErrLine, $aErrContext)
	{
		switch ($iErrno) {
			case E_USER_ERROR :
				$this->aErrors[] = array('msg' => 'Fatal error <b>' . $sErrstr . '</b>', 'code' => $iErrno, 'file' => $sErrFile, 'line' => $iErrLine, 'context' => $aErrContext);
				break;
			case E_USER_WARNING :
				$this->aErrors[] = array('msg' => 'Warning <b>' . $sErrstr . '</b>', 'code' => $iErrno, 'file' => $sErrFile, 'line' => $iErrLine, 'context' => $aErrContext);
				break;
			case E_USER_NOTICE :
				$this->aErrors[] = array('msg' => 'Notice <b>' . $sErrstr . '</b>', 'code' => $iErrno, 'file' => $sErrFile, 'line' => $iErrLine, 'context' => $aErrContext);
				break;
			default :
				$this->aErrors[] = array('msg' => 'Unknow error <b>' . $sErrstr . '</b>', 'code' => $iErrno, 'file' => $sErrFile, 'line' => $iErrLine, 'context' => $aErrContext);
				break;
		}
		return (
			$this->displayErrorModule()
		);
	}

	/**
	 * displayModule() method displays view
	 *
	 * @param string $sTplName
	 * @param array $aAssign
	 * @return string html
	 */
	public function displayModule($sTplName, $aAssign)
	{
		if (file_exists(_FPC_PATH_TPL . $sTplName) && is_file(_FPC_PATH_TPL . $sTplName)) {
			if (version_compare(_PS_VERSION_, '1.5', '>')) {
				$smarty = Context::getContext()->smarty;
			}
			else {
				global $smarty;
			}

			// set assign module name
			$aAssign = array_merge($aAssign, array('sModuleName' => Tools::strtolower(_FPC_MODULE_NAME), 'bDebug' => _FPC_DEBUG, 'iCompare' => version_compare(_PS_VERSION_, '1.4.1')));

			$smarty->assign($aAssign);

			return (
				$this->display(__FILE__, _FPC_PATH_TPL_NAME . $sTplName)
			);
		}
		else {
			throw new Exception('Template "' . $sTplName . '" doesn\'t exists', 120);
		}
	}

	/**
	 * displayErrorModule() method displays view with error
	 *
	 * @param string $sTplName
	 * @param array $aAssign
	 * @return string html
	 */
	public function displayErrorModule()
	{
		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			$smarty = Context::getContext()->smarty;
		}
		else {
			global $smarty;
		}

		$smarty->assign(
			array(
				'sHomeURI'      => BT_FPCModuleTools::truncateUri(),
				'aErrors'       => $this->aErrors,
				'sModuleName'   => Tools::strtolower(_FPC_MODULE_NAME),
				'bDebug'        => _FPC_DEBUG,
			)
		);

		return (
			$this->display(__FILE__, _FPC_PATH_TPL_NAME . _FPC_TPL_COMMON_PATH . _FPC_TPL_ERROR)
		);
	}

	/**
	 * _updateModule() method updates module as necessary
	 *
	 * @return array
	 */
	private function _updateModule()
	{
		require(_FPC_PATH_LIB . 'module-update_class.php');

		// check if update tables
		BT_FpcModuleUpdate::create()->run(array('sType' => 'tables'));

		// check if update fields
		BT_FpcModuleUpdate::create()->run(array('sType' => 'fields'));

		// check if update hooks
		BT_FpcModuleUpdate::create()->run(array('sType' => 'hooks'));

		// check if update templates
		BT_FpcModuleUpdate::create()->run(array('sType' => 'templates'));

		return BT_FpcModuleUpdate::create()->aErrors;
	}
}