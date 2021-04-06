<?php
// $Id: Config.php,v 1.3 2003/07/06 09:59:54 haruki Exp $
//
// This is sample config file for props.

// Application level configration is defined with constants.

define('PROPS_DIR', dirname(__FILE__));

// Change these to siut for your enviroment.
if(!defined('PROPS_APP_ROOT_DIR'))
    define('PROPS_APP_ROOT_DIR', dirname(dirname(__FILE__)));
if(!defined('PROPS_APP_URL'))
    define('PROPS_APP_URL', 'http://tanstafl.sakura.ne.jp/trade/tests/');
if(!defined('PROPS_MODULE_ROOT_DIR'))
    define('PROPS_MODULE_ROOT_DIR', PROPS_APP_ROOT_DIR.'/modules');
if(!defined('PROPS_REQUEST_DIR'))
    define('PROPS_REQUEST_DIR', PROPS_DIR.'/request');

// It may be not nessesary to change these below.

define('PROPS_ACTION_DIRNAME','action');
define('PROPS_VIEW_DIRNAME','view');
define('PROPS_GATE_DIRNAME','gate');
define('PROPS_ORDER_DIRNAME','order');

define('PROPS_OPERATION_PARAMETER', 'op');
define('PROPS_STATE_PARAMETER', 'st');
define('PROPS_EVENT_PARAMETER', 'ev');

// depends on server settings
define('PROPS_PHP_EXT', '.php');
define('PROPS_INDEX', 'index');
?>