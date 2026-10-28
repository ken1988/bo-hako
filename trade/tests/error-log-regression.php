<?php
// Run with: php -d short_open_tag=1 trade/tests/error-log-regression.php
// Legacy PHP deprecations are outside this warning/fatal-error regression.
error_reporting(E_ALL & ~E_DEPRECATED);
chdir(dirname(__DIR__));
require 'config.php';
require 'hako-file.php';
require 'hako-util.php';
// Logging is stubbed: regression tests must never write game logs.
class LogIO {}
require 'hako-turn.php';
require 'hako-html.php';
set_error_handler(function ($severity, $message, $file, $line) {
    if (error_reporting() & $severity) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
});
function check($condition, $message) {
    if (!$condition) throw new RuntimeException($message);
}
// Avoid reading deployment secrets or running Init's runtime initialization.
$init = (new ReflectionClass('Init'))->newInstanceWithoutConstructor();
$io = new HakoIO();
$turn = new Turn();
$turn->log = new class {
    function __call($name, $arguments) {}
};
function fixture($population, $meat) {
    global $io, $init;
    $island = $io->normalizeIslandData(array());
    $island['land'] = array_fill(0, $init->islandSize, array_fill(0, $init->islandSize, $init->landSea));
    $island['landValue'] = array_fill(0, $init->islandSize, array_fill(0, $init->islandSize, 0));
    $island['pop'] = $population;
    $island['alcohol'] = $meat;
    $island['money'] = 100000;
    $island['food'] = 10000;
    return $island;
}
foreach (array(0, 1, 4, 5, 10, 100) as $population) {
    foreach (array(0, 1, 100) as $meat) {
        $island = fixture($population, $meat);
        $island['percat'] = 10;
        $turn->income($island);
        $need = round($population / 10);
        $expected = ($need > 0 && $meat > 0) ? min(10, round($meat / $need * 10)) : 0;
        check($island['percat'] == $expected, 'Meat happiness mismatch');
        check($island['alcohol'] == $meat - min($need, $meat), 'Meat consumption mismatch');
    }
}
$island = fixture(0, 100);
$turn->estimate($island);
check($island['pop'] == 0 && $island['hapiness'] == 0, 'Empty island estimate');
$island['land'][10][10] = $init->landTown;
$island['landValue'][10][10] = 120;
$turn->estimate($island);
check($island['pop'] == 120, 'Inhabited island population');
check($island['hapiness'] > 0, 'Inhabited island happiness');
$html = new HtmlMap();
$init->lbbsMax = 3;
$init->lbbsSpeaker = true;
$hako = new HakoIO();
$hako->idToNumber = array(660 => 0);
$island['lbbs'] = array('', '0>gone,634>0>1>old>black', '0>active,660>0>1>new>black');
$GLOBALS['THIS_FILE'] = 'hako-main.php';
ob_start();
$html->lbbsContents($hako, $island);
$output = ob_get_clean();
check(strpos($output, '?Sight=634') === false, 'Deleted island must not link');
check(strpos($output, '?Sight=660') !== false, 'Index-zero island must link');
ob_start();
(new HtmlTop())->setStyleSheet();
ob_end_clean();
echo "PASS: 18 income cases, empty/inhabited estimates, BBS, stylesheet\n";
