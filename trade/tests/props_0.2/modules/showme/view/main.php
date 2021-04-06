<?php
// $Id: main.php,v 1.5 2003/07/08 08:23:11 haruki Exp $

class propsView_main extends propsView
{
    function display(&$order, $response, $control, $state)
    //(&$request, $action_response, $command ='', &$view_response)
    {
        $str = htmlspecialchars($response);
        $errs = $order->getError('str');
        $err = empty($errs) ? '' : '<p><font color="#cc0033">'.htmlspecialchars($errs).'</font></p>';
        $val = empty($errs) ? '' : htmlspecialchars($order->getValue('str'));
        $out['main'] = '
        <div align="center">
            <table border="1" cellpadding="0" cellspacing="0" width="300">
                <tr>
                    <td bgcolor="#669900">
                        <div align="center">
                            <font color="white">All Text You input</font></div>
                    </td>
                </tr>
                <tr>
                    <td>'.nl2br($str).'</td>
                </tr>
            </table>
            '.$err.'
            <p>&nbsp;</p>
            <form name="mainform" method="post" action="index.php">
                <input type="hidden" value="add" name="op">
                <input type="hidden" value="'.$str.'" name="orig">
                <input type="text" name="str" value="'.$val.'">
                <p><input type="submit" name="submitButtonName">  <input type="reset"></p>
            </form>
            <p>&nbsp;</p>
        </div>';

        $out['time'] = sprintf("%0.5f", $sec = mtime_diff($GLOBALS['st'], microtime()));
        HtmlTemplate::t_Include(PROPS_APP_ROOT_DIR.'/display/default.html', $out);
    }
}

?>