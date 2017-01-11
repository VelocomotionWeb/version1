<?php
global $smarty;
include( '../../config/config.inc.php' );
require 'src/facebook.php';

	$fb_connect_appid = Configuration::get('PS_FBCONNECT_APPID');
	$fb_connect_secret = Configuration::get('PS_FBCONNECT_SECRET');
	if ($fb_connect_appid=='' or $fb_connect_secret=='') {
		return false;
	} else {
		$facebook = new Facebook(array(
		  'appId'  => $fb_connect_appid,
		  'secret' => $fb_connect_secret,
		));
		$facebook->destroySession();
		Tools::redirect('index.php?mylogout=');
	}
?>