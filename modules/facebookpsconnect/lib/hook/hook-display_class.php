<?php
/**
 * hook-display_class.php file defines controller which manage hooks sequentially
 */

class BT_FPCHookDisplay implements BT_IFpcHook
{
	/**
	 * @var bool $bProcessHookAndConnector : detect if hook and connectors have been processed
	 */
	static protected $bProcessHookAndConnector = null;

	/**
	 * @var bool $bConnectorsActive : detect if one connector is active at least
	 */
	static protected $bConnectorsActive = null;

	/**
	 * @var string $sCurrentURI : get current URI
	 */
	static protected $sCurrentURI = '';

	/**
	 * @var string $sModuleURI : get Module web service URI
	 */
	protected $sModuleURI = null;

	/**
	 * @var string $sHookType : define hook type
	 */
	protected $sHookType = null;

	/**
	 * @var int $iCustomerLogged : get customer ID if is logged
	 */
	protected $iCustomerLogged = null;

	/**
	 * Magic Method __construct assigns few information about hook
	 *
	 * @param string
	 */
	public function __construct($sHookType)
	{
		// set hook type
		$this->sHookType = $sHookType;

		$this->iCustomerLogged = BT_FPCModuleTools::getCustomerId();
	}

	/**
	 * Magic Method __destruct
	 */
	public function __destruct()
	{
		unset($this);
	}

	/**
	 * run() method execute hook
	 *
	 * @param array $aParams
	 * @return array
	 */
	public function run(array $aParams = array())
	{
		// set variables
		$aDisplayHook = array();

		// set module URI
		$this->sModuleURI = _FPC_MODULE_URL . 'ws-' . _FPC_MODULE_SET_NAME . '.php';

		// process hooks and connectors
		$this->_processConnectorAndHook();

		// get current URL
		if (empty(self::$sCurrentURI)) {
			self::$sCurrentURI = urlencode($this->_getCurrentUrl());
		}

		switch ($this->sHookType) {
			case 'header' :
				// use case - display in header
				$aDisplayHook = call_user_func(array($this, '_displayHeader'));
				break;
			case 'account' :
				// use case - display in account
				$aDisplayHook = call_user_func(array($this, '_displayAccount'));
				break;
			case 'top' :
				// use case - display connect buttons in top
				$aDisplayHook = call_user_func_array(array($this, '_displayTop'), array($aParams));
				break;
			case 'left' :
			case 'right' :
				// use case - display block connect in  left or right column
				$aDisplayHook = call_user_func_array(array($this, '_displayBlock'), array($aParams));
				break;
			case 'footer' :
				// use case - display connect buttons in footer
				$aDisplayHook = call_user_func_array(array($this, '_displayFooter'), array($aParams));
				break;
			default :
				break;
		}

		// use case - generic assign
		if (!empty($aDisplayHook['assign'])) {
			if (!empty(FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_API_REQUEST_METHOD'])) {
				$aDisplayHook['assign'] = array_merge($aDisplayHook['assign'], $this->_assign());
			}
			else {
				$aDisplayHook['assign'] = array('bDisplay' => false);
			}
		}

		return $aDisplayHook;
	}

	/**
	 * _assign() method assigns transverse data
	 *
	 * @category hook collection
	 * @see
	 *
	 * @return array
	 */
	private function _assign()
	{
		// set smarty variables
		return (
		array(
			'sModuleURI'        => $this->sModuleURI,
			'iCurrentLang'      => intval(FacebookPsConnect::$iCurrentLang),
			'sCurrentLang'      => FacebookPsConnect::$sCurrentLang,
			'bCustomerLogged'   => $this->iCustomerLogged,
			'bHookDisplay'      => true,
			'bVersion16'        => version_compare(_PS_VERSION_, '1.6', '>')? true : false,
			'bVersion15'        => version_compare(_PS_VERSION_, '1.5', '>')? true : false,
		)
		);
	}

	/**
	 * _processConnectorAndHook() method unserialize connector and hook content
	 *
	 * @return array
	 */
	private function _processConnectorAndHook()
	{
		if (self::$bProcessHookAndConnector === null) {
			// unserialize connectors and hooks data
			BT_FPCModuleTools::getHookData();
			self::$bConnectorsActive = BT_FPCModuleTools::getConnectorData(false, true);

			foreach ($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'] as $sName => &$aConnector) {
				$aConnector['tpl'] = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_HOOK_PATH . $aConnector['tpl']);
			}
			self::$bProcessHookAndConnector = true;
		}
	}

