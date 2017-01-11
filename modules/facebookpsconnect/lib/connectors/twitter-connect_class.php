<?php
/**
 * twitter-connector_class.php file defines method for handing connectors
 */


if (!defined('_PS_VERSION_')) {
	exit(1);
}


/**
 * declare TwitterConnect Exception class
 */
class BT_TwitterException extends BT_ConnectorException{}

class BT_TwitterConnect extends BT_BaseConnector
{
	/**
	 * get twitter oauth obj
	 *
	 * @param obj $oTwitterOAuth
	 */
	protected $oTwitterOAuth = null;


	/**
	 * __construct() magic method assign connector keys
	 *
	 * @param array $aParams
	 */
	public function __construct(array $aParams)
	{
		// include abstract connector
		require_once(_FPC_PATH_LIB_CONNECTOR . 'twitter/twitteroauth.php');

		if (!empty($aParams['id'])
			&& !empty($aParams['secret'])
			&& !empty($aParams['callback'])
		) {
			$this->consumer_key = $aParams['id'];
			$this->consumer_secret = $aParams['secret'];
			$this->oauth_callback = $aParams['callback'];

			// set user
			parent::setUser();
		}
		else {
			throw new BT_TwitterException(FacebookPsConnect::$oModule->l('Invalid connector keys', 'twitter-connect_class'), 530);
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
			case 'consumer_key':
			case 'sConsumerKey' :
				$this->consumer_key = is_string($mValue) ? $mValue : NULL;
				break;
			case 'consumer_secret':
			case 'sConsumerSecret' :
				$this->consumer_secret = is_string($mValue) ? $mValue : NULL;
				break;
			case 'oauth_callback':
			case 'sOauthCallback' :
				$this->oauth_callback = is_string($mValue) ? $mValue : NULL;
				break;
			default:
				throw new BT_TwitterException(FacebookPsConnect::$oModule->l('Invalid set keys', 'twitter-connect_class') . $sName, 531);
				break;
		}
	}

	/**
	 * __get() magic method return connector key value
	 *
	 * @param string $sName
	 */
	public function __get($sName)
	{
		$mValue = null;

		switch ($sName) {
			case 'consumer_key' :
			case 'sConsumerKey' :
				$mValue = $this->consumer_key;
				break;
			case 'consumer_secret' :
			case 'sConsumerSecret' :
				$mValue = $this->consumer_secret;
				break;
			case 'oauth_callback' :
			case 'sOauthCallback' :
				$mValue = $this->oauth_callback;
				break;
			default:
				break;
		}
		return $mValue;
	}

