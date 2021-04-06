<?php
// $Id: index.php,v 1.5 2003/07/08 08:28:45 haruki Exp $

include('../../main.php');

$op = '
default:
    order:
        title: def
    practice:
        title: orig
add:
    order:
        title: def
        mistake: orig
    practice:
        title: add
';

$practice = '
add:
    action:
        title: add
    view:
        title: main
orig:
    action:
        title: orig
    view:
        title: main
';

$tyaml = new tyaml;
$op = $tyaml->load($op);
$practice = $tyaml->load($practice);

$controller = new propsController;
$controller->setCurrentModule(basename(dirname(__file__)));
$controller->setCaseTitle(basename(__file__));
$controller->setRequestType('http');

$controller->process($op, $practice, $flow);
?>