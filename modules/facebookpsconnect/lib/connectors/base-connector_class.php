<?php
/**
 * base-connector_class.php file defines method for handling connectors
 */

/**
 * declare Connector Exception class
 */
class BT_ConnectorException extends Exception {}


abstract class BT_BaseConnector
{

	/**
	 * @var object $oUser : get user's social data
	 */
	public $oUser = null;

	/**
	 * @var object $oSession : get session object
	 */
	protected static $oSession = null;

	/**
	 * @var array $aRequiredAddressFields : authorized address fields
	 */
	public static $aRequiredAddressFields = array(
		'id_country'    => 0,
		'id_state'      => 0,
		'id_customer'   => 0,
		'id_manufacturer' => 0,
		'id_supplier' => 0,
		'id_warehouse' => 0,
		'alias' => '',
		'company' => '',
		'lastname' => '',
		'firstname' => '',
		'address1' => '',
		'address2' => '',
		'postcode' => '',
		'city' => '',
		'other' => '',
		'phone' => '',
		'phone_mobile' => '',
		'vat_number' => '',
		'dni' => '',
		'date_add' => '',
		'date_upd' => '',
		'active' => 1,
		'deleted' => 0,
	);

	/**
	 * @var string $sConnector : connector name
	 */
	public static $sName = '';

	/**
	 * @var string $sModuleURI : set Module URI
	 */
	public static $sModuleURI = '';

	/**
	 * get params keys
	 *
	 * @param array $aParams
	 */
	abstract public  function __construct(array $aParams);

	/**
	 * connect() method check user authentication
	 *
	 * @param array $aParams
	 * @return string
	 */
	abstract public function connect(array $aParams = null);

	/**
	 * callback() method executed by social network callback if needed
	 *
	 * @param array $aParams
	 */
	abstract public function callback(array $aParams = null);

	/**
	 * setUser() method create stdClass object for user's social data
	 *
	 */
	public function setUser() {
		$this->oUser = new stdClass();
	}

	/**
	 * login() method return succeeded HTML message of connector
	 *
	 * @param $sData
	 * @return string
	 */
	protected function login($sData = '')
	{
		$sLink = '';

		// get back URI
		$sBackURI = self::$oSession->get('back');

		if (!empty($sBackURI)) {
			$sLink = urldecode($sBackURI);
		}
		else {
			if (version_compare(_PS_VERSION_, '1.4', '>')) {
				$sLink = BT_FPCModuleTools::getAccountPageLink();
			}
			else {
				global $smarty;

				$sLink = $smarty->_tpl_vars['base_dir_ssl']  . 'my-account.php';
			}
		}

		if (!empty($sData)) {
			$sLink .= (strstr($sLink, '?')? '&' : '?') . 'data=' . $sData;
		}

		// detect user agent to redirect or close the popup windows and reload the current page
		if (!empty($_SERVER['HTTP_USER_AGENT'])
			&& ( stristr($_SERVER['HTTP_USER_AGENT'], 'iphone') ||  stristr($_SERVER['HTTP_USER_AGENT'], 'mobile'))
		) {
			header("Location: " . $sLink);
			exit(0);
		}
		else {
			return '<script>'
				. '     window.opener.location.href = "' . $sLink . '";'
				. '     window.opener.focus();'
				. '     window.close();'
				. '</script>';
		}
	}

