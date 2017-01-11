<?php
/**
 * twitter-callback.php file execute module for Front Office
 */

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/facebookpsconnect.php');


// instantiate
$oModule = new FacebookPsConnect();

// execute twitter connector
echo $oModule->HookConnectorCallback(array('connector' => 'twitter', 'activecallback' => true, 'oauth_token' => Tools::getValue('oauth_token'), 'oauth_verifier' => Tools::getValue('oauth_verifier')));