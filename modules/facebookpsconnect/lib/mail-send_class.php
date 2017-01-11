<?php
/**
 * mail-send_class.php file defines all method to send email
 */

class BT_FpcMailSend
{
	/**
	 * @var bool $bProcess : define if process or not
	 */
	protected $bProcess = null;

	/**
	 * Magic Method __construct
	 */
	public function __construct(){

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
	 * @param string $sType
	 * @param array $aParams
	 * @return bool
	 */
	public function run($sType, array $aParams)
	{
		$bSend = false;
		$this->bProcess = false;

		switch ($sType) {
			case 'customerAccountNotification' : // use case - send a notification to the customer
				$aParams = $this->_processCustomerNotification($aParams);
				break;
			case 'customerTwitterNotification' : // use case - send a notification to the customer
				$aParams = $this->_processCustomerTwitterNotification($aParams);
				break;
			default :
				break;
		}

		// use case - only if process true
		if ($this->bProcess) {
			$bSend = Mail::send($aParams['isoId'], $aParams['tpl'], $aParams['subject'], $aParams['vars'], $aParams['email']);
		}

		return $bSend;
	}


	/**
	 * _processCustomerNotification() method process data for sending an e-mail notification to the customer once his account is created
	 *
	 * @param array $aData
	 * @return array
	 */
	private function _processCustomerNotification(array $aData)
	{
		$aParams = array();

		// set the subject / email / lang ID
		$aParams['subject'] = FacebookPsConnect::$oModule->l('Your account has been created', 'mail-send_class');
		$aParams['email']   = $aData['email'];
		$aParams['isoId']   = !empty($aData['isoId'])? $aData['isoId'] : Configuration::get('PS_LANG_DEFAULT');
		$aParams['tpl']     = 'account';
		$aParams['vars']    = array(
			'{email}' 	    => $aData['email'],
			'{passwd}' 	    => $aData['password'],
			'{firstname}' 	=> $aData['firstname'],
			'{lastname}' 	=> $aData['lastname'],
		);

		$this->bProcess = true;

		return $aParams;
	}

	/**
	 * _processCustomerTwitterNotification() method send customer information after update with twitter connector
	 *
	 * @param array $aData
	 * @return array
	 */
	private function _processCustomerTwitterNotification($aData)
	{
		// set the subject / email / lang ID
		$aParams['subject'] = FacebookPsConnect::$oModule->l('Your account has been updated', 'mail-send_class');
		$aParams['email']   = $aData['email'];
		$aParams['isoId']   = !empty($aData['isoId'])? $aData['isoId'] : Configuration::get('PS_LANG_DEFAULT');
		$aParams['tpl']     = 'account';
		$aParams['vars']    = array(
			'{email}' 	    => $aData['email'],
			'{passwd}' 	    => $aData['password'],
			'{firstname}' 	=> $aData['firstname'],
			'{lastname}' 	=> $aData['lastname'],
		);

		$this->bProcess = true;

		return $aParams;
	}


	/**
	 * create() method set singleton
	 *
	 * @return obj
	 */
	public static function create()
	{
		static $oMailSend;

		if (null === $oMailSend) {
			$oMailSend = new BT_FpcMailSend();
		}
		return $oMailSend;
	}
}