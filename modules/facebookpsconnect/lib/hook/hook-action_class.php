<?php
/**
 * hook-action_class.php file defines controller which manage hooks sequentially
 */

class BT_FPCHookAction implements BT_IFpcHook
{
	/**
	 * @var string $sHookAction : define hook action
	 */
	protected $sHookAction = null;

	/**
	 * Magic Method __construct assigns few information about hook
	 *
	 * @param string $sHookAction
	 */
	public function __construct($sHookAction)
	{
		// set hook action
		$this->sHookAction = $sHookAction;
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
	public function run(array $aParams = null)
	{
		// set variables
		$aDisplayHook = array();

		switch ($this->sHookAction) {
			case 'connect' :
			case 'callback' :
				// use case - callback executed by social network connector
				$aDisplayHook = call_user_func_array(array($this, '_connect'), array('connector' => $aParams['connector'], $aParams));
				break;
			case 'collect' :
				// use case - collect data of social widgets
				$aDisplayHook = call_user_func_array(array($this, '_collect'), array('collector' => $aParams['type'], $aParams));
				break;
			case 'updateCustomer' :
				// use case - update customer facebook association
				$aDisplayHook = call_user_func_array(array($this, '_updateCustomer'), array($aParams));
				break;
			case 'updateEmail' :
				// use case - update customer facebook association
				$aDisplayHook = call_user_func_array(array($this, '_updateEmail'), array($aParams));
				break;
			default :
				break;
		}

		return $aDisplayHook;
	}

	/**
	 * _connect() method
	 *
	 * @param string $sType
	 * @param array $aParams
	 * @return array
	 */
	private function _connect($sType, array $aParams)
	{
		// include abstract connector
		require_once(_FPC_PATH_LIB_CONNECTOR . 'connector-ctrl_class.php');

		// instantiate
		$oConnectorCtrl = new BT_FPCConnectorCtrl($sType);

		// set Module URI
		$aParams['sURI'] = BT_FPCModuleTools::detectHttpUri(_FPC_MODULE_URL . $sType . '-callback.php');

		// get serialized connector data
		BT_FPCModuleTools::getConnectorData();

		// get connector options
		$aParams = array_merge($aParams, $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'][$sType]['data']);

		// get logged Customer ID
		$iCustomerId = BT_FPCModuleTools::getCustomerId();

		if (!empty($iCustomerId)) {
			$aParams['iCustomerId'] = $iCustomerId;
		}

		// exec connector
		$aAssign = $oConnectorCtrl->run($aParams);

		unset($oConnectorCtrl);

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_CONNECTOR_RESPONSE, 'assign' => $aAssign)
		);
	}

