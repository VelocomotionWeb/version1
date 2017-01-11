<?php
/**
 * facebook-connector_class.php file defines method for handing connectors
 */


if (!defined('_PS_VERSION_')) {
	exit(1);
}


/**
 * declare Facebook Exception class
 */
class BT_FacebookException extends BT_ConnectorException{}

class BT_FacebookConnect extends BT_BaseConnector
{
	/**
	 * @var string $sFbUri : FB URI
	 */
	public $sFbUri = 'https://www.facebook.com/dialog/oauth';

	/**
	 * @var string $sGraphUrl : FB Graph URL
	 */
	public $sGraphUrl = 'https://graph.facebook.com/oauth/access_token';

	/**
	 * @var string $sFieldList : FB field list
	 */
	public $sFieldList = 'email,first_name,last_name,birthday,gender';

	/**
	 * __construct magic method assign connector keys
	 *
	 * @param array $aParams
	 */
	public function __construct(array $aParams)
	{
		$this->iAppId = !empty($aParams['id'])? $aParams['id'] : null;
		$this->sSecret = !empty($aParams['secret'])? $aParams['secret'] : null;
		$this->bCookie = true;
		$this->sPermissions = !empty($aParams['scope'])? $aParams['scope'] : null;

		// set customer id for Ps & FB association
		if (!empty($aParams['iCustomerId'])) {
			self::$oSession->set('iCustomerId', intval($aParams['iCustomerId']));
		}

		// put data in session - use case for connect and collect at the same time.
		if (!empty($aParams['data'])) {
			self::$oSession->set('data', $aParams['data']);
		}

		// set FB URLs
		$this->sFbUri .= '?client_id=' . $this->iAppId  . '&redirect_uri=' . urlencode(self::$sModuleURI) . '&scope=' . $this->sPermissions;
		$this->sGraphUrl .= '?client_id=' . $this->iAppId  . '&redirect_uri=' . urlencode(self::$sModuleURI) . '&client_secret=' . $this->sSecret;

		// set user
		parent::setUser();
	}


	/**
	 * __set() method allow to check value assign to property
	 *
	 * @param array $aParams
	 */
	public function __set($sName, $mValue)
	{
		switch ($sName) {
			case 'appId' :
			case 'iAppId' :
				$this->iAppId = is_numeric($mValue)? $mValue : null;
				break;
			case 'secret' :
			case 'sSecret' :
				$this->sSecret = is_string($mValue)? $mValue : null;
				break;
			case 'cookie' :
			case 'bCookie' :
				$this->bCookie = is_bool($mValue)? $mValue : null;
				break;
			case 'permissions' :
			case 'sPermissions' :
				$this->sPermissions = is_string($mValue)? $mValue : null;
				break;
			default:
				break;
		}
	}

	/**
	 * __get() method returns allowed properties
	 *
	 * @param string $sName
	 * @return property : mixed or null
	 */
	public function __get($sName)
	{
		switch ($sName) {
			case 'appId' :
			case 'iAppId' :
				return $this->iAppId;
				break;
			case 'secret' :
			case 'sSecret' :
				return $this->sSecret;
				break;
			case 'cookie' :
			case 'bCookie' :
				return $this->bCookie;
				break;
			case 'permissions' :
			case 'sPermissions' :
				return $this->sPermissions;
				break;
			default:
				break;
		}
		return null;
	}