	/**
	 * connect() method  check if token is valid or not, and either redirect on Twitter interface or log the customer by creating his account if necessary
	 *
	 * @param array $aParams
	 * @return string
	 */
	public function connect(array $aParams = null)
	{
		// detect if callback requested
		if (empty($aParams['oauth_token']) && empty($aParams['oauth_token_secret'])) {
			// redirect on Twitter interface
			$this->redirect();
		}
		else {
			// set new twitter oauth with token
			$this->getTwitterOAuth($this->consumer_key,$this->consumer_secret, $aParams['oauth_token'], $aParams['oauth_token_secret']);

			// verify credentials
			$oTwitterAccount = $this->oTwitterOAuth->get('account/verify_credentials');

			if (!empty($oTwitterAccount->errors)){
				throw new BT_TwitterException(FacebookPsConnect::$oModule->l('Authentication failed', 'twitter-connect_class'), 532);
			}

			// set create status
			$bCreateStatus  = true;
			$bCreatePs      = false;
			$bCreateSocial  = false;

			// set data
			$this->oUser->id = $oTwitterAccount->id;
			$this->oUser->email = 'twitter' . $oTwitterAccount->id . '@twitter.com';
			$this->oUser->customerId = 0;
			// get last name
			$aName = explode(' ', $oTwitterAccount->name);

			// manage last name with figure
			$aName = preg_replace('`(.*?)(\d+)(.*?)`','$1',$aName);
			if (empty($aName[0]))
			{
				$aName[0] = "generic name";
			}

			// set name
			if (count($aName) != 1) {
				$this->oUser->first_name = $aName[0];
				$this->oUser->last_name = $aName[1];
			}
			else {
				$this->oUser->first_name = $aName[0];
				$this->oUser->last_name = $aName[0];
			}

			// test if user already exist in social table
			$bCreateSocial = !parent::existSocialAccount($this->oUser->id);

			// use case - social account exist
			if (empty($bCreateSocial)) {
				// get PS customer ID
				$iParentId = parent::getCustomerId($this->oUser->id);

				if (!empty($iParentId)) {
					// get PS customer data
					$aCustomerData = parent::getCustomerData($iParentId);

					// if exists set existing customer e-mail address
					if (!empty($aCustomerData[0]['email'])) {
						$this->oUser->email = $aCustomerData[0]['email'];
					}
				}
			}

			// test if user already exist in PS table
			$bCreatePs = !parent::existPsAccount($this->oUser->email);

			// use case - social account exist
			if (empty($bCreateSocial)) {
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
				throw new BT_TwitterException(FacebookPsConnect::$oModule->l('Internal server error. Account creation processing is unavailable', 'twitter-connector_class'), 533);
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
		// use case - test session token and oauth verifier
		if ((!empty($oauth_token) && $aParams['oauth_token'] !== self::$oSession->get('oauth_token')) || empty($aParams['oauth_verifier'])) {
			throw new BT_TwitterException(FacebookPsConnect::$oModule->l('Invalid signed request (verify oauth token & oauth verifier)', 'twitter-connect_class'), 534);
		}

		// set new twitter oauth with token
		$this->getTwitterOAuth($this->consumer_key,$this->consumer_secret,self::$oSession->get('oauth_token'),self::$oSession->get('oauth_token_secret'));

		// get new token
		$aTwitterAccessToken = $this->oTwitterOAuth->getAccessToken($aParams['oauth_verifier']);

		// use case - code 200 OK
		if (200 == $this->oTwitterOAuth->http_code) {
			 return $this->connect($aTwitterAccessToken);
		}
		// use case - redirect
		else {
			$this->redirect();
		}
	}

	/**
	 * redirect() method redirect on twitter interface
	 *
	 * @category connector collection
	 * @uses
	 *
	 */
	protected function redirect()
	{
		// instantiate twitter oauth object
		$this->getTwitterOAuth($this->consumer_key, $this->consumer_secret);

		// get access token
		$aRequestToken = $this->oTwitterOAuth->getRequestToken($this->oauth_callback);

		// check token confirmation
		if (empty($aRequestToken['oauth_callback_confirmed'])) {
			throw new BT_TwitterException(FacebookPsConnect::$oModule->l('Invalid consumer key or consumer secret', 'twitter-connect_class'), 535);
		}

		self::$oSession->set('oauth_token',$aRequestToken['oauth_token']);
		self::$oSession->set('oauth_token_secret',$aRequestToken['oauth_token_secret']);

		$sTwitterUrl = $this->oTwitterOAuth->getAuthorizeURL($aRequestToken['oauth_token']);

		if (200 == $this->oTwitterOAuth->http_code) {
			// redirect on twitter interface
			header('location:' . $sTwitterUrl);
			exit(0);
		}
		else {
			throw new BT_TwitterException(FacebookPsConnect::$oModule->l('Authorized URL failed', 'twitter-connect_class'), 536);
		}
	}

	/**
	 * getTwitterOAuth() method instantiate twitter oauth obj
	 *
	 * @category connector collection
	 * @uses
	 *
	 * @param int $iConsumerKey
	 * @param string $sConsumerSecret
	 * @param string $sOauthToken
	 * @param string $sOauthTokenSecret
	 */
	protected function getTwitterOAuth($iConsumerKey, $sConsumerSecret, $sOauthToken = null, $sOauthTokenSecret = null)
	{
		$this->oTwitterOAuth = new TwitterOAuth($iConsumerKey, $sConsumerSecret, $sOauthToken, $sOauthTokenSecret);
	}
}