	/**
	 * _collect() method
	 *
	 * @param string $sType
	 * @param array $aParams
	 * @return array
	 */
	private function _collect($sType, array $aParams)
	{
		$aAssign = array();

		// ci = customer ID
		// si = customer social ID
		// ca = connector action : ex like or want
		// ct = connector type : ex product or category
		// oi = object ID : ex product ID or category ID (base64 encoded)

		// test if params are valid
		if (((!empty($aParams['ci']) && md5('collect' . FacebookPsConnect::$oCookie->id_customer) == $aParams['ci']) || !empty($aParams['si']))
			&& !empty($aParams['ca'])
			&& !empty($aParams['ct'])
			&& !empty($aParams['oi'])
		) {
			// need to be decoded
			if (!empty($aParams['ci'])) {
				$aParams['ci'] = FacebookPsConnect::$oCookie->id_customer;
				$aParams['oi'] = base64_decode($aParams['oi']);
			}
			if (!empty($aParams['si']) && !empty($aParams['ci'])) {
				$aParams['si'] = base64_decode($aParams['si']);
			}
			$aParams['ca'] = base64_decode($aParams['ca']);
			$aParams['ct'] = base64_decode($aParams['ct']);

			// include
			require_once(_FPC_PATH_LIB . 'module-dao_class.php');

			if (!BT_FPCModuleDao::existSocialData(FacebookPsConnect::$iShopId, $aParams['ci'], $aParams['si'], $sType, $aParams['ca'], $aParams['ct'], $aParams['oi'])) {
				// register social data
				$bAdd = BT_FPCModuleDao::collectSocialData($aParams['ci'], $aParams['si'], FacebookPsConnect::$iShopId, $sType, $aParams['ca'], $aParams['ct'], $aParams['oi']);
			}
			else {
				$bAdd = false;
			}
			$aAssign['sResponse'] = BT_FPCModuleTools::jsonEncode(array('status' => $bAdd));
		}

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_COllECTOR_RESPONSE, 'assign' => $aAssign)
		);
	}

	/**
	 * _updateCustomer() method update customer facebook association
	 *
	 * @param array $aParams
	 * @return bool
	 */
	private function _updateCustomer(array $aParams)
	{
		$aAssign = array('bFancyClose' => true);

		if (!empty($aParams['id'])) {
			// include
			require_once(_FPC_PATH_LIB . 'module-dao_class.php');

			if (!BT_FPCModuleDao::existCustomerAssociationStatus(FacebookPsConnect::$iShopId,$aParams['id'])) {
				BT_FPCModuleDao::addCustomerAssociationStatus(FacebookPsConnect::$iShopId,$aParams['id']);
			}
		}

		return (
			array('tpl' => _FPC_TPL_HOOK_PATH . _FPC_TPL_EMPTY, 'assign' => $aAssign)
		);
	}

	/**
	 * _updateEmail() method update customer email
	 *
	 * @param array $aParams
	 * @return bool
	 */
	private function _updateEmail(array $aParams)
	{
		$aAssign = array();

		if (version_compare(_PS_VERSION_, '1.4', '>')) {
			$oLink = new Link();

			$sLink = $oLink->getPageLink('my-account.php');

			unset($oLink);
		}
		else {
			global $smarty;

			$sLink = $smarty->_tpl_vars['base_dir_ssl']  . 'my-account.php';
		}

		$aAssign['sLink'] = $sLink;

		// get serialized connector data
		BT_FPCModuleTools::getConnectorData();

		// check if customer is the same customer connected and if the e-mail is valid
		if (!empty($aParams['connector'])
			&&	!empty($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'][$aParams['connector']]['data']['activeConnector'])
			&&	$aParams['customerId'] === md5(_FPC_MODULE_NAME . $aParams['connector'] . FacebookPsConnect::$oCookie->id_customer)
			&&	!empty($aParams['customerId'])
			&&	filter_var($aParams['socialEmail'], FILTER_VALIDATE_EMAIL) !== false
		) {
			// include
			require_once(_FPC_PATH_LIB . 'module-dao_class.php');

			// check if exists and return id customer if exists
			$iCustomerId = Customer::customerExists($aParams['socialEmail'], true);

			// if customer not exists
			if (empty($iCustomerId)) {
				if (BT_FPCModuleDao::updateCustomerEmail(FacebookPsConnect::$oCookie->id_customer, $aParams['socialEmail'])) {
					$aAssign['sMsg'] = FacebookPsConnect::$oModule->l('Your information has been updated', 'hook-action_class');

					// set the new e-mail in cookie
					if (version_compare(_PS_VERSION_, '1.5', '>')) {
						Context::getContext()->cookie->email = $aParams['socialEmail'];
					}
					else {
						global $cookie;

						$cookie->email = $aParams['socialEmail'];
					}
				}
				else {
					$aAssign['aErrors'][] = array('msg' => FacebookPsConnect::$oModule->l('Internal server error. The customer e-mail has not been updated. Please try again by clicking on reload button below', 'hook-action_class') . '.', 'code'  => 590);
				}

				// manage the update firstname and name for twitter connexion
				BT_FPCModuleDao::updateCustomerFirstName(FacebookPsConnect::$oCookie->id_customer, $aParams['socialFirstName']);
				BT_FPCModuleDao::updateCustomerName(FacebookPsConnect::$oCookie->id_customer, $aParams['socialName']);
				BT_FPCModuleDao::updateCustomerPassword(FacebookPsConnect::$oCookie->id_customer, $aParams['socialPassword']);

				require_once(_FPC_PATH_LIB . 'mail-send_class.php');

				$aMailParams = array(
					'isoId' => FacebookPsConnect::$iCurrentLang,
					'email' => $aParams['socialEmail'],
					'password' => $aParams['socialPassword'],
					'firstname' => $aParams['socialFirstName'],
					'lastname' => $aParams['socialName'],
				);
				BT_FpcMailSend::create()->run('customerTwitterNotification', $aMailParams);
			}
			// use case - already exists
			else {
				$aAssign['aErrors'][] = array('msg' => FacebookPsConnect::$oModule->l('This e-mail address is already taken by a customer account or you already have linked this e-mail address with another network. Please try again by clicking on reload button below', 'hook-action_class') . '.', 'code'  => 591);
			}
		}
		else {
			$aAssign['aErrors'][] = array('msg' => FacebookPsConnect::$oModule->l('Internal server error. The customer could not be identified. You may be a victim of cross-site request forgery', 'hook-action_class') . '.', 'code'  => 592);
		}

		if (empty($aAssign['aErrors'])) {
			$sTpl = _FPC_TPL_HOOK_PATH . _FPC_TPL_CONFIRM;
		}
		else {
			$sTpl = _FPC_TPL_HOOK_PATH . _FPC_TPL_ERROR;
		}

		return (
			array('tpl' => $sTpl, 'assign' => $aAssign)
		);
	}
}