<?php
/**
 * connector-ctrl_class.php file defines controller which manage type of hook object derived of abstract type as factory pattern
 */

class BT_FPCConnectorCtrl
{
	/**
	 * @var obj $_sConnectorType : defines connector used
	 */
	private $_sConnectorType = null;

	/**
	 * Magic Method __construct assigns few information about module and instantiate parent class
	 *
	 * @param string $sType : type of connector to execute
	 */
	public function __construct($sType)
	{
		// include abstract connector
		require_once(_FPC_PATH_LIB_CONNECTOR . 'base-connector_class.php');

		$this->_sConnectorType = $sType;
	}

	/**
	 * run() method execute hook
	 *
	 * @param array $aParams
	 * @return array
	 */
	public function run(array $aParams)
	{
		$aAssign = array();

		try {
			// get connector
			$oConnector = BT_BaseConnector::get($this->_sConnectorType, $aParams);

			// first connection
			if (empty($aParams['activecallback'])) {
				$mContent = $oConnector->connect();
			}
			else {
				// exec callback for some connectors which need to use redirection and callback
				$mContent = $oConnector->callback($aParams);
			}
			$aAssign['content'] = $mContent;
		}
		catch (BT_ConnectorException $e) {
			$aAssign['aErrors'][] = array('msg' => $e->getMessage(), 'code' => $e->getCode());
			$aAssign['sErrorInclude'] = BT_FPCModuleTools::getTemplatePath(_FPC_PATH_TPL_NAME . _FPC_TPL_HOOK_PATH . _FPC_TPL_ERROR);
		}
		return $aAssign;
	}
}