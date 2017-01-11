<?php
/**
 * warnings_class.php file defines method for detecting warnings and display
 */

class BT_FPCWarning
{
	/**
	 * var $bStopExecution : defines if execution has to be stopped
	 */
	public $bStopExecution = false;


	/**
	 * run() method detect warnings and display them
	 */
	public function run($sType, $mValue, array $aParams = array(), $bStop = false)
	{
		$bWarning = false;

		switch ($sType) {
			case 'configuration' :
				if (!Configuration::get($mValue)) {
					$bWarning = true;
				}
				break;
			case 'directive' :
				if (!ini_get($mValue) ){
					$bWarning = true;
				}
				break;
			case 'module' :
				// get module's vars to check
				$aModuleVars = (!empty($aParams['vars']) && is_array($aParams['vars'])) ? $aParams['vars'] : array();

				// if only activated
				$bActivatedOnly = !empty($aParams['installed']) ? $aParams['installed'] : false;

				if (!BT_FPCModuleTools::isInstalled($mValue, $aModuleVars, false, $bActivatedOnly)) {
					$bWarning = true;
				}
				break;
			case 'function' :
				if (!function_exists($mValue)) {
					$bWarning = true;
				}
				break;
			case 'callback' :
				$mReturn = call_user_func_array($mValue, array($aParams));

				if ($mReturn) {
					$bWarning = true;
				}
				break;
			case 'file-permission' :
				// use case - check file permission
				if (!is_writable($mValue)) {
					$bWarning = true;
				}
				break;
			default:
				$bWarning = false;
				break;
		}

		if ($bWarning && $bStop) {
			$this->bStopExecution = true;
		}

		return $bWarning;
	}

	/**
	 * create() method manages singleton
	 *
	 * @return array
	 */
	public static function create()
	{
		static $oWarning;

		if( null === $oWarning) {
			$oWarning = new BT_FPCWarning();
		}
		return $oWarning;
	}
}