	/**
	 * connect() method check if token is valid or not, and either redirect on FB interface or log the customer by creating his account if necessary
	 *
	 * @param array $aParams
	 * @return string
	 */
	public function connect(array $aParams = null)
	{
		// use case - not FB code returned - redirect
		if (empty($aParams['code']) || empty($aParams['access_token'])) {
			// redirect on FB interface
			$this->redirect();
		}
		else {
			// test token
			$oFBUser = BT_FPCModuleTools::jsonDecode(BT_FPCModuleTools::fileGetContent('https://graph.facebook.com/me?access_token=' . $aParams['access_token'] . '&fields=' . $this->sFieldList));

			// only if social user id exist
			if (!empty($oFBUser->id) && !empty($oFBUser->email)) {
				// set create status
				$bCreateStatus = true;
				$bCreatePs = false;
				$bCreateSocial = false;

				// set FB data
				$this->oUser->id = $oFBUser->id;
				$this->oUser->customerId = 0;
				$this->oUser->first_name = $oFBUser->first_name;
				$this->oUser->last_name = $oFBUser->last_name;
				$this->oUser->email = $oFBUser->email;

				// set birthday
				if (!empty($oFBUser->birthday)) {
					$aBirthday = explode('/', $oFBUser->birthday);

					// format date for PS customer table
					$this->oUser->birthday = $aBirthday[2] . '-' . $aBirthday[0] . '-' . $aBirthday[1];
				}

				// set gender
				if (!empty($oFBUser->gender)) {
					// get gender ID from PS
					$this->oUser->gender = parent::getGender($oFBUser->gender);
				}


				// use case - customer is already logged and ask him account association
				if (($iCustomerId = self::$oSession->get('iCustomerId')) !== null) {
					// get customer id
					$this->oUser->customerId = $iCustomerId;

					// delete customer ID session
					self::$oSession->delete('iCustomerId');

					// use case - social account not exists
					if (!parent::existSocialAccount($this->oUser->id)) {
						$bCreateSocial = true;
					}
					// use case - 2 accounts exist - return an error
					else {
						$aSocialData = parent::getSocialData($oFBUser->id);

						// get customer data of old account
						$aCustomerData = parent::getCustomerData($aSocialData[0]['CNT_CUST_ID']);

						throw new BT_FacebookException(FacebookPsConnect::$oModule->l('This Facebook account has already been linked to a customer account on our shop. The e-mail address of this account on our shop is', 'facebook-connect_class') . ' : "' . $aCustomerData[0]['email'] . '". '
							. FacebookPsConnect::$oModule->l('Please contact the merchant to warn him', 'facebook-connect_class') . '.',
							520);
					}
				}
				// use case - no customer logged - no association asked
				else {
					// test if user already exist in social table
					$bCreateSocial = !parent::existSocialAccount($this->oUser->id);

					// test if user already exist in PS table
					$bCreatePs = !parent::existPsAccount($this->oUser->email);

					// use case - social account exist
					if (empty($bCreateSocial)) {
						// use case - create new PS account and have to delete old social account
						if (!empty($bCreatePs)) {
							$iCustomerId = parent::getCustomerId($this->oUser->id);

							// use case - PS customer account exists too
							if (parent::existPsAccount($iCustomerId, 'id')) {
								$bCreateSocial = false;
								$bCreatePs = false;
							}
							else {
								parent::deleteSocialAccount($this->oUser->id);
								$bCreateSocial = true;
							}
						}
					}
					// use case - social account not exist + test if user already exist in PS table and set customer ID
					elseif (!$bCreatePs) {
						$this->oUser->customerId = Customer::customerExists($this->oUser->email, true);
					}
				}

				// use case - if one of 2 accounts has to be created at least
				if (!empty($bCreatePs) || !empty($bCreateSocial)) {
					// create customer in 1 or 2 tables
					$bCreateStatus = parent::createCustomer($bCreatePs, $bCreateSocial);
				}

				// use case  - create status valid
				if ($bCreateStatus) {
					// load customer
					parent::loadCustomer($this->oUser->id);

					// get data session if exists
					$sData = self::$oSession->get('data');

					// delete session
					self::$oSession->delete('data');

					return $this->login($sData);
				}
				else {
					throw new BT_FacebookException(FacebookPsConnect::$oModule->l('Internal server error. Account creation processing is unavailable', 'facebook-connect_class'), 521);
				}
			}
			else {
				throw new BT_FacebookException(FacebookPsConnect::$oModule->l('You may have not allowed the email address to be used or', 'facebook-connect_class') . ' ' . FacebookPsConnect::$oModule->l('The token is not valid. You may be a victim of cross-site request forgery or the connect method to the Facebook URL with HTTPS is not allowed. Please contact the merchant to warn him', 'facebook-connect_class'), 522);
			}
		}
	}


	/**
	 * callback() method check exchanged code and connect the customer
	 *
	 * @params array $aParams
	 */
	public function callback(array $aParams = null)
	{
		if ((!empty($aParams['state'])
				&&	self::$oSession->get('state') == $aParams['state'])
			&&	!empty($aParams['code'])
		) {
			// get oauth_token
			$sResponse = BT_FPCModuleTools::fileGetContent($this->sGraphUrl . '&code=' . $aParams['code']);

			if (!empty($sResponse)) {
				// set params
				$aQUERY = array();

				// parse URI
				parse_str($sResponse, $aQUERY);

				if (!empty($aQUERY['access_token'])) {

					// set session
					self::$oSession->set('access_token', $aQUERY['access_token']);

					return $this->connect(array('code' => $aParams['code'], 'access_token' => $aQUERY['access_token']));
				}
			}
			else {
				throw new BT_FacebookException(FacebookPsConnect::$oModule->l('Internal server error. Facebook access token is empty or the connect method to the Facebook URL with HTTPS is not allowed. Please contact the merchant to warn him', 'facebook-connect_class'), 523);
			}
		}
		else {
			throw new BT_FacebookException(FacebookPsConnect::$oModule->l('The state doesn\'t match. You may be a victim of cross-site request forgery or you decided to cancel your connect processing. Please close this window', 'facebook-connect_class'), 524);
		}
	}

	/**
	 * redirect() method redirect on FB interface
	 */
	protected function redirect()
	{
		// set
		$sState = md5(uniqid(rand(), true));

		// set state session value for a secure response
		self::$oSession->set('state', $sState);

		// set dialog URL
		$sDialogUrl = $this->sFbUri . '&state=' . $sState;

		unset($sState);

		// redirect on fb interface
		header('location:' . $sDialogUrl);
		exit(0);
	}
}