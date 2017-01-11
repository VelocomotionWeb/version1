<?php
/**
 * install.conf.php file defines all needed constants and variables used in installation of module
 */

/*
 * include common conf
 */
require_once(dirname(__FILE__) . '/common.conf.php');

/*
 * defines install library path
 * uses => to include class files  
 */
define('_FPC_PATH_LIB_INSTALL', _FPC_PATH_LIB . 'install/');

/*
 * defines installation sql file
 * uses => only with DB install action  
 */
define('_FPC_INSTALL_SQL_FILE', 'install.sql'); // comment if not use SQL

/*
 * defines uninstallation sql file
 * uses => only with DB uninstall action  
 */
define('_FPC_UNINSTALL_SQL_FILE', 'uninstall.sql'); // comment if not use SQL

/*
 * defines constant for plug SQL install/uninstall debug
 * uses => set "true" only in debug mode - exceeds install sql execution  
 */
define('_FPC_LOG_JAM_SQL', false); // comment if not use SQL

/*
 * defines constant for plug CONFIG install/uninstall debug
 * uses => set "true" only in debug mode - exceeds uninstall sql execution  
 */
define('_FPC_LOG_JAM_CONFIG', false);