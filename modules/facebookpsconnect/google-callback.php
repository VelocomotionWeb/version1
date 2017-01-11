<?php
/**
 * google-callback.php file execute module for Front Office
 */

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/facebookpsconnect.php');


// instantiate
$oModule = new FacebookPsConnect();

// execute google connector
echo $oModule->HookConnectorCallback(array('connector' => 'google', 'activecallback' => true, 'code' => Tools::getValue('code')));