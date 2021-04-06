<?php

require 'config.php';
require 'hako-html.php';
define("READ_LINE", 1024);
$init = new Init;
$THIS_FILE = $init->baseDir . "/hako-axes.php";

class HtmlMente extends HTML {

  function enter() {
    global $init;
    print <<<END
<h1>箱国 アクセスログ閲覧室</h1>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<strong>パスワード：</strong>
<input type="password" size="32" maxlength="32" name="PASSWORD">
<input type="hidden" name="mode" value="enter">
<input type="submit" value="入室する">
</form>

END;
  
  }

  function main($data) {
    global $init;
    print "<h1>箱国 アクセスログ閲覧室</h1>\n";
    $this->dataPrint($data);
  }

  // 表示モード
  function dataPrint($data, $suf = "") {
    global $init;

    print "<HR>";

print <<<END
<br>
<h2>アクセスログ</h2>
<table><tr class="NumberCell">
<td>ログインした時間</td><td>国ＩＤ</td><td>国の名前</td><td>ＩＰ情報</td><td>ホスト情報</td><td>プロキシ判定</td></tr>
END;
$ip_log = file("$init->logname");
$ax = $init->axesmax - 1;
for($i=0; $i<$ax; $i++){
  $number = $ip_log[$i];
  $num = preg_replace( "/,/", "</TD><TD>", $number );
  print "<TR>\n";
  print "<TD>{$num}</TD>\n";
  print "</TR>\n";
}
print "</table>";
}
}

class Main {
  var $mode;
  var $dataSet = array();
  function execute() {
    $html = new HtmlMente;

    $this->parseInputData();

    $html->header();
    switch($this->mode) {

    case "enter":
      if($this->passCheck())
        $html->main($this->dataSet);

      break;
    default:
      $html->enter();
      break;
    }
    $html->footer();
  }
  //----------------------------------------
  function parseInputData() {
    $this->mode = $_POST['mode'];    
    if(!empty($_POST)) {
      while(list($name, $value) = each($_POST)) {
//        $value = Util::sjis_convert($value);
        // 半角カナがあれば全角に変換して返す
//        $value = i18n_ja_jp_hantozen($value,"KHV");
        $value = str_replace(",", "", $value);

        $this->dataSet["{$name}"] = $value;
      }
    }
  }

  //----------------------------------------
  function passCheck() {
    global $init;
    if(strcmp($this->dataSet['PASSWORD'], $init->masterPassword) == 0) {
      return 1;
    } else {
      print "<h2>パスワードが違います。</h2>\n";
      return 0;
    }
  }
}

$start = new Main();
$start->execute();

?>
