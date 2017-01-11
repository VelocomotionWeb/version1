<?php
/**
 * amazon-connector_class.php file defines method for handing connectors
 */

if (!defined('_PS_VERSION_')) {
	exit(1);
}


/**
 * declare AmazonConnect Exception class
 */
class BT_AmazonException extends BT_ConnectorException{}

class BT_AmazonConnect extends BT_BaseConnector
{
	/**
	 * @var string $sAmazonAuthorizeUri : AMAZON AUTHORIZE URI
	 */
	public $sAmazonAuthorizeUri = 'https://www.amazon.com/ap/oa';

	/**
	 * @var string $sAmazonTokenUri : AMAZON TOKEN URI
	 */
	public $sAmazonTokenUri = 'https://api.amazon.com/auth/o2/token';

	/**
	 * @var string $sAmazonProfileUri : AMAZON PROFILE URI
	 */
	public $sAmazonProfileUri = 'https://api.amazon.com/user/profile';

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

			// format the good GET URL Request
			$this->sAmazonAuthorizeUri .= '?client_id=' . $this->appId  . '&redirect_uri=' . self::$sModuleURI . '&scope=profile';

			// set user
			parent::setUser();
		}
		else {
			throw new BT_AmazonException(FacebookPsConnect::$oModule->l('Invalid connector keys', 'amazon-connect_class'), 520);
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
				throw new BT_AmazonException(FacebookPsConnect::$oModule->l('Invalid set keys', 'amazon-connect_class') . $sName, 521);
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
	 * connect() method check if token is valid or not, and either redirect on Amazon interface or log the customer by creating his account if necessary
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
			$oAmazonUser = BT_FPCModuleTools::jsonDecode(BT_FPCModuleTools::fileGetContent($this->sAmazonProfileUri . '?access_token=' . $aParams['access_token']));

			// only if social user id exist
			if (!empty($oAmazonUser->user_id)) {
				// set create status
				$bCreateStatus = true;
				$bCreatePs = false;
				$bCreateSocial = false;

				// set Amazon data
				$this->oUser->id = $oAmazonUser->user_id;
				$this->oUser->customerId = 0;
				$this->oUser->email = $oAmazonUser->email;

				// get last name
				if (strstr($oAmazonUser->name, ' ')) {
					$aName = explode(' ', $oAmazonUser->name);
				}
				else {
					$aName = array();
					$aName[0] = $oAmazonUser->name;
					$aName[1] = $oAmazonUser->name;
				}

				// set name
				$this->oUser->first_name = $aName[0];
				$this->oUser->last_name = $aName[1];

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

					// get data session if exists
					$sData = self::$oSession->get('data');

					// delete session
					self::$oSession->delete('data');

					return $this->login($sData);
				}
				else {
					throw new BT_AmazonException(FacebookPsConnect::$oModule->l('Internal server error. Account creation processing is unavailable', 'amazon-connect_class'), 523);
				}
			}
			else {
				if (!empty($oAmazonUser->error)) {
					$sMsg = $oAmazonUser->error_description;
				}
				else {
					$sMsg = FacebookPsConnect::$oModule->l('The token is not valid. You may be a victim of cross-site request forgery or the connect method to the Amazon URL with HTTPS is not allowed. Please contact the merchant to warn him', 'amazon-connect_class');
				}
				throw new BT_AmazonException($sMsg, 524);
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
		if ((!empty($aParams['state'])
			&&	self::$oSession->get('state') == $aParams['state'])
			&&	!empty($aParams['code'])
		) {
			// get oauth_token
			$aOauthParams = array(
				'content' => array(
					'grant_type' => 'authorization_code',
					'code' => $aParams['code'],
					'client_id' => $this->appId,
					'client_secret' => $this->secret,
					'redirect_uri' => self::$sModuleURI,
				),
			);
			$oJsonResponse = BT_FPCModuleTools::execHttpRequest($this->sAmazonTokenUri, $aOauthParams);

			if (!empty($oJsonResponse)) {
				// decode the response
				$oResponse = BT_FPCModuleTools::jsonDecode($oJsonResponse);

				if (!empty($oResponse->access_token)) {
					// set session
					self::$oSession->set('access_token', $oResponse->access_token);

					return $this->connect(array('code' => $aParams['code'], 'access_token' => $oResponse->access_token));
				}
				else {
					if (!empty($oResponse->error)) {
						$sMsg = $oResponse->error_description;
					}
					else {
						$sMsg = FacebookPsConnect::$oModule->l('Internal server error. Amazon access token is empty or the connect method to the Amazon URL with HTTPS is not allowed. Please contact the merchant to warn him', 'amazon-connect_class');
					}
					throw new BT_AmazonException($sMsg, 525);
				}
			}
			else {
				throw new BT_AmazonException(FacebookPsConnect::$oModule->l('Internal server error. Amazon access token is empty or the connect method to the Amazon URL with HTTPS is not allowed. Please contact the merchant to warn him', 'amazon-connect_class'), 526);
			}
		}
		else {
			throw new BT_AmazonException(FacebookPsConnect::$oModule->l('The state doesn\'t match. You may be a victim of cross-site request forgery or you decided to cancel your connect processing. Please close this window', 'amazon-connect_class'), 527);
		}
	}


	/**
	 * redirect() method redirect on Amazon interface
	 */
	protected function redirect()
	{
		// set
		$sState = md5(uniqid(rand(), true));

		// set state session value for a secure response
		self::$oSession->set('state', $sState);

		// set dialog URL
		$sDialogUrl = $this->sAmazonAuthorizeUri . '&response_type=code&state=' . $sState;

		unset($sState);

		// redirect on amazon interface
		header('location:' . $sDialogUrl);
		exit(0);
	}
}