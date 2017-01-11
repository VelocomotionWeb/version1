<?php
/**
 * hook.conf.php file defines all needed constants and variables for hook context
 */

/*
 * include common conf
 */
require_once(dirname(__FILE__) . '/common.conf.php');

/*
 * defines hook library path
 * uses => to include class files 
 */
define('_FPC_PATH_LIB_HOOK', _FPC_PATH_LIB . 'hook/');

/*
 * defines hook tpl path
 * uses => to set good absolute path 
 */
define('_FPC_TPL_HOOK_PATH', 'hook/');

/*
 * defines connector buttons tpl
 * uses => to display connect in hook mode 
 */
define('_FPC_TPL_CONNECTOR_BUTTONS', 'connector-buttons.tpl');

/*
 * defines connector buttons js tpl
 * uses => to display connect in hook mode 
 */
define('_FPC_TPL_CONNECTOR_BUTTONS_JS', 'connector-buttons-js.tpl');

/*
 * defines connector buttons content tpl
 * uses => to display connect in hook mode 
 */
define('_FPC_TPL_CONNECTOR_BUTTONS_CNT', 'buttons-content.tpl');

/*
 * defines connect response tpl
 * uses => to display connect in hook mode 
 */
define('_FPC_TPL_CONNECTOR_RESPONSE', 'connector-response.tpl');

/*
 * defines connect response tpl
 * uses => to display connect in hook mode 
 */
define('_FPC_TPL_CONNECTOR_ACCOUNT', 'connector-account.tpl');

/*
 * defines collector response tpl
 * uses => to display connect in hook mode 
 */
define('_FPC_TPL_COllECTOR_RESPONSE', 'collector-response.tpl');

/*
 * defines account block response tpl
 * uses => to display connect in hook mode 
 */
define('_FPC_TPL_ACCOUNT_BLOCK', 'block-account.tpl');

/*
 * defines variable for setting all request params
 * uses => with admin interface 
 */
$GLOBALS[_FPC_MODULE_NAME . '_MONTH'] = array(
	'fr' => array(
		'short' => array('','Jan','F&eacute;v','Mars','Avr','Mai','Jui','Juil','Aout','Sept','Oct','Nov','D&eacute;c'),
		'long' => array('','Janvier','F&eacute;vrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','D&eacute;cembre'),
	),
	'de' => array(
		'short' => array('','Jan','Feb','M' . chr(132) . 'rz','Apr','Mai','Jun','Jul','Aug','Sept','Okt','Nov','Dez'),
		'long' => array('','Januar','Februar','M' . chr(132) . 'rz','April','Mai','Juni','Juli','August','September','Oktober','November','Dezember'),
	),
	'it' => array(
		'short' => array('','Gen','Feb','Marzo','Apr','Mag','Giu','Lug','Ago','Sett','Ott','Nov','Dic'),
		'long' => array('','Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'),
	),
	'es' => array(
		'short' => array('','Ene','Feb','Marzo','Abr','Mayo','Junio','Jul','Ago','Sept','Oct','Nov','Dic'),
		'long' => array('','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'),
	),
);

/*
 * defines variable for matching lang ref between FB / twitter to prestashop
 * uses => only in hook mode 
 */
$GLOBALS[_FPC_MODULE_NAME . '_REF_LANG'] = array(
	'en' => array('FB' => 'en_US', 'TWITTER' => 'en'),
	'fr' => array('FB' => 'fr_FR', 'TWITTER' => 'fr'),
	'es' => array('FB' => 'es_LA', 'TWITTER' => 'es'),
	'de' => array('FB' => 'de_DE', 'TWITTER' => 'de'),
	'it' => array('FB' => 'it_IT', 'TWITTER' => 'it'),
	'zh' => array('FB' => 'zh_CN', 'TWITTER' => 'zh-cn'),
	'tw' => array('FB' => 'zh_TW', 'TWITTER' => 'zh-tw'),
	'cs' => array('FB' => 'cs_CZ', 'TWITTER' => 'en'),
	'nl' => array('FB' => 'nl_NL', 'TWITTER' => 'nl'),
	'ja' => array('FB' => 'ja_JP', 'TWITTER' => 'ja'),
	'pl' => array('FB' => 'pl_PL', 'TWITTER' => 'pl'),
	'pt' => array('FB' => 'pt_PT', 'TWITTER' => 'pt'),
	'ru' => array('FB' => 'ru_RU', 'TWITTER' => 'ru'),
	'sv' => array('FB' => 'sv_SE', 'TWITTER' => 'sv'),
);