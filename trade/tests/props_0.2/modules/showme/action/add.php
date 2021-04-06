<?php
// $Id: add.php,v 1.5 2003/07/08 08:16:24 haruki Exp $
class propsAction_add extends propsAction
{
    function execute(&$request, &$response, $control ='')
    {
        $orig = $request->getValue('orig');
        if($orig === false) $orig = '';

        $str = ($orig == '' ? '' : $orig."\n").$request->getValue('str');

        $response = $str;

        return '';
    }
}

?>