	/**
	 * _getCurrentUrl() method returns current URL
	 *
	 * @return array
	 */
	private function _getCurrentUrl()
	{
		$sLink = '';

		// PS 1.5
		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			// check if OPC deactivated and get redirect on authentication page
			if (Tools::getValue('controller') == 'authentication'
				&&	Tools::getIsset('back')
			) {
				$sLink = urldecode(Tools::getValue('back'));

				if ($sLink == 'my-account') {
					$sLink = BT_FPCModuleTools::getAccountPageLink();
				}
			}
			// check if OPC URL
			elseif (
				Tools::getValue('controller') == 'orderopc'
				||	Tools::getValue('controller') == 'order'
			) {
				$sLink = $_SERVER['REQUEST_URI'];
			}
			// my account page
			else {
				$sLink = BT_FPCModuleTools::getAccountPageLink();
			}
		}
		// PS 1.4
		elseif (version_compare(_PS_VERSION_, '1.4', '>')) {
			// check if OPC deactivated and get redirect on authentication page
			if (strstr($_SERVER['SCRIPT_NAME'], 'authentication')
				&&	Tools::getIsset('back')
			) {
				if ($sLink == 'my-account.php') {
					$sLink = BT_FPCModuleTools::getAccountPageLink();
				}
				else {
					$oLink = new Link();

					$sLink = $oLink->getPageLink('order.php');

					unset($oLink);
				}
			}
			// check if OPC URL
			elseif (strstr($_SERVER['SCRIPT_NAME'], 'order-opc')
				||
				strstr($_SERVER['SCRIPT_NAME'], 'order')
			) {
				$sLink = $_SERVER['REQUEST_URI'];
			}
			// my account page
			else {
				$sLink = BT_FPCModuleTools::getAccountPageLink();
			}
		}
		// PS 1.2 & 1.3
		else {
			// check if get redirect on authentication page
			if (strstr($_SERVER['SCRIPT_NAME'], 'authentication')
				||	Tools::getIsset('back')
			) {
				$sLink = Tools::getValue('back');

				// get PS_BASE_URI
				$sLink = Configuration::get('PS_BASE_URI') . $sLink;
			}
			// check if check-out URL
			elseif (strstr($_SERVER['SCRIPT_NAME'], 'order')) {
				$sLink = $_SERVER['REQUEST_URI'];
			}
			// my account page
			else {
				global $smarty;

				$sLink = $smarty->_tpl_vars['base_dir_ssl']  . 'my-account.php';
			}
		}

