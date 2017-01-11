<?php
/**
 * google-connector_class.php file defines method for handing connectors
 */


if (!defined('_PS_VERSION_')) {
	exit(1);
}


/**
 * declare Google Exception class
 */
class BT_GoogleException extends BT_ConnectorException{}

class BT_GoogleConnect extends BT_BaseConnector
{
	/**
	 * get Google client obj
	 *
	 * @param obj $oGoogleClient
	 */
	protected $oGoogleClient = null;

	/**
	 * get Google oauth obj
	 *
	 * @param obj $oGoogleOAuth
	 */
	protected $oGoogleOAuth = null;

	/**
	 * get Google Auth URL
	 *
	 * @param string $sAuthUrl
	 */
	protected $sAuthUrl;

	/**
	 * __construct() magic method assign connector keys
	 *
	 * @param array $aParams
	 */
	public function __construct(array $aParams)
	{

		// include abstract connector
		require_once(_FPC_PATH_LIB_CONNECTOR . 'google/Google_Client.php');
		require_once(_FPC_PATH_LIB_CONNECTOR . 'google/contrib/Google_Oauth2Service.php');

		if (!empty($aParams['id'])
			&& !empty($aParams['secret'])
			&& !empty($aParams['developerKey'])
			&& !empty($aParams['callback'])
		) {
			$this->consumer_id = $aParams['id'];
			$this->consumer_secret = $aParams['secret'];
			$this->developer_key = $aParams['developerKey'];
			$this->oauth_callback = $aParams['callback'];

			// set user
			parent::setUser();
		}
		else {
			throw new BT_GoogleException(FacebookPsConnect::$oModule->l('Invalid connector keys', 'google-connect_class'), 540);
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
			case 'consumer_id':
			case 'sConsumerId' :
				$this->consumer_id = is_string($mValue) ? $mValue : NULL;
				break;
			case 'consumer_secret':
			case 'sConsumerSecret' :
				$this->consumer_secret = is_string($mValue) ? $mValue : NULL;
				break;
			case 'developer_key':
			case 'sDeveloperKey' :
				$this->developer_key = is_string($mValue) ? $mValue : NULL;
				break;
			case 'oauth_callback':
			case 'sOauthCallback' :
				$this->oauth_callback = is_string($mValue) ? $mValue : NULL;
				break;
			default:
				throw new BT_GoogleException(FacebookPsConnect::$oModule->l('Invalid set keys ', 'google-connect_class') . $sName, 541);
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
			case 'consumer_key' :
			case 'sConsumerKey' :
				return $this->consumer_key;
				break;
			case 'consumer_secret' :
			case 'sConsumerSecret' :
				return $this->consumer_secret;
				break;
			case 'developer_key' :
			case 'sDeveloperKey' :
				return $this->developer_key;
				break;
			case 'oauth_callback' :
			case 'sOauthCallback' :
				return $this->oauth_callback;
				break;
			default:
				break;
		}
		return null;
	}

	/**
	 * connect() method check if code is valid or not, and either redirect on Google interface or log the customer by creating his account if necessary
	 *
	 * @param array $aParams
	 * @return string
	 */
	public function connect(array $aParams = null)
	{
		// Instantiate Google objects
		$this->getGoogleObj();

		if ( empty($aParams['code']) ) {

			// Redirect to Google Interface
			$this->redirect();
		}
		else {
			$this->oGoogleClient->authenticate($aParams['code']);
			self::$oSession->set('code', $this->oGoogleClient->getAccessToken());
			$this->oGoogleClient->setAccessToken(self::$oSession->get('code'));

			// get response
			$response = $this->oGoogleClient->getAccessToken(self::$oSession->get('code'));

			// use case - valid response
			if ( $response ) {

				// set create status
				$bCreateStatus = true;
				$bCreatePs = false;
				$bCreateSocial = false;

				// get user info
				$aGoogleAccount = $this->oGoogleOAuth->userinfo->get();

				// set data
				$this->oUser->id = $aGoogleAccount['id'];
				$this->oUser->email = $aGoogleAccount['email'];
				$this->oUser->customerId = 0;

				// get last name
				$aName = explode(' ', $aGoogleAccount['name']);

				// set name
				$this->oUser->first_name = $aName[0];
				$this->oUser->last_name = $aName[1];

				// set birthday
				if (!empty($aGoogleAccount['birthday'])) {
					// format date for PS customer table
					$this->oUser->birthday = $aGoogleAccount['birthday'];
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
				else {
					throw new BT_GoogleException(FacebookPsConnect::$oModule->l('Internal server error. Account creation processing is unavailable', 'google-connector_class'), 542);
				}
			}
			else {
				throw new BT_GoogleException(FacebookPsConnect::$oModule->l('Unable to get access token', 'google-connect_class'), 543);
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
		// instantiate Google objects
		$this->getGoogleObj();

		// Check if received token is correct
		if (empty($aParams['code'])) {
			throw new BT_GoogleException(FacebookPsConnect::$oModule->l('Can\'t get valid token code from Google!', 'google-connect_class'), 544);
		}

		return $this->connect(array('code' => $aParams['code']));
	}

	/**
	 * redirect() method redirect on google interface
	 */
	protected function redirect()
	{
		$this->setAuthUrl();
		if ( empty($this->sAuthUrl) ) {
			throw new BT_GoogleException(FacebookPsConnect::$oModule->l('Authentication URL is not valid!', 'google-connect_class'), 545);
		}
		header('Location:' . $this->sAuthUrl);
		exit(0);
	}


	/**
	 * getGoogleObj method instantiates Google classes : Client & OAuth Objects
	 */
	protected function getGoogleObj()
	{
		// Instantiates Google Client Object
		$this->oGoogleClient = new Google_Client();

		$this->oGoogleClient->setApplicationName('Faccebook Connect');
		$this->oGoogleClient->setClientId($this->consumer_id);
		$this->oGoogleClient->setClientSecret($this->consumer_secret);
		$this->oGoogleClient->setRedirectUri($this->oauth_callback);
		$this->oGoogleClient->setDeveloperKey($this->developer_key);

		// Instantiates Google OAuth Object
		$this->oGoogleOAuth = new Google_Oauth2Service($this->oGoogleClient);
	}


	/**
	 * getAuthUrl method create the url needed by the client to authenticate
	 */
	protected function setAuthUrl()
	{
		$this->sAuthUrl = $this->oGoogleClient->createAuthUrl();
	}
}