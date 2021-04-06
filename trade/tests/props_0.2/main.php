<?php
// $Id: main.php,v 1.3 2003/07/08 08:19:44 haruki Exp $

function mtime_diff($a, $b)
{
    list($a_dec, $a_sec) = explode(" ", $a);
    list($b_dec, $b_sec) = explode(" ", $b);
    return $b_sec - $a_sec + $b_dec - $a_dec;
}
$st = microtime();

// Change these to siut for your enviroment.
define('PROPS_APP_ROOT_DIR', dirname(__FILE__));
define('PROPS_APP_URL', 'http://tanstafl.sakura.ne.jp/trade/tests');

include(PROPS_APP_ROOT_DIR.'/props/include.php');
include(PROPS_APP_ROOT_DIR.'/display/htmltemplate.php');

?>