	/**
	 * createCustomer() method create customer in PS with social account association
	 *
	 * @param bool $bCreatePs
	 * @param bool $bCreatePs
	 * @return bool
	 */
	protected function createCustomer($bCreatePs, $bCreateSocial) {
		// set
		$bResult = true;

		// set transaction
		Db::getInstance()->Execute('BEGIN');

		// use case - create PS account
		if ($bCreatePs) {
			if (!empty($this->oUser->email) && !empty($this->oUser->first_name)) {
				// set default values
				$iGender = empty($this->oUser->gender)? self::getGender('') : $this->oUser->gender;

				// throw exception if default customer group empty
				if (empty(FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DEFAULT_CUSTOMER_GROUP'])) {
					throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Internal server error => default customer group is empty', 'base-connector_class'), 518);
				}
				else {
					$iDefaultGroup = FacebookPsConnect::$aConfiguration[_FPC_MODULE_NAME . '_DEFAULT_CUSTOMER_GROUP'];
				}

				// set password
				$sPassword = mt_rand();
				$sPasswordGen = Tools::encrypt($sPassword);

				// date
				$sLastPwdGen = time();
				$iDateAdd = time();
				// secure key
				$sSecureKey = md5(uniqid(rand(), true));

				// define query
				$sQuery = 'INSERT INTO `' . _DB_PREFIX_ . 'customer` SET'
					. (version_compare(_PS_VERSION_, '1.5', '>')? ' id_shop = ' . FacebookPsConnect::$iShopId . ', ' : '')
					. ' id_gender = ' . $iGender .', '
					. ((version_compare(_PS_VERSION_, '1.3', '>')) ? ' id_default_group = ' . $iDefaultGroup . ', ' : '')
					. ' firstname = "' . $this->oUser->first_name . '", lastname = "' . $this->oUser->last_name . '", '
					. ' email = "' . pSQL($this->oUser->email) . '", passwd = "' . $sPasswordGen . '", '
					. ' last_passwd_gen = ' . $sLastPwdGen . ','
					. ' secure_key = "' . pSQL($sSecureKey) . '", active = 1, '
					. ' date_add = FROM_UNIXTIME(' . $iDateAdd . '), date_upd = FROM_UNIXTIME(' . $iDateAdd . ') ';

				if (!empty($this->oUser->birthday)) {
					$sQuery .= ', birthday = "' . $this->oUser->birthday . '"';
				}

				// exec query
				$bResult = Db::getInstance()->Execute($sQuery);

				if ($bResult) {
					// get last insert ID
					$this->oUser->customerId = Db::getInstance()->Insert_ID();

					$sQuery = 'INSERT INTO `'._DB_PREFIX_.'customer_group` SET id_customer = ' . $this->oUser->customerId . ', id_group = ' . $iDefaultGroup;

					// exec query
					$bResult = Db::getInstance()->Execute($sQuery);

					// use case - customer creation succeed
					if ($bResult) {
						// exec transaction
						Db::getInstance()->Execute('COMMIT');

						// send an e-mail with password
						$this->sendCustomerNotification(FacebookPsConnect::$iCurrentLang, $this->oUser->email, $sPassword, $this->oUser->first_name, $this->oUser->last_name);
					}
					else {
						Db::getInstance()->Execute('ROLLBACK');

						throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Internal server error => customer creation in database failed', 'base-connector_class'), 519);
					}
				}
			}
			else {
				Db::getInstance()->Execute('ROLLBACK');

				throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Internal server error => invalid social customer email and name', 'base-connector_class'), 521);
			}
		}

		// use case - if customer is created or logged
		if (!empty($this->oUser->customerId)) {
			// use case - update customer address or birthday
			if (empty($bCreatePs)) {
				// update birthday
				if (!empty($this->oUser->birthday)) {
					$this->updateCustomerBirthDay($this->oUser->customerId, $this->oUser->birthday);
				}
				// update gender
				if (!empty($this->oUser->gender)) {
					$this->updateCustomerGender($this->oUser->customerId, $this->oUser->gender);
				}
			}

			// update address
			if (!empty($this->oUser->address)) {
				$this->updateCustomerAddress($this->oUser->customerId, $this->oUser->address);
			}
			// use case - create PS account
			if ($bCreateSocial) {
				if (!empty($this->oUser->id) && !empty($this->oUser->email)) {
					$sQuery = 'INSERT INTO `' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_connect` SET '
						. ' CNT_CUST_ID = ' . $this->oUser->customerId . ', '
						. ' CNT_SHOP_ID = ' . FacebookPsConnect::$iShopId . ', '
						. ' CNT_CUST_SOCIAL_ID = "' . pSQL($this->oUser->id) . '", '
						. ' CNT_CUST_TYPE = "' . self::$sName . '"';

					// exec query
					$bResult = Db::getInstance()->Execute($sQuery);

					// use case - customer creation succeed
					if ($bResult) {
						// exec transaction
						Db::getInstance()->Execute('COMMIT');
					}
					else {
						Db::getInstance()->Execute('ROLLBACK');

						throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Internal server error => customer creation in database failed', 'base-connector_class'), 522);
					}
				}
				else {
					Db::getInstance()->Execute('ROLLBACK');

					throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Internal server error => invalid social customer email', 'base-connector_class'), 523);
				}
			}
		}

		return $bResult;
	}


	/**
	 * loadCustomer() method load customer in PS
	 *
	 * @param numeric $nSocialId
	 */
	protected function loadCustomer($nSocialId) {
		// get customer ID
		$iCustomerId = self::getCustomerId($nSocialId);

		// auth customer
		$oCustomer = new Customer($iCustomerId);

		// is valid customer
		if (Validate::isLoadedObject($oCustomer)) {
			if (version_compare(_PS_VERSION_, '1.5', '>')) {
				Context::getContext()->cookie->id_customer = intval($oCustomer->id);
				Context::getContext()->cookie->customer_lastname = $oCustomer->lastname;
				Context::getContext()->cookie->customer_firstname = $oCustomer->firstname;
				Context::getContext()->cookie->logged = 1;
				Context::getContext()->cookie->passwd = $oCustomer->passwd;
				Context::getContext()->cookie->email = $oCustomer->email;
//                Context::getContext()->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
				Context::getContext()->customer->logged = 1;

				if (Configuration::get('PS_CART_FOLLOWING')
					&& (empty(Context::getContext()->cookie->id_cart)
					|| Cart::getNbProducts(Context::getContext()->cookie->id_cart) == 0)
				) {
					Context::getContext()->cookie->id_cart = intval(Cart::lastNoneOrderedCart($oCustomer->id));
				}

				Hook::Exec('authentication');
			}
			else {
				global $cookie;

				$cookie->id_customer = intval($oCustomer->id);
				$cookie->customer_lastname = $oCustomer->lastname;
				$cookie->customer_firstname = $oCustomer->firstname;
				$cookie->logged = 1;
				$cookie->passwd = $oCustomer->passwd;
				$cookie->email = $oCustomer->email;
//                $cookie->is_guest = !Tools::getValue('is_new_customer', 1);
				$oCustomer->logged = 1;

				if (Configuration::get('PS_CART_FOLLOWING')
						&&
					(empty($cookie->id_cart)
						||
					Cart::getNbProducts($cookie->id_cart) == 0)
				) {
					$cookie->id_cart = intval(Cart::lastNoneOrderedCart($oCustomer->id));
				}

				Module::HookExec('authentication');
			}
		}
		else {
			throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Internal server error => authentication failed', 'base-connector_class'), 531);
		}
	}

	/**
	 * existSocialAccount() method check if customer social account already exists
	 *
	 * @param int $iId
	 * @param string $sType
	 * @return int : 0 => not exists
	 */
	public function existSocialAccount($iId, $sType = 'social') {
		// query
		$sQuery = 'SELECT COUNT(*) as nb'
			. ' FROM ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_connect'
			. ' WHERE ';

		if ($sType == 'ps') {
			$sQuery .= '`CNT_CUST_ID` = ' . $iId;
		}
		else {
			$sQuery .= '`CNT_CUST_SOCIAL_ID` = "' . pSQL($iId) . '"';
		}

		$sQuery .= ' AND CNT_CUST_TYPE = "' . self::$sName . '"';

		// execute
		$iSocialExist = Db::getInstance()->getRow($sQuery);

		// customer exists in social table
		return $iSocialExist['nb'];
	}

	/**
	 * deleteSocialAccount() method delete social account
	 *
	 * @param int $iSocialId
	 * @return bool
	 */
	protected function deleteSocialAccount($iSocialId) {
		// query
		$sQuery = 'DELETE FROM ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_connect '
			. ' WHERE CNT_CUST_SOCIAL_ID = "' . pSQL($iSocialId) . '"'
			. ' AND CNT_CUST_TYPE = "' . self::$sName . '"';

		// execute
		return (
			Db::getInstance()->Execute($sQuery)
		);
	}

	/**
	 * existPsAccount() method check if customer account already exists
	 *
	 * @param mixed $mCustomerId
	 * @param string $sType
	 * @return int : 0 => not exists
	 */
	public function existPsAccount($mCustomerId, $sType = 'email') {
		// query
		$sQuery = 'SELECT COUNT(*) as nb'
			. ' FROM ' . _DB_PREFIX_ . 'customer'
			. ' WHERE `active` = 1 AND ';

		if ($sType == 'id') {
			$sQuery .= '`id_customer` = ' . $mCustomerId;
		}
		else {
			$sQuery .= '`email` = "' . pSQL($mCustomerId) . '"';
		}

		$sQuery .= ' AND `deleted` = 0' . (version_compare(_PS_VERSION_, '1.4', '>')? ' AND `is_guest` =  0' : '');

		// execute
		$iPsExist = Db::getInstance()->getRow($sQuery);

		// customer exists in PS table
		return $iPsExist['nb'];
	}

	/**
	 * getCustomerId() method returns PS customer ID
	 *
	 * @param int $iSocialId
	 * @return int : PS customer ID
	 */
	protected function getCustomerId($iSocialId) {
		// query
		$sQuery = 'SELECT CNT_CUST_ID as id'
			. ' FROM ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_connect'
			. ' WHERE `CNT_CUST_SOCIAL_ID` = "' . pSQL($iSocialId) . '" AND CNT_CUST_TYPE = "' . self::$sName . '"';

		// execute
		$aResult = Db::getInstance()->getRow($sQuery);

		return (
			!empty($aResult['id'])? $aResult['id'] : 0
		);
	}


	/**
	 * getSocialData() method returns social data
	 *
	 * @param int $iSocialId
	 * @return array
	 */
	protected function getSocialData($iSocialId) {
		// query
		$sQuery = 'SELECT * '
			. ' FROM ' . _DB_PREFIX_ . strtolower(_FPC_MODULE_NAME) . '_connect'
			. ' WHERE `CNT_CUST_SOCIAL_ID` = "' . pSQL($iSocialId) . '" AND CNT_CUST_TYPE = "' . self::$sName . '"';

		// execute
		return (
			Db::getInstance()->ExecuteS($sQuery)
		);
	}


	/**
	 * getCustomerData() method returns customer data
	 *
	 * @param int $iCustomerId
	 * @return array
	 */
	protected function getCustomerData($iCustomerId) {
		// query
		$sQuery = 'SELECT * '
			. ' FROM ' . _DB_PREFIX_ . 'customer'
			. ' WHERE `id_customer` = "' . pSQL($iCustomerId) . '"';

		// execute
		return (
			Db::getInstance()->ExecuteS($sQuery)
		);
	}


	/**
	 * updateCustomerBirthDay() method update customer birthday
	 *
	 * @param int $iSocialId
	 * @param string $sBirthday
	 * @return int : PS customer ID
	 */
	protected function updateCustomerBirthDay($iCustomerId, $sBirthday) {
		// query
		$sQuery = 'UPDATE ' . _DB_PREFIX_ . 'customer SET birthday = "' . pSQL($sBirthday) . '"'
			. ' WHERE `active` = 1 AND `id_customer` = ' . $iCustomerId
			. ' AND `deleted` = 0' . (version_compare(_PS_VERSION_, '1.4', '>')? ' AND `is_guest` =  0' : '');

		// execute
		return (
			Db::getInstance()->Execute($sQuery)
		);
	}

	/**
	 * updateCustomerAddress() method update customer address
	 *
	 * @param int $iCustomerId
	 * @param obj $oAddress
	 * @return bool
	 */
	protected function updateCustomerAddress($iCustomerId, stdClass $oAddress) {
		$bInsert = true;

		// query
		$sQuery = 'SELECT count(*) as nb'
			. ' FROM ' . _DB_PREFIX_ . 'address'
			. ' WHERE `id_customer` = ' . $iCustomerId;

		$aExists = Db::getInstance()->ExecuteS($sQuery);

		if (empty($aExists[0]['nb'])) {
			// define query
			$sQuery = 'INSERT INTO `' . _DB_PREFIX_ . 'address` SET'
				. ' id_country = ' . $oAddress->id_country .', '
				. ' id_state = ' . $oAddress->id_state .', '
				. ' id_customer = ' . $iCustomerId .', '
				. ' id_manufacturer = ' . $oAddress->id_manufacturer .', '
				. ' id_supplier = ' . $oAddress->id_supplier .', '
				. ((version_compare(_PS_VERSION_, '1.5', '>')) ? ' id_warehouse = ' . $oAddress->id_warehouse . ', ' : '')
				. ' alias = "' . pSQL($oAddress->alias) .'", '
				. ' company = "' . pSQL($oAddress->company) .'", '
				. ' lastname = "' . pSQL($oAddress->lastname) .'", '
				. ' firstname = "' . pSQL($oAddress->firstname) .'", '
				. ' address1 = "' . pSQL($oAddress->address1) .'", '
				. ' address2 = "' . pSQL($oAddress->address2) .'", '
				. ' postcode = "' . pSQL($oAddress->postcode) .'", '
				. ' city = "' . pSQL($oAddress->city) .'", '
				. ' other = "' . pSQL($oAddress->other) .'", '
				. ' phone = "' . pSQL($oAddress->phone) .'", '
				. ' phone_mobile = "' . pSQL($oAddress->phone_mobile) .'", '
				. ((version_compare(_PS_VERSION_, '1.4', '>')) ? ' vat_number = "' . $oAddress->vat_number . '", ' : '')
				. ((version_compare(_PS_VERSION_, '1.4', '>')) ? ' dni = "' . $oAddress->dni . '", ' : '')
				. ' date_add = "' . $oAddress->date_add .'", '
				. ' date_upd = "' . $oAddress->date_upd .'", '
				. ' active = ' . $oAddress->active .', '
				. ' deleted = ' . $oAddress->deleted;

			// exec query
			$bInsert = Db::getInstance()->Execute($sQuery);
		}

		return $bInsert;
	}

	/**
	 * updateCustomerGender() method update gender
	 *
	 * @param int $iSocialId
	 * @param string $sBirthday
	 * @return int : PS customer ID
	 */
	protected function updateCustomerGender($iCustomerId, $iCustomerGender) {

		$bResult = true;

		// query
		$sQuery = 'SELECT id_gender as id'
			. ' FROM ' . _DB_PREFIX_ . 'customer'
			. ' WHERE `id_customer` = ' . $iCustomerId;

		$aGenderExists = Db::getInstance()->getValue($sQuery);

		// use case - empty id for 1.5 or id equal unknown gender for under 1.5
		if ((version_compare(_PS_VERSION_, '1.5', '>') && empty($aGenderExists['id']))
				||
			(version_compare(_PS_VERSION_, '1.5', '<') && $aGenderExists['id'] == 9)
		) {
			// query
			$sQuery = 'UPDATE ' . _DB_PREFIX_ . 'customer SET id_gender = ' . $iCustomerGender
				. ' WHERE `active` = 1 AND `id_customer` = ' . $iCustomerId
				. ' AND `deleted` = 0' . (version_compare(_PS_VERSION_, '1.4', '>')? ' AND `is_guest` =  0' : '');

			// execute
			$bResult = Db::getInstance()->Execute($sQuery);
		}

		return $bResult;
	}

	/**
	 * concatAddress() method concatenate address
	 *
	 * @param string $sAddress
	 * @param string $sSeparator
	 * @return string
	 */
	protected function concatAddress($sAddress, $sSeparator = ',') {

		$sLine1 = '';
		$sLine2 = '';

		if (strlen($sAddress) > 38) {
			$aAddress = explode($sSeparator, $sAddress);

			if (count($aAddress) == 3) {
				if (strlen($aAddress[0] . ' ' . $aAddress[1]) <= 38) {
					$sLine1 = trim($aAddress[0]) . ' ' . trim($aAddress[1]);
					$sLine2 = trim($aAddress[2]);
				}
				else {
					$sLine1 = trim($aAddress[0]);
					$sLine2 = trim($aAddress[1]);
				}
			}
			elseif (count($aAddress) == 2) {
				if (strlen($aAddress[0] . ' ' . $aAddress[1]) <= 38) {
					$sLine1 = trim($aAddress[0]) . ' ' . trim($aAddress[1]);
					$sLine2 = '';
				}
				else {
					$sLine1 = trim($aAddress[0]);
					$sLine2 = trim($aAddress[1]);
				}
			}
			else {
				$sLine1 = $aAddress[0];
			}
		}
		else {
			$sLine1 = $sAddress;
		}

		return array('line1' => $sLine1, 'line2' => $sLine2);
	}


	/**
	 * getGender() method get gender
	 *
	 * @param string $sGender
	 * @return int : PS customer gender ID
	 */
	protected function getGender($sGender) {
		$iGender = 0;

		if (version_compare(_PS_VERSION_, '1.5', '>')) {
			if ($sGender == 'male') {
				$iGender = 0;
			}
			elseif($sGender == 'female') {
				$iGender = 1;
			}
			// default case - male
			else {
				$iGender = 0;
			}

			$sQuery = 'SELECT id_gender '
				. ' FROM ' . _DB_PREFIX_ . 'gender'
				. ' WHERE `type` = ' . $iGender;

			$aGender = Db::getInstance()->getRow($sQuery);

			$iGender = !empty($aGender['id_gender'])? $aGender['id_gender'] : 1;

			unset($aGender);
		}
		else {
			if ($sGender == 'male') {
				$iGender = 1;
			}
			elseif($sGender == 'female') {
				$iGender = 2;
			}
			// case - unknown
			else {
				$iGender = 9;
			}
		}

		return $iGender;
	}


	/**
	 * getCountryID() method return the country ID
	 *
	 * @param string $sCountryCode
	 * @return int
	 */
	protected function getCountryID($sCountryCode)
	{
		// get the country ID if exists - if not get the default one
		$iDefaultCountryId = $iCountryId = (int) Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));

		// get lang ID
		$iCountryId = Country::getByIso(strtoupper($sCountryCode));

		if (empty($iCountryId)) {
			$iCountryId = $iDefaultCountryId;
		}

		return $iCountryId;
	}

	/**
	 * getStateId() method returns the state ID
	 *
	 * @param string $sCountryCode
	 * @param string $sStateCode
	 * @return int
	 */
	protected function getStateId($iCountryId, $sStateCode)
	{
		// set
		$iStateId = null;

		if ($this->containsStates($iCountryId)) {
			if (!empty($sStateCode)) {
				// get State ID
				$iStateId = (int) Db::getInstance()->getValue('
					SELECT `id_state` FROM `' . _DB_PREFIX_ . 'state`
					WHERE `id_country` = ' . $iCountryId . '
					AND `iso_code` = \'' . $sStateCode . '\' AND `active` = 1'
				);
			}
		}

		return $iStateId;
	}

	/**
	 * containsStates() method returns if country contains state or not
	 *
	 * @param int $iCountryId
	 * @return bool
	 */
	protected function containsStates($iCountryId)
	{
		return (
			(bool)Db::getInstance()->getValue('
			SELECT `contains_states`
			FROM `' . _DB_PREFIX_ . 'country`
			WHERE `id_country` = '. (int) $iCountryId)
		);
	}

	/**
	 * sendCustomerNotification() method send an-mail when the account is created
	 *
	 * @param int $iCountryId
	 * @return bool
	 */
	protected static function sendCustomerNotification($iIsoLang, $sEmail, $sPassword, $sFirstName, $sLastName)
	{
		// set params
		if (!empty($iIsoLang)
			&& !empty($sEmail)
			&& !empty($sPassword)
			&& !empty($sFirstName)
			&& !empty($sLastName)
		) {
			require_once(_FPC_PATH_LIB . 'mail-send_class.php');

			$aParams = array();

			$aParams['isoId'] = $iIsoLang;
			$aParams['email'] = $sEmail;
			$aParams['password'] = $sPassword;
			$aParams['firstname'] = $sFirstName;
			$aParams['lastname'] = $sLastName;

			BT_FpcMailSend::create()->run('customerAccountNotification', $aParams);
		}
	}



	/**
	 * get() method instantiate matched connector object
	 *
	 * @param string $sConnectorType
	 * @param array $aParams
	 * @return obj connector abstract type
	 */
	public static function get($sConnectorType, array $aParams = null) {
		// if valid connector
		if (in_array($sConnectorType, array_keys($GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS']))) {

			// set module URI
			if (!empty($aParams['callback'])) {
				self::$sModuleURI = $aParams['callback'];
			}
			elseif (!empty($aParams['sURI'])) {
				self::$sModuleURI = $aParams['sURI'];
			}

			// include
			require_once ($sConnectorType . '-connect_class.php');
			require_once (_FPC_PATH_LIB_COMMON . 'session.class.php');

			// get session object
			self::$oSession = BT_FpcSession::create(array('sPrefix' => _FPC_MODULE_NAME . '_'));

			// check if back URI is set
			if (!empty($aParams['back'])) {
				// delete first
				self::$oSession->delete('back');

				self::$oSession->set('back', $aParams['back']);
			}

			// set class name
			$sClassName = 'BT_' . ucfirst($sConnectorType) . 'Connect';

			// get connector name
			self::$sName = $sConnectorType;

			return (
				new $sClassName($aParams)
			);
		}
		else {
			throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Internal server error => invalid connector', 'base-connector_class'), 520);
		}
	}
}