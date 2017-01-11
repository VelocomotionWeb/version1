<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

error_reporting(E_ALL | E_STRICT);

/* will include module file */
include_once('../../../../../UploadHandler.php');
include_once('../../../../../../../config/config.inc.php');
include_once('../../../../../../../config/settings.inc.php');
include_once('../../../../../../../classes/Cookie.php');

session_start();
if(time() - $_SESSION['lastActiveUpload'] < 10*60){
	$cookie=$_SESSION['cookie'];
	if(($cookie->id_employee != null OR $cookie->id_customer !=null) AND
			in_array($cookie->profile, array("1", "2", "3"))){
		$uploadHandler = new UploadHandler();
	}else{
		$file = new stdClass();
		$file->error="Not Allow for unauthenticated user, please login again or refresh page";
		$file->name="";
		$file->size=0;
		$file->type="";
		echo json_encode($file);
	
	}
}else{
		session_unset();     // unset $_SESSION variable for the run-time
		session_destroy();   // destroy session data in storage
		$file = new stdClass();
		$file->error="Not Allow for unauthenticated user, please login again or refresh page";
		$file->name="";
		$file->size=0;
		$file->type="";
		echo json_encode($file);
	
	
}



