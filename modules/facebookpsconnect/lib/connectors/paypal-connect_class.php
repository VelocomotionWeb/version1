<?php
/**
 * Paypal-connector_class.php file defines method for handing connectors
 */


if (!defined('_PS_VERSION_')) {
	exit(1);
}


/**
 * declare PaypalConnect Exception class
 */
class BT_PaypalException extends BT_ConnectorException{}

class BT_PaypalConnect extends BT_BaseConnector
{
	/**
	 * get Paypal oauth obj
	 *
	 * @param obj $oPaypalOAuth
	 */
	protected $oPaypalOAuth = null;


	/**
	 * __construct() magic method assign connector keys
	 *
	 * @param array $aParams
	 */
	public function __construct(array $aParams)
	{

		if (!empty($aParams['id'])
			&& !empty($aParams['secret'])
			&& !empty($aParams['callback'])
		) {
			$this->appId = $aParams['id'];
			$this->secret = $aParams['secret'];
			$this->callback = $aParams['callback'];

			// set user
			parent::setUser();
		}
		else {
			throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Invalid connector keys', 'paypal-connect_class'), 522);
		}
	}


	/**
	 * __set() magic method assign connector keys
	 *
	 * @param string $sName
	 * @param mixed $mValue
	 */
	public function __set($sName, $mValue)
	{

		switch($sName){
			case 'appId':
			case 'sAppId' :
				$this->appId = is_string($mValue) ? $mValue : NULL;
				break;
			case 'secret':
			case 'sSecret' :
				$this->secret = is_string($mValue) ? $mValue : NULL;
				break;
			case 'callback' :
			case 'sCallback' :
				$this->callback = is_string($mValue) ? $mValue : NULL;
				break;
			default:
				throw new BT_ConnectorException(FacebookPsConnect::$oModule->l('Invalid set keys', 'Paypal-connect_class') . $sName, 520);
				break;
		}

	}

	/**
	 * __get() magic method return connector keys
	 *
	 * @param string $sName
	 */
	public function __get($sName)
	{
		switch ($sName) {
			case 'appId' :
			case 'sAppId' :
				return $this->appId;
				break;
			case 'secret' :
			case 'sSecret' :
				return $this->secret;
				break;
			default:
				break;
		}
		return null;
	}

	/**
	 * connect() method check if token is valid or not, and either redirect on Paypal interface or log the customer by creating his account if necessary
	 *
	 * @param array $aParams
	 * @return string
	 */
	public function connect(array $aParams = null)
	{
		// detect if callback requested
		if (empty($aParams['access_token'])){
			$this->redirect();
		}
		else {
			// get profile data
			$oPaypalAccount = $this->getProfile($aParams);

			// set create status
			$bCreateStatus = true;
			$bCreatePs = false;
			$bCreateSocial = false;

			// set ID from email because there is no ID returned by Paypal
			$this->oUser->id = md5($oPaypalAccount->email);
			$this->oUser->email = $oPaypalAccount->email;
			$this->oUser->customerId = 0;

			// set name
			$this->oUser->first_name = $oPaypalAccount->given_name;
			$this->oUser->last_name = $oPaypalAccount->family_name;

			// set birthday
			if (!empty($oPaypalAccount->birthday)) {
				// format date for PS customer table
				$this->oUser->birthday = $oPaypalAccount->birthday;
			}

			// set customer address
			if (is_object($oPaypalAccount->address) && !empty($oPaypalAccount->address)) {
				// create a default object for address details
				$this->oUser->address = new stdClass();
				// set address data
				$this->oUser->address->id_country       = parent::getCountryID($oPaypalAccount->address->country);
				$this->oUser->address->id_state         = !empty($oPaypalAccount->address->region) ? parent::getStateId($this->oUser->address->id_country, $oPaypalAccount->address->region) : parent::$aRequiredAddressFields['id_state'];
				$this->oUser->address->id_manufacturer  = parent::$aRequiredAddressFields['id_manufacturer'];
				$this->oUser->address->id_supplier      = parent::$aRequiredAddressFields['id_supplier'];
				$this->oUser->address->id_warehouse     = parent::$aRequiredAddressFields['id_warehouse'];
				$this->oUser->address->alias            = FacebookPsConnect::$oModule->l('My address', 'paypal-connect_class');
				$this->oUser->address->company          = '';
				$this->oUser->address->lastname         = $this->oUser->last_name;
				$this->oUser->address->firstname        = $this->oUser->first_name;

				// get explode street address
				$aStreet = parent::concatAddress($oPaypalAccount->address->street_address);

				$this->oUser->address->address1     = $aStreet['line1'];
				$this->oUser->address->address2     = $aStreet['line2'];
				$this->oUser->address->postcode     = $oPaypalAccount->address->postal_code;
				$this->oUser->address->city         = $oPaypalAccount->address->locality;
				$this->oUser->address->other        = parent::$aRequiredAddressFields['other'];
				$this->oUser->address->phone        = !empty($oPaypalAccount->phone_number)? $oPaypalAccount->phone_number : parent::$aRequiredAddressFields['phone'];
				$this->oUser->address->phone_mobile = !empty($oPaypalAccount->phone_number)? $oPaypalAccount->phone_number : parent::$aRequiredAddressFields['phone_mobile'];
				$this->oUser->address->vat_number   = parent::$aRequiredAddressFields['vat_number'];
				$this->oUser->address->dni          = parent::$aRequiredAddressFields['dni'];
				$this->oUser->address->date_add     = date('Y-m-d H:i:s', time());
				$this->oUser->address->date_upd     = date('Y-m-d H:i:s', time());
				$this->oUser->address->active       = parent::$aRequiredAddressFields['active'];
				$this->oUser->address->deleted      = parent::$aRequiredAddressFields['deleted'];

				unset($sStreetLine1);
				unset($sStreetLine2);
			}

			// test if user already exist in social table
			$bCreateSocial = !parent::existSocialAccount($this->oUser->id);

			// test if user already exist in PS table
			$bCreatePs = !parent::existPsAccount($this->oUser->email);

			// use case - social account exist
			if (!$bCreateSocial) {
				// use case - create new PS account and have to delete old social account
				if (!empty($bCreatePs)) {
					parent::deleteSocialAccount($this->oUser->id);
					$bCreateSocial = true;
				}
			}
			// social account do not exist and PS account exists
			elseif (empty($bCreatePs)) {
				$this->oUser->customerId = Customer::customerExists($this->oUser->email, true);
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

				return $this->login();
			}
		}
	}