		return $sLink;
	}

	/**
	 * _displayHeader() method add to header JS and CSS
	 *
	 * @return array
	 */
	private function _displayHeader()
	{
		// set
		$aAssign = array();

		// set js msg translation
		BT_FPCModuleTools::translateJsMsg();

		$aAssign['oJsTranslatedMsg'] = BT_FPCModuleTools::jsonEncode($GLOBALS[_FPC_MODULE_NAME . '_JS_MSG']);

		// old version
		if (version_compare(_PS_VERSION_, '1.4.1', '<')) {
			$aAssign['bAddJsCss'] = true;
		}
		else {
			// use case - get context
			if (version_compare(_PS_VERSION_, '1.5', '>')) {
				// add in minify process by prestahsop
				Context::getContext()->controller->addCSS(_FPC_URL_CSS . 'hook.css');
				Context::getContext()->controller->addJS(_FPC_URL_JS . 'module.js');

				if (Tools::getValue('controller') == 'myaccount') {
					// get fancybox plugin
					$aJsCss = Media::getJqueryPluginPath('fancybox');

					// add fancybox plugin
					if (!empty($aJsCss['js']) && !empty($aJsCss['css'])) {
						Context::getContext()->controller->addCSS($aJsCss['css']);
						Context::getContext()->controller->addJS($aJsCss['js']);
					}
				}
			}
			else {
				// add in minify process by prestahsop
				Tools::addCSS(_FPC_URL_CSS . 'hook.css');
				Tools::addJS(_FPC_URL_JS . 'module.js');

				// add fancybox plugin
				Tools::addCSS(_PS_CSS_DIR_ . 'jquery.fancybox-1.3.4.css');
				Tools::addJS(_PS_JS_DIR_ . 'jquery/jquery.fancybox-1.3.4.js');
			}
			$aAssign['bAddJsCss'] = false;
		}

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_HEADER, 'assign' => $aAssign)
		);
	}

	/**
	 * _displayTop() method displays connector buttons
	 *
	 * @param array $aParams
	 * @return array
	 */
	private function _displayTop(array $aParams)
	{
		// set
		$aAssign = array();

		// get all configured hooks
		if (array_key_exists($this->sHookType, $GLOBALS[_FPC_MODULE_NAME . '_ZONE'])
			&&	false !== $GLOBALS[_FPC_MODULE_NAME . '_ZONE'][$this->sHookType]['data']
			&&	self::$bConnectorsActive
		) {
			$aAssign['bDisplay']                = true;
			$aAssign['sConnectorButtonsIncl']   = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS);
			$aAssign['aHookConnectors']         = $GLOBALS[_FPC_MODULE_NAME . '_ZONE'][$this->sHookType]['data'];
			$aAssign['aConnectors']             = $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'];
			$aAssign['sPosition']               = 'top';
			$aAssign['sStyle']                  = 'badgeTop';
			$aAssign['sBackUri']                = self::$sCurrentURI;
		}
		else {
			$aAssign['bDisplay'] = false;
		}

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS_JS, 'assign' => $aAssign)
		);
	}

	/**
	 * _displayBlock() method
	 *
	 * @param array $aParams
	 * @return array
	 */
	private function _displayBlock(array $aParams)
	{
		// set
		$aAssign = array();

		// get all configured hooks
		if (FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_BLOCK']
			&& !empty($GLOBALS[_FPC_MODULE_NAME . '_ZONE'][$this->sHookType]['data'])
		) {

			$aAssign['sStyle']                  = strtolower(_FPC_MODULE_NAME).'_mini_button';
			$aAssign['bDisplay']                = true;
			$aAssign['bConnectorsActive']       = self::$bConnectorsActive;
			$aAssign['sConnectorButtonsIncl']   = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS);
			$aAssign['aHookConnectors']         = $GLOBALS[_FPC_MODULE_NAME . '_ZONE'][$this->sHookType]['data'];
			$aAssign['aConnectors']             = $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'];
			$aAssign['sPosition']               = 'blockAccount';
			$aAssign['sBackUri']                = self::$sCurrentURI;
			$aAssign['sLinkAccount16']          =  BT_FPCModuleTools::getAccountPageLink();

			// customer data
			$aAssign['sCustomerName'] = $this->iCustomerLogged ? BT_FPCModuleTools::getCookieObj()->customer_firstname . ' ' . BT_FPCModuleTools::getCookieObj()->customer_lastname : false;
			$aAssign['sFirstName'] = $this->iCustomerLogged ? BT_FPCModuleTools::getCookieObj()->customer_firstname : false;
			$aAssign['sLastName'] = $this->iCustomerLogged ? BT_FPCModuleTools::getCookieObj()->customer_lastname : false;

			// customer not logged
			if (!empty($this->iCustomerLogged)) {
				$aAssign['oCart'] = BT_FPCModuleTools::getCartObj();
				$aAssign['iCartQty'] = BT_FPCModuleTools::getCartObj()->nbProducts();
			}
		}
		else {
			$aAssign['bDisplay'] = false;
		}

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_ACCOUNT_BLOCK, 'assign' => $aAssign)
		);
	}


	/**
	 * _displayFooter() method displays buttons in footer
	 *
	 * @param array $aParams
	 * @return array
	 */
	private function _displayFooter(array $aParams)
	{
		// set
		$aAssign = array();

		$aAssign['bDisplay'] = false;

		$aAssign['bDisplayBlockInfoAccount'] = (int)FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_ACCOUNT'];
		$aAssign['bDisplayBlockInfoCart'] = (int)FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_CART'];

		// get all configured hooks
		if (array_key_exists($this->sHookType, $GLOBALS[_FPC_MODULE_NAME . '_ZONE'])
			&& self::$bConnectorsActive
		) {
			// set
			$sContent = '';

			$aAssign['aConnectors']             = $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'];
			$aAssign['sConnectorButtonsIncl']   = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS);

			// use case - footer layout
			if (false !== $GLOBALS[_FPC_MODULE_NAME . '_ZONE']['footer']['data']) {
				$aAssign['aHookConnectors'] = $GLOBALS[_FPC_MODULE_NAME . '_ZONE']['footer']['data'];
				$aAssign['sPosition']       = 'bottom';
				$aAssign['sStyle']          = 'badgeBottom';
				$aAssign['bDisplay']        = true;
				$aAssign['sBackUri']        = self::$sCurrentURI;

				$sContent .= FacebookPsConnect::$oModule->displayModule(_FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS_JS, $aAssign);
			}

			// use case - customer authentication page layout
			if (false !== $GLOBALS[_FPC_MODULE_NAME . '_ZONE']['authentication']['data']
				&&	(((version_compare(_PS_VERSION_, '1.5', '>') && Tools::getValue('controller') == 'authentication')
				||	strstr($_SERVER['SCRIPT_NAME'], 'authentication'))
				||
				((version_compare(_PS_VERSION_, '1.5', '>') && Tools::getValue('controller') == 'orderopc')
				||	strstr($_SERVER['SCRIPT_NAME'], 'order-opc')))
			) {
				$aAssign['sStyle']          = '';
				$aAssign['aHookConnectors'] = $GLOBALS[_FPC_MODULE_NAME . '_ZONE']['authentication']['data'];
				$aAssign['bDisplay']        = true;
				$aAssign['sBackUri']        = self::$sCurrentURI;

				if ((version_compare(_PS_VERSION_, '1.5', '>') && Tools::getValue('controller') == 'orderopc')
					||	strstr($_SERVER['SCRIPT_NAME'], 'order-opc')
				) {
					$aAssign['sPosition'] = 'newaccount';

				}
				else {
					$aAssign['sPosition'] = 'authentication';
				}

				// use case on FB friendly permission option
				if (!empty($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['facebook']['data']['activeConnector'])
					&&	BT_FPCModuleTools::getCustomerId() == 0
				) {
					if (!empty($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['facebook']['data']['permissions'])) {
						$aAssign['sFriendlyText'] = FacebookPsConnect::$oModule->l('You can use any of the login buttons above to automatically create an account on our shop.', 'hook-display_class') . '.';
					}
					else {
						$aAssign['sDefaultText'] = FacebookPsConnect::$oModule->l('You can use any of the login buttons above to automatically create an account on our shop.', 'hook-display_class') . '.';
					}
				}

				$sContent .= FacebookPsConnect::$oModule->displayModule(_FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS_JS, $aAssign);
			}
			// use case - top block user layout iset test for 1.6 hide block
			if (isset($GLOBALS[_FPC_MODULE_NAME . '_ZONE']['blockUser']) && false !== $GLOBALS[_FPC_MODULE_NAME . '_ZONE']['blockUser']['data']) {
				$aAssign['sStyle']          = strtolower(_FPC_MODULE_NAME).'_mini_button';
				$aAssign['aHookConnectors'] = $GLOBALS[_FPC_MODULE_NAME . '_ZONE']['blockUser']['data'];
				$aAssign['sPosition']       = 'blockUser';
				$aAssign['bDisplay']        = true;
				$aAssign['sBackUri']        = self::$sCurrentURI;


				$sContent .= FacebookPsConnect::$oModule->displayModule(_FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS_JS, $aAssign);
			}
			$aAssign['sContent'] = $sContent;
		}

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_BUTTONS_CNT, 'assign' => $aAssign)
		);
	}

	/**
	 * _displayAccount() method displays fancybox if customer do not use a social connector to link his PS account
	 *
	 * @category hook collection
	 * @uses
	 *
	 * @param array $aParams
	 * @return array
	 */
	private function _displayAccount()
	{
		$aAssign = array(
			'iCustomerId' 		=> $this->iCustomerLogged,
			'bUseJqueryUI'	    => true,
		);

		$aAssign['bDisplay'] = false;

		// if one of connectors is active at least
		if (self::$bConnectorsActive) {
			require_once(_FPC_PATH_LIB . 'module-dao_class.php');

			// include abstract connector
			require_once(_FPC_PATH_LIB_CONNECTOR . 'base-connector_class.php');

			if (FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DISPLAY_FB_POPIN']
				&&	!empty($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['facebook']['data']['activeConnector'])
			) {
				// set
				$bSocialCustomerExist = false;

				// loop on each connector to check if social account already exists - if not, display FB popin account association
				foreach ($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'] as $sName => $aConnector) {
					if (!empty($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'][$sName]['data'])) {
						// get connector options
						$aParams = $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'][$sName]['data'];

						// get connector
						$oConnector = BT_BaseConnector::get($sName, $aParams);

						// check if customer is already logged from FB connector
						if ($oConnector->existSocialAccount($aAssign['iCustomerId'], 'ps')) {
							$bSocialCustomerExist = true;
						}

						unset($oConnector);
					}
				}

				if (!BT_FPCModuleDao::existCustomerAssociationStatus(FacebookPsConnect::$iShopId, $this->iCustomerLogged)
					&&	empty($bSocialCustomerExist)
				) {
					$aAssign['bDisplay']                = true;
					$aAssign['bSocialCustomerExist']    = true;
					$aAssign['sConnectorButtonFacebook']= BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_HOOK_PATH . _FPC_TPL_BUTTON_FB);
					$aAssign['sModuleURI']              = $this->sModuleURI;
					$aAssign['bFriendlyPermission']     = !empty($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['facebook']['data']['permissions'])? true : false;
					$aAssign['sBackUri']                = self::$sCurrentURI;
				}
			}

			// get connector options
			$aParams = $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']['twitter']['data'];

			// test if twitter is already configured
			if (!empty($aParams)) {
				// get connector
				$oConnector = BT_BaseConnector::get('twitter', $aParams);

				// check if customer is already logged from FB connector
				if ($oConnector->existSocialAccount($aAssign['iCustomerId'], 'ps')
					&&	strstr(FacebookPsConnect::$oCookie->email, 'twitter.com')
				) {
					$aAssign['iCustomerId'] = md5(_FPC_MODULE_NAME . 'twitter' . $aAssign['iCustomerId']);
					$aAssign['sConnector'] = 'twitter';
					$aAssign['bTwitterCustomerExist'] = true;
					$aAssign['bDisplay'] = true;
				}
				unset($oConnector);
			}

			// use case - data sent for collecting
			$sRequestData = Tools::getValue('data');

			if (!empty($sRequestData)) {
				$aRequestData = unserialize(gzuncompress(urldecode(base64_decode($sRequestData))));

				if (!empty($aRequestData)) {
					if (empty($aRequestData['ci'])) {
						$aRequestData['ci'] = md5('collect' . FacebookPsConnect::$oCookie->id_customer);

						if (!empty($aRequestData['cn']) && 	!empty($aRequestData['ca'])	&&!empty($aRequestData['ct']) && !empty($aRequestData['oi'])) {
							// execute social collect method
							$sReturn = FacebookPsConnect::$oModule->HookSocialCollector(base64_decode($aRequestData['cn']), $aRequestData);

							if (!empty($sReturn)) {
								// get collect data array
								$oResponse = BT_FPCModuleTools::jsonDecode($sReturn);

								if (!empty($oResponse->status)) {
									$aAssign['bCustomerCollect'] = true;
								}
							}
						}
					}
				}
			}
		}

		$aAssign['sModuleURI'] = _FPC_MODULE_URL . 'ws-' . _FPC_MODULE_SET_NAME . '.php';

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_ACCOUNT, 'assign' => $aAssign)
		);
	}
}