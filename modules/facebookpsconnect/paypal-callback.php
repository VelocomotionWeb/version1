<?php
/**
 * paypal-callback.php file execute module for Front Office
 */

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/facebookpsconnect.php');


// instantiate
$oModule = new FacebookPsConnect();

// execute paypal connector
echo $oModule->HookConnectorCallback(
	array(
		'connector' => 'paypal',
		'activecallback' => true,
		'code' => Tools::getValue('code'),
		'access_token' => Tools::getValue('access_token')
	)
);
