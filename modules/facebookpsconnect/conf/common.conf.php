<?php
/**
 * common.conf.php file defines all needed constants and variables for all context of using module - install / admin / hook / tab
 */

/*
 * defines constant of module name
 * * uses => set short name of module 
 */
define('_FPC_MODULE_NAME', 'FBPSC');

/*
 * defines set module name
 * uses => on setting name of module 
 */
define('_FPC_MODULE_SET_NAME', 'facebookpsconnect');

/*
 * defines root path of module
 * uses => with all included files  
 */
define('_FPC_PATH_ROOT', _PS_MODULE_DIR_ . _FPC_MODULE_SET_NAME . '/');

/*
 * defines conf path
 * uses => with including conf files in match environment  
 */
define('_FPC_PATH_CONF', _FPC_PATH_ROOT . 'conf/');

/*
 * defines libraries path
 * uses => with all class files  
 */
define('_FPC_PATH_LIB', _FPC_PATH_ROOT . 'lib/');

/*
 * defines sql path
 * uses => with all SQL script  
 */
define('_FPC_PATH_SQL', _FPC_PATH_ROOT . 'sql/');

/*
 * defines common library path
 * uses => to include class files  
 */
define('_FPC_PATH_LIB_COMMON', _FPC_PATH_LIB . 'common/');

/*
 * defines connectors library path
 * uses => to include class files  
 */
define('_FPC_PATH_LIB_CONNECTOR', _FPC_PATH_LIB . 'connectors/');

/*
 * defines connectors library path
 * uses => to include class files  
 */
define('_FPC_PATH_LIB_PROTOCOL', _FPC_PATH_LIB . 'protocols/');

/*
 * defines views folder
 * uses => to include img / css / js / templates files
 */
define('_FPC_PATH_VIEWS', 'views/');

/*
 * defines common path tpl
 * uses => to set good absolute path
 */
define('_FPC_TPL_COMMON_PATH', 'admin/');

/*
 * defines js URL
 * uses => to include js files on templates (use prestashop constant _MODULE_DIR_)  
 */
define('_FPC_URL_JS', _MODULE_DIR_ . _FPC_MODULE_SET_NAME . '/' . _FPC_PATH_VIEWS . 'js/');

/*
 * defines css URL
 * uses => to include css files on templates (use prestashop constant _MODULE_DIR_)  
 */
define('_FPC_URL_CSS', _MODULE_DIR_ . _FPC_MODULE_SET_NAME . '/' . _FPC_PATH_VIEWS . 'css/');

/*
 * defines img path
 * uses => to include all used images  
 */
define('_FPC_PATH_IMG', 'img/');

/*
 * defines admin img URL
 * uses => to include js files on templates (use prestashop constant _MODULE_DIR_)
 */
define('_FPC_URL_IMG_ADMIN', _MODULE_DIR_ . _FPC_MODULE_SET_NAME . '/'. _FPC_PATH_VIEWS . _FPC_PATH_IMG . 'admin');

/*
 * defines img URL
 * uses => to include img files in templates (use Prestashop constant _MODULE_DIR_)  
 */
define('_FPC_URL_IMG', _MODULE_DIR_ . _FPC_MODULE_SET_NAME . '/'. _FPC_PATH_VIEWS . _FPC_PATH_IMG);

/*
 * defines MODULE URL
 * uses => to execute updating of callback review value  
 */
define('_FPC_MODULE_URL', _MODULE_DIR_ . _FPC_MODULE_SET_NAME . '/');

/*
 * defines tpl path name
 * uses => with included templates  
 */
define('_FPC_PATH_TPL_NAME', _FPC_PATH_VIEWS . 'templates/');

/*
 * defines tpl path
 * uses => with included templates  
 */
define('_FPC_PATH_TPL', _FPC_PATH_ROOT . _FPC_PATH_TPL_NAME);

/*
 * defines header tpl
 * uses => with display admin interface  
 */
define('_FPC_TPL_HEADER', 'header.tpl');

/*
 * defines constant of error tpl
 * uses => with display error - transverse tpl  
 */
define('_FPC_TPL_ERROR', 'error.tpl');

/*
 * defines constant of empty tpl
 * uses => with display error - transverse tpl  
 */
define('_FPC_TPL_EMPTY', 'empty.tpl');

/*
 * defines confirm tpl
 * uses => with display admin / hook interface  
 */
define('_FPC_TPL_CONFIRM', 'confirm.tpl');

/*
 * defines facebook button tpl
 * uses => to display connect in hook mode  
 */
define('_FPC_TPL_BUTTON_FB', 'button-facebook.tpl');

/*
 * defines twitter button tpl
 * uses => to display connect in hook mode  
 */
define('_FPC_TPL_BUTTON_TWITTER', 'button-twitter.tpl');

/*
 * defines google button tpl
 * uses => to display connect in hook mode  
 */
define('_FPC_TPL_BUTTON_GOOGLE', 'button-google.tpl');

/*
 * defines paypal button tpl
 * uses => to display connect in hook mode  
 */
define('_FPC_TPL_BUTTON_PAYPAL', 'button-paypal.tpl');

/*
 * defines amazon button tpl
 * uses => to display connect in hook mode
 */
define('_FPC_TPL_BUTTON_AMAZON', 'button-amazon.tpl');

/*
 * defines activate / deactivate debug mode
 * uses => only in debug / programming mode  
 */
define('_FPC_DEBUG', false);

/*
 * defines constant to use or not js on submit action
 * uses => only in debug mode - test checking control on server side  
 */
