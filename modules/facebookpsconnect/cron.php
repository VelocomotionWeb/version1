<?php
/**
* cron.php file execute
*/

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/facebookpsconnect.php');

$oModule = new FacebookPsConnect();

// use case - send action
$_POST['sAction'] = 'send';

// email type
$_POST['sType'] = 'callback';

echo $oModule->getContent();