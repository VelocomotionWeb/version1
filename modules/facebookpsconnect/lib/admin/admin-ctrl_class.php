<?php
/**
 * admin-ctrl_class.php file defines controller which manage type of derived admin object of abstract type as factory pattern
 */

class BT_AdminCtrl
{  
	/**
	 * Magic Method __construct
	 */
	public function __construct()
	{
		
	}

	/**
	 * Magic Method __destruct
	 */
	public function __destruct()
	{

	}


	/**
	 * run() method execute abstract derived admin object
	 *
	 * @param string $sAdminType : type of interface to display
	 * @param array $aRequest : request
	 * @return array $aDisplay : empty => false / not empty => true
	 */
	public function run($sAdminType, $aRequest)
	{
		// set
		$aDisplay = array();

		// include interface
		require_once(_FPC_PATH_LIB_ADMIN . 'i-admin.php');

		switch ($sAdminType) {
			case 'display' :
				// include matched admin object
				require_once(_FPC_PATH_LIB_ADMIN . 'admin-display_class.php');
				
				$oAdminType = BT_AdminDisplay::create();
				break;
			case 'update'   : // update basic settings / 
				// include matched admin object
				require_once(_FPC_PATH_LIB_ADMIN . 'admin-update_class.php');

				$oAdminType = BT_AdminUpdate::create();
				break;
			default :
				$oAdminType = false;
				break;
		}
		
		// process data to use in view (tpl)
		if (!empty($oAdminType)) {
			$aDisplay = $oAdminType->run($aRequest);

			// destruct
			unset($oAdminType);
		}

		return $aDisplay;
	}
}