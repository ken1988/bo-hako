<?php
$class = 'defaultDisplay';

class defaultDisplay extends propsDisplay
{
    function display($request, &$view_response, $command ='')
    {
?>
<html>

    <head>
        <meta http-equiv="content-type" content="text/html">
        <title>Props Sample Applications</title>
    </head>

    <body bgcolor="#99ff00">
        <div align="center">
            <p><b>Props Sample Applications</b></p>
        </div>
        <hr>
        <div align="center">
            <?php
                while($str = $view_response->getData('main')){
                    echo $str.'<br />';
            }
            ?>
        </div>
        <hr>
        <div align="center">
            <p><img src="../../images/propslogo.gif" width="88" height="31" border="0"><br />
                <font size="2">Powerd by Props Framework</font><br>
                <?php echo printf("%0.5f <br>", $sec = mtime_diff($GLOBALS['st'], microtime())); ?></p>
        </div>
    </body>

</html>
<?php

    }
}

?>