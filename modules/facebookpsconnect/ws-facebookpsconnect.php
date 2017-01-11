<?php
/**
 * ws-facebookpsconnect.php file execute module for Front Office
 */

require_once(dirname(__FILE__) . '/../../config/config.inc.php');
require_once(dirname(__FILE__) . '/../../init.php');
require_once(dirname(__FILE__) . '/facebookpsconnect.php');

// get type of content to display
$sAction = Tools::getIsset('sAction') ? Tools::getValue('sAction') : '';
$sType = Tools::getIsset('sType') ? Tools::getValue('sType') : '';

$sUseCase = $sAction . $sType;

// instantiate
$oModule = new FacebookPsConnect();

switch ($sUseCase) {
	case 'connectplugin' :
		// exec matched connector
		echo $oModule->HookConnectorConnect(array_merge($_GET, $_POST));
		break;
//    case 'cltscl' : // collect social data from widget
//        if (!empty($_POST['cn'])
//                &&
//            array_key_exists(base64_decode($_POST['cn']), $GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'])
//        ) {
//            // collect FB data
//            echo $oModule->HookSocialCollector(base64_decode($_POST['cn']), $_POST);
//        }
//        break;
	case 'updatecustomer' :
		// collect FB data
		echo $oModule->HookCustomerAssociation($_POST);
		break;
	case 'updateemail' :
		// collect FB data
		echo $oModule->HookCustomerEmail($_POST);
		break;
	default:
		break;
}