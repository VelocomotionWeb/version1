<?php
/**
 * admin.conf.php file defines all needed constants and variables for admin context
 */

/*
 * include common conf
 */
require_once(dirname(__FILE__) . '/common.conf.php');

/*
 * defines modules support mail
 */
define('_FPC_MAIL', 'http://www.businesstech.fr/en/contact-us');

/*
 * defines admin library path
 * uses => to include class files
 */
define('_FPC_PATH_LIB_ADMIN', _FPC_PATH_LIB . 'admin/');

/*
 * defines constant of connector path tpl
 * uses => to set good absolute path
 */
define('_FPC_TPL_CONNECTOR_PATH', 'connectors-form/');

/*
 * defines admin logs path
 * uses => to write log files
 */
define('_FPC_PATH_LOGS', _FPC_PATH_ROOT . 'logs/');

/*
 * defines admin path tpl
 * uses => to set good absolute path
 */
define('_FPC_TPL_ADMIN_PATH', 'admin/');

/*
 * defines body tpl
 * uses => with display admin interface
 */
define('_FPC_TPL_BODY', 'body.tpl');

/*
 * defines basic settings tpl
 * uses => with display admin interface
 */
define('_FPC_TPL_BASIC_SETTINGS', 'basic-settings.tpl');

/*
 * defines connectors settings tpl
 * uses => with display admin interface
 */
define('_FPC_TPL_CONNECTOR_SETTINGS', 'connector-settings.tpl');

/*
 * defines hook settings tpl
 * uses => with display admin interface
 */
define('_FPC_TPL_HOOK_SETTINGS', 'hook-settings.tpl');

/*
 * defines hook form  tpl
 * uses => with display admin interface
 */
define('_FPC_TPL_HOOK_FORM', 'hook-form.tpl');

/*
 * defines system health settings tpl
 * uses => with display admin interface
 */
define('_FPC_TPL_SYS_HEALTH_SETTINGS', 'system-health-settings.tpl');

/*
 * defines curl ssl tpl
 * uses => with display admin interface
 */
define('_FPC_TPL_CURL_SSL', 'curl-ssl.tpl');

/*
 * defines constant of body tpl
 * uses => in display admin interface with connector form
 */
define('_FPC_TPL_PREREQUISITES_CHECK_SETTINGS', 'prerequisites-check.tpl');

/*
 * defines constant of body tpl
 * uses => in display admin interface with connector form
 */
define('_FPC_TPL_CONNECTOR_BODY', _FPC_TPL_CONNECTOR_PATH . 'body.tpl');

/*
 * defines constant for external BT API URL
 * uses => with display admin interface
 */
define('_FPC_BT_API_MAIN_URL', '//api.businesstech.fr/prestashop-modules/');

/*
 * defines constant for external BT API URL
 * uses => with display admin interface
 */
define('_FPC_BT_FAQ_MAIN_URL', 'http://faq.businesstech.fr/');

/*
 * defines constant for SQL update file
 * uses => with display admin interface
 */
define('_FPC_CUST_TYPE_SQL_FILE', 'update-customer-type.sql');

/*
 * defines variable for sql update
 * uses => with admin
 */
$GLOBALS[_FPC_MODULE_NAME . '_SQL_UPDATE'] = array(
	'table' => array(
//        'voucher' => _GSR_VOUCHER_SQL_FILE,
	),
	'field' => array(
		array('field' => 'CNT_CUST_TYPE', 'table' => 'connect', 'file' => _FPC_CUST_TYPE_SQL_FILE, 'check' => true, 'type' => 'Type', 'value' => "enum('none','facebook','twitter','google','paypal','amazon')"),
	)
);

/*
 * defines variable for setting all request params
 * uses => with admin interface
 */
$GLOBALS[_FPC_MODULE_NAME . '_REQUEST_PARAMS'] = array(
    'basic' => array('action' => 'update', 'type' => 'basic'),
    'connectorForm' => array('action' => 'display', 'type' => 'connectorForm'),
    'connector' => array('action' => 'update', 'type' => 'connector'),
    'hook' => array('action' => 'update', 'type' => 'hook'),
    'hookForm' => array('action' => 'display', 'type' => 'hookForm'),
	'curlssl' => array('action' => 'display', 'type' => 'curlssl'),
);