<?php
//$Id: orig.php,v 1.5 2003/07/08 08:16:24 haruki Exp $

class propsAction_orig extends propsAction
{
    function execute(&$request, &$response, $control ='')
    {
        $orig = $request->getValue('orig');
        if($orig === false) $orig = '';
        $response = $orig;
        return '';
    }
}
?>