define('_FPC_USE_JS', true);

/*
 * defines variable for setting configuration options
 * uses => with install or update action - declare all mandatory values stored by prestashop in module using  
 */
$GLOBALS[_FPC_MODULE_NAME . '_CONFIGURATION'] = array(
	_FPC_MODULE_NAME . '_MODULE_VERSION'            => '1.0.1',
	_FPC_MODULE_NAME . '_DISPLAY_FB_POPIN'          => 1,
	_FPC_MODULE_NAME . '_DISPLAY_BLOCK'             => 0,
	_FPC_MODULE_NAME . '_DEFAULT_CUSTOMER_GROUP'    => '',
	_FPC_MODULE_NAME . '_API_REQUEST_METHOD'        => '',
	_FPC_MODULE_NAME . '_TEST_CURl_SSL'             => 0,
	_FPC_MODULE_NAME . '_SSL_TEST_TODO'             => 1,
	_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_ACCOUNT'=> 0,
	_FPC_MODULE_NAME . '_DISPLAY_BLOCK_INFO_CART'   => 0,
);

/*
 * defines variable for setting hooks
 * uses => in INSTALL / ADMIN / HOOK mode  
 */
$GLOBALS[_FPC_MODULE_NAME . '_HOOKS'] = array(
	'header'  => array('name' => ((version_compare(_PS_VERSION_, '1.5.0', '>'))? 'displayHeader' : 'header'), 'data' => Configuration::get(_FPC_MODULE_NAME . '_HEADER'), 'use' => false, 'title' => ''),
	'top'     => array('name' => ((version_compare(_PS_VERSION_, '1.5.0', '>'))? 'displayTop' : 'top'), 'data' => Configuration::get(_FPC_MODULE_NAME . '_TOP'), 'use' => true, 'title' => ''),
	'account' => array('name' => ((version_compare(_PS_VERSION_, '1.5.0', '>'))? 'displayCustomerAccount' : 'customerAccount'), 'data' => Configuration::get(_FPC_MODULE_NAME . '_ACCOUNT'), 'use' => false, 'title' => ''),
	'right'   => array('name' => ((version_compare(_PS_VERSION_, '1.5.0', '>'))? 'displayRightColumn' : 'rightColumn'), 'data' => Configuration::get(_FPC_MODULE_NAME . '_RIGHT'), 'use' => true, 'title' => ''),
	'left'    => array('name' => ((version_compare(_PS_VERSION_, '1.5.0', '>'))? 'displayLeftColumn' : 'leftColumn'), 'data' => Configuration::get(_FPC_MODULE_NAME . '_LEFT'), 'use' => true, 'title' => ''),
	'footer'  => array('name' => ((version_compare(_PS_VERSION_, '1.5.0', '>'))? 'displayFooter' : 'footer'), 'data' => Configuration::get(_FPC_MODULE_NAME . '_FOOTER'), 'use' => true, 'title' => ''),
);


/*
 * defines variable for setting zones
 * uses => in ADMIN / HOOK mode  
 */
$GLOBALS[_FPC_MODULE_NAME . '_ZONE'] = array_merge($GLOBALS[_FPC_MODULE_NAME . '_HOOKS'],
	array(
		'authentication' => array('name' => 'authentication', 'data' => Configuration::get(_FPC_MODULE_NAME . '_AUTHENTICATION'), 'use' => true, 'title' => ''),
	)
);

/*
 * Hide block user for prestashop 1.6
 */
if (version_compare(_PS_VERSION_,'1.6.0','<')) {
	$GLOBALS[_FPC_MODULE_NAME . '_ZONE']['blockUser'] = array('name' => 'blockUser', 'data' => Configuration::get(_FPC_MODULE_NAME . '_BLOCKUSER'), 'use' => true, 'title' => '');
}

/*
 * defines variable for setting connectors
 * uses => in install / ADMIN / HOOK mode 
 */
$GLOBALS[_FPC_MODULE_NAME . '_CONNECTORS'] = array(
	'facebook' => array(
		'title'     => '',
		'adminTpl'  => 'facebook.tpl',
		'data'      => Configuration::get(_FPC_MODULE_NAME . '_FACEBOOK'),
		'tpl'       => _FPC_TPL_BUTTON_FB,
	),
	'twitter' => array(
		'title'     => '',
		'adminTpl'  => 'twitter.tpl',
		'data'      => Configuration::get(_FPC_MODULE_NAME . '_TWITTER'),
		'tpl'       => _FPC_TPL_BUTTON_TWITTER,
	),
	'google' => array(
		'title'     => '',
		'adminTpl'  => 'google.tpl',
		'data'      => Configuration::get(_FPC_MODULE_NAME . '_GOOGLE'),
		'tpl'       => _FPC_TPL_BUTTON_GOOGLE,
	),
	'paypal' => array(
		'title'     => '',
		'adminTpl'  => 'paypal.tpl',
		'data'      => Configuration::get(_FPC_MODULE_NAME . '_PAYPAL'),
		'tpl'       => _FPC_TPL_BUTTON_PAYPAL,
	),
	'amazon' => array(
		'title'     => '',
		'adminTpl'  => 'amazon.tpl',
		'data'      => Configuration::get(_FPC_MODULE_NAME . '_AMAZON'),
		'tpl'       => _FPC_TPL_BUTTON_AMAZON,
	),
);

/*
 * defines variable for translating js msg
 * uses => with admin interface - declare all displayed error messages
 */
$GLOBALS[_FPC_MODULE_NAME . '_JS_MSG'] = array();