	/**
	 * callback() method sign the oauth token and call connect()
	 *
	 * @param array $aParams
	 * @return string
	 */
	public function callback(array $aParams = null)
	{
		$oToken = $this->getAccessToken($aParams['code']);

		if (!empty($oToken->errors)){
			throw new BT_PaypalException($oToken->error_description, 522);
		}

		return $this->connect((array)$oToken);
	}


	/**
	 * redirect() method redirect on Paypal interface
	 */
	protected function redirect()
	{
		$sAuthUrl = sprintf("%s?client_id=%s&response_type=code&scope=%s&redirect_uri=%s&nonce=%s&state=%s",
			'https://www.paypal.com/webapps/auth/protocol/openidconnect/v1/authorize',
			$this->appId,
			'profile email address phone openid https://uri.paypal.com/services/paypalattributes',
			urlencode($this->callback),
			time() . rand(),
			$this->state);

		//forward user to PayPal auth page
		header("Location: $sAuthUrl");
		exit(0);
	}

	/**
	 * getAccessToken() method returns a valid token
	 *
	 * After the user is forwarded back to the application callback (defined in
	 * the application at devportal.x.com) and the code parameter is available on
	 * the query string, exchange the code parameter for an access token.
	 *
	 */
	public function getAccessToken($code)
	{
		$sPostVal = sprintf("client_id=%s&client_secret=%s&grant_type=authorization_code&code=%s&redirect_uri=%s",
			$this->appId,
			$this->secret,
			$code,
			urlencode($this->callback)
		);
		return (
			BT_FPCModuleTools::jsonDecode($this->runCurl('https://api.paypal.com/v1/identity/openidconnect/tokenservice', 'POST', $sPostVal))
		);
	}

	/**
	 * getProfile() method returns the customer's profile
	 *
	 *
	 * @param array $aToken
	 * @return array
	 */
	public function getProfile(array $aToken)
	{
		$sProfileUrl = sprintf("%s?schema=openid&access_token=%s",
			"https://api.paypal.com/v1/identity/openidconnect/userinfo/",
			$aToken['access_token']);

		return (
			BT_FPCModuleTools::jsonDecode($this->runCurl($sProfileUrl))
		);
	}

	/**
	 * runCurl() method execute cURL
	 *
	 * @param string $sUrl
	 * @param string $sMethod
	 * @param mixed $mPost
	 * @return array
	 */
	protected function runCurl($sUrl, $sMethod = 'GET', $mPost = array())
	{
		$hcUrl = curl_init($sUrl);

		//GET request: send headers and return data transfer
		if ($sMethod == 'GET'){
			$aOptions = array(
				CURLOPT_URL => $sUrl,
				CURLOPT_RETURNTRANSFER => 1,
			);
			curl_setopt_array($hcUrl, $aOptions);
			//POST / PUT request: send post object and return data transfer
		}
		else {
			$aOptions = array(
				CURLOPT_URL => $sUrl,
				CURLOPT_POST => 1,
				CURLOPT_VERBOSE => 1,
				CURLOPT_POSTFIELDS => $mPost,
				CURLOPT_RETURNTRANSFER => 1
			);
			curl_setopt_array($hcUrl, $aOptions);
		}

		$response = curl_exec($hcUrl);
		curl_close($hcUrl);

		return $response;
	}
}