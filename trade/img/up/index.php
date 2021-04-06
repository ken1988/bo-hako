<?php
/********************************************************************
* PHPあぷろだ customizing by NetMania
*http://www.netmania.jp
*
*Let's PHP さんのPHPあぷろだのカスタマイズバージョンです。
*シンプル・クールにデザイン変更してあります。
*利用規約はLet's PHPさんに準じます。
*
*log/		[777]
*imgs/		[777]
*ログフォルダ内は全て666に変更(重要)
*
/*********************************************************************
  PHPあぷろだ               by ToR

  http://php.s3.to
  
  source by ずるぽん
  http://zurubon.virtualave.net/

  2001/08/30
  2001/09/04 v1.1 クッキーで環境設定、FTP転送（削除はまだ
　2002/06/12 v1.2 move_uploaded_fileに変更（215行
  2002/07/23 v1.3 del=のCSS対策(147行
  2002/08/06 v2.0 仕様ちょと変える(許可拡張子、元ファイル名表示
  2004/10/10 v2.2 もろもろ修正

  もしもの為の.htaccess （CGI禁止SSI禁止Index表示禁止）
  Options -ExecCGI -Includes -Indexes

　.txtでも、中身がHTMLだと表示されちゃうので注意
 *********************************************/
if(phpversion()>="4.1.0"){//PHP4.1.0以降対応
  $_GET = array_map("_clean", $_GET);
  $_POST = array_map("_clean", $_POST);
  extract($_GET);
  extract($_POST);
  extract($_COOKIE);
  extract($_SERVER);
  $upfile_type=_clean($_FILES['up']['type']);
  $upfile_size=$_FILES["upfile"]["size"];//某所で気づく・・・
  $upfile_name=_clean($_FILES["upfile"]["name"]);
  $upfile=$_FILES["upfile"]["tmp_name"];
}

  $logfile	= "./log/upup.log";	//ログファイル名（変更する事）
  $updir 	= "./log/";	//アップ用ディレクトリ(変更する場合は、35.48.50行も変更）
  $prefix	= '';		//接頭語（up001.txt,up002.jpgならup）
  $logmax	= 200;		//log保存行（これ以上は古いのから削除）
  $commax	= 2500;		//コメント投稿量制限（バイト。全角はこの半分）
  $limitk	= 4;		//アップロード制限（KB キロバイト）
  $page_def	= 20;		//一ページの表示行数
  $admin	= "63810";	//削除管理パス
  $auto_link	= 0;		//コメントの自動リンク（Yes=1;No=0);
  $denylist	= array('192.168.0.1','sex.com','annony');	//アクセス拒否ホスト
  $arrowext	= array('png');	//許可拡張子 小文字（それ以外はエラー


  $count_file	= "./log/count.txt";  //カウンタファイル（空ファイルで666）

  $last_file	= "./log/last.cgi";	//連続投稿制限用ファイル（空ファイルで666）
  $last_time	= 0;		//同一IPからの連続投稿許可する間隔（分）（0で無制限）
  $count_start	= "2008/12/18";	

  /* 項目表示（環境設定）の初期状態 (表示ならChecked 表示しないなら空) */
  $f_act  = 'checked';	//ACT（削除リンク）
  $f_com  = 'checked';	//コメント
  $f_size = 'checked';	//ファイルサイズ
  $f_mime = '';		//MIMEタイプ
  $f_date = 'checked';	//日付け
  $f_anot = 'checked';	//別窓で開く？
  $f_orig = 'checked';	//元ファイル名

if($act=="envset"){
  $cookval = implode(",", array($acte,$come,$sizee,$mimee,$datee,$anote,$orige));
  setcookie ("upcook", $cookval,time()+365*24*3600);
}
function _clean($str) {
  $str = htmlspecialchars($str);
  if (get_magic_quotes_gpc()) $str = stripslashes($str);
  return $str;
}
/* ここからヘッダー */
?>

<HTML>
<HEAD><META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">

<TITLE>PHPアップローダー</TITLE>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<META NAME="ROBOTS" CONTENT="NOARCHIVE"> 
<META NAME="GOOGLEBOT" CONTENT="NOSNIPPET"> 


<STYLE type=text/css>

.ss{
	FONT-SIZE: 7pt;
	font-family: Osaka,Verdana,Arial,Helvetica,sans-serif;
}

BODY {
	FONT-SIZE: 12px; COLOR: #777777; LINE-HEIGHT: 155%;
	font-family: Osaka,Verdana,Arial,Helvetica,sans-serif;
}
TD {
	FONT-SIZE: 12px; COLOR: #777777; LINE-HEIGHT: 155%; 
	font-family: Osaka,Verdana,Arial,Helvetica,sans-serif;
}

a:link {
	color: #666666;
		text-decoration: none;
}
a:visited {
	color: #888888;
	text-decoration: none;
}
a:hover {
	color: #333333;
	text-decoration: none;
}
</STYLE>
</HEAD>
<body>
<table width=370 border=1 cellspacing=0 cellpadding=3 bordercolorlight=#CBCBCB bordercolordark=#FFFFFF bgcolor=#E5E5E5 align=center>
 <tr>
  <td align=center><b>PHPアップローダー<b></td>
 </tr>
</table>


<?php
/* ヘッダーここまで */
$foot = <<<FOOT
<BR>
<table width=98% border=0 align=center cellpadding=2 cellspacing=0><tr><td align=right class=ss>

<a href="http://zurubon.strange-x.com/uploader/">ずるぽんあぷろだ</a> + <a href="http://php.s3.to/" target="_top">レッツPHP!</A> + <a href="http://www.netmania.jp" target="_top">ネットマニア</A></td></tr></table>

</BODY>
</HTML>
FOOT;

function FormatByte($size){//バイトのフォーマット（B→kB）
  if($size == 0)			$format = "";
  else if($size <= 1024)		$format = $size."B";
  else if($size <= (1024*1024))		$format = sprintf ("%dKB",($size/1024));
  else if($size <= (10*1024*1024))	$format = sprintf ("%.2fMB",($size/(1024*1024)));
  else					$format = $size."B";

  return $format;
}
function paging($page, $total){//ページリンク作成
  global $PHP_SELF,$page_def;

    for ($j = 1; $j * $page_def < $total+$page_def; $j++) {
      if($page == $j){//今表示しているのはﾘﾝｸしない
        $next .= "[ <b>$j</b> ]";
      }else{
        $next .= sprintf("[<a href=\"%s?page=%d\">%d</a>]", $PHP_SELF,$j,$j);//他はﾘﾝｸ
      }
    }
    if($page=="all") return sprintf ("<BR><table width=650 border=0 align=center cellpadding=2 cellspacing=0><tr><td align=right>Page: %s [ALL]</td></tr></table><BR>",$next,$PHP_SELF);
    else return sprintf ("<BR><table width=650 border=0 align=center cellpadding=2 cellspacing=0><tr><td align=right>Page: %s [<a href=\"%s?page=all\">ALL</a>]</td></tr></table><BR>",$next,$PHP_SELF);
}
function error($mes1,$mes2=""){//えっらーﾒｯｾｰｼﾞ
  global $foot;

  echo "<BR><table width=370 border=0 align=center cellpadding=2 cellspacing=0 bgcolor=#EFEFEF><tr><td align=center>
<strong>$mes1</strong><br>
$mes2</td></tr></table>";
  echo $foot;
  exit;
}
/* start */
$limitb = $limitk * 1024;
$host = @gethostbyaddr($REMOTE_ADDR);
if(!$upcook) $upcook=implode(",",array($f_act,$f_com,$f_size,$f_mime,$f_date,$f_anot,$f_orig));
list($c_act,$c_com,$c_size,$c_mime,$c_date,$c_anot,$c_orig)=explode(",",$upcook);

/* アクセス制限 */
if(is_array($denylist)){
  while(list(,$line)=each($denylist)){
    if(strstr($host, $line)) error('アクセス制限','あなたにはアクセス権限がありません。');
  }
}
/* 削除実行 */
if($delid && $delpass!=""){
  $old = file($logfile);
  $find = false;
  for($i=0; $i<count($old); $i++){
    list($did,$dext,,,,,,$dpwd,)=explode("\t",$old[$i]);
    if($delid==$did){
      $find = true;
      $del_ext = $dext;
      $del_pwd = rtrim($dpwd);
    }else{
      $new[] = $old[$i];
    }
  }
  if(!$find) error('削除エラー','該当ファイルが見つかりません');
  if($delpass == $admin || substr(md5($delpass), 2, 7) == $del_pwd){
    if(file_exists($updir.$prefix.$delid.".$del_ext")) unlink($updir.$prefix.$delid.".$del_ext");

    $fp = fopen($logfile, "w");
    flock($fp, 2);
    fputs($fp, implode("",$new));
    fclose($fp);
  }else{
    error('削除エラー','パスワードが違います');
  }
}
/* 削除フォーム */
if($del){
  error("投稿データ削除","
<form action=$PHP_SELF method=\"POST\">
<input type=hidden name=delid value=\"".htmlspecialchars($del)."\">
パスワード入力：<input type=password size=12 name=delpass>
<input type=submit value=\"削除\"></form>");
}
/* 環境設定フォーム */
if($act=="env"){
  echo "
<BR><table width=370 border=0 align=center cellpadding=2 cellspacing=0><tr>

<td bgcolor=#F8F8F8 align=center>
<strong>環境設定</strong><br>
</td></tr><tr><td>
<form method=GET action=\"$PHP_SELF\"><input type=hidden name=act value=\"envset\">

<li><strong>表示設定</strong>
<BR>
<input type=checkbox name=acte value=checked $c_act>削除リンク<br>
<input type=checkbox name=come value=checked $c_com>備考・説明<br>
<input type=checkbox name=sizee value=checked $c_size>ファイルサイズ<br>
<input type=checkbox name=mimee value=checked $c_mime>ファイル種類<br>
<input type=checkbox name=datee value=checked $c_date>日付<br>
<input type=checkbox name=orige value=checked $c_orig>元ファイル名<br>
<BR>
<li><strong>動作設定</strong>
<BR>
<input type=checkbox name=anote value=checked $c_anot>ファイルを開く時は別窓で開く<br>

<br>
cookieを利用しています。<br>
上記の設定で訪問することができます。<br><br>
<table border=0 align=center cellpadding=5 cellspacing=0><tr><td>
<input type=submit value=\"登録\">
<input type=reset value=\"元に戻す\">　　>> <a href=\"$PHP_SELF\">戻る</a></td></tr></table>
</form>

</td></tr></table>
";
echo $foot;
exit;
}
$lines = file($logfile);
/* アプロード書き込み処理 */
if(file_exists($upfile) && $com && $upfile_size > 0){
  if(strlen($com) > $commax) error('投稿エラー','コメントが長すぎます');
  if($upfile_size > $limitb)        error('投稿エラー','ファイルがデカすぎます');
  /* 連続投稿制限 */
  if($last_time > 0){
    $now = time();
    $last = @fopen($last_file, "r+") or die("連続投稿用ファイル $last_file を作成してください");
    $lsize = fgets($last, 1024);
    list($ltime, $lip) = explode("\t", $lsize);
    if($host == $lip && $last_time*60 > ($now-$ltime)){
      error('連続投稿制限中','時間を置いてやり直してください');
    }
    rewind($last);
    fputs($last, "$now\t$host\t");
    fclose($last);
  }
  /* 拡張子と新ファイル名 */
  $pos = strrpos($upfile_name,".");	//拡張子取得
  $ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
  $ext = strtolower($ext);//小文字化
  if(!in_array($ext, $arrowext))
    error("拡張子エラー","その拡張子ファイルはアップロードできません");
  /* 拒否拡張子はtxtに変換
  for($i=0; $i<count($denyext); $i++){
    if(strstr($ext,$denyext[$i])) $ext = 'txt';
  }
  */
  list($id,) = explode("\t", $lines[0]);//No取得
  $id = sprintf("%03d", ++$id);		//インクリ
  $newname = $prefix.$id.".".$ext;

  /* 自鯖転送 */
  move_uploaded_file($upfile, $updir.$newname);//3.0.16より後のバージョンのPHP 3または 4.0.2 後
  //copy($upfile, $updir.$newname);
  chmod($updir.$newname, 0604);

  /* MIMEタイプ */
  if(!$upfile_type) $upfile_type = "text/plain";//デフォMIMEはtext/plain

  /* コメント他 */
  $com = htmlspecialchars($com);	//タグ変換
  if(get_magic_quotes_gpc()) $com = stripslashes($com);	//￥除去

  $now = gmdate("Y/m/d(D)H:i", time()+9*60*60);	//日付のフォーマット
  $pwd = ($pass) ? substr(md5($pass), 2, 7) : "*";	//パスっ作成（無いなら*）

  $dat = implode("\t", array($id,$ext,$com,$host,$now,$upfile_size,$upfile_type,$pwd,$upfile_name,));

  if(count($lines) >= $logmax){		//ログオーバーならデータ削除
    for($d = count($lines)-1; $d >= $logmax-1; $d--){
      list($did,$dext,)=explode("\t", $lines[$d]);
      if(file_exists($updir.$prefix.$did.".".$dext)) {
        unlink($updir.$prefix.$did.".".$dext);
      }
    }
  }

  $fp = fopen ($logfile , "w");		//書き込みモードでオープン
  flock($fp ,2);
  fputs ($fp, "$dat\n");		//先頭に書き込む
  for($i = 0; $i < $logmax-1; $i++)	//いままでの分を追記
    fputs($fp, $lines[$i]);
  fclose ($fp);
  reset($lines);
  $lines = file($logfile);		//入れなおし
}
foreach($arrowext as $list) $arrow .= $list." ";
/* 投稿フォーム */
echo '

<table width=370 border=0 align=center cellpadding=6 cellspacing=0 bgcolor=#F8F8F8 style=\"border-color:#CBCBCB; border-width:1px; border-style:solid;\"><tr>

<FORM METHOD="POST" ENCTYPE="multipart/form-data" ACTION="'.$PHP_SELF.'" ><tr><td align=center width=60>ファイル</td>
<td>
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="'.$limitb.'">
<INPUT TYPE=file  SIZE="16" NAME="upfile"> パス：<INPUT TYPE=password SIZE="6" NAME="pass" maxlength="10"></td></tr>
<tr><td align=center width=60>コメント</td><td><INPUT TYPE=text SIZE="47" NAME="com"><BR>
<INPUT TYPE=submit VALUE="アップロード・再読込"><INPUT TYPE=reset VALUE="キャンセル"><br>


</td></FORM></tr>

<tr>
 <td colspan=2 class=ss>■コメント無記入の場合リロードになります。URLはオートリンクします。<BR>■アップ可能：'.$arrow.'<BR>■ファイルは'.$limitk.' KBまでアップロード可能です。<BR>■右クリック - 対象をファイルに保存をして下さい。</td>
 </tr>


</table>
<table width=370 border=0 align=center cellpadding=2 cellspacing=0 bgcolor=#EFEFEF>
 <tr>
  <td align=center class=ss>　
';
/* カウンタ */
echo "[count: ";
if(file_exists($count_file)){
  $fp = fopen($count_file,"r+");//読み書きモードでオープン
  $count = fgets($fp, 64);	//64バイトorEOFまで取得、カウントアップ
  $count++;
  fseek($fp, 0);			//ポインタを先頭に、ロックして書き込み
  flock($fp,2);
  fputs($fp, $count);
  fclose($fp);			//ファイルを閉じる
  echo $count;			//カウンタ表示
}
/* モードリンク */
echo '

]　<a href="'.$PHP_SELF.'?act=env">環境設定</a> | <a href=?>リロード</a> | <a href="sam.php">画像一覧</a>

</td>
 </tr>
</table>
';
/* ログ開始位置 */
$st = ($page) ? ($page - 1) * $page_def : 0;
if(!$page) $page = 1;
if($page == "all"){
  $st = 0;
  $page_def = count($lines);
}
echo paging($page, count($lines));//ページリンク
//メインヘッダ
echo "

<table width=650 border=0 align=center cellpadding=0 cellspacing=0 bgcolor=#CCCCCC><tr><td>

<table width=650 align=center cellpadding=2 cellspacing=1><tr bgcolor=#F8F8F8>";

echo "<th>DL</th>";
if($c_com) echo "<th>備考・説明</th>";
if($c_size) echo "<th>サイズ</th>";
if($c_mime) echo "<th>種類</th>";
if($c_date) echo "<th>日付</th>";
if($c_orig) echo "<th>元ファイル名</th>";
if($c_act) echo "<th>削除</th>";
echo "</tr>";
//メイン表示
for($i = $st; $i < $st+$page_def; $i++){
  if($lines[$i]=="") continue;
  list($id,$ext,$com,$host,$now,$size,$mtype,$pas,$orig,)=explode("\t",$lines[$i]);
  $fsize = FormatByte($size);
  if($auto_link) $com = ereg_replace("(https?|ftp|news)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)","<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",$com);

  $filename = $prefix.$id.".$ext";
  $target = $updir.$filename;

  if($c_anot) $jump = "target='_new'";
  echo "<tr bgcolor=#FFFFFF><!--$host-->";//ホスト表示

  echo "<td align=center>[<a href='$target' $jump>$filename</a>]</td>";
  if($c_com) echo "<td>$com</td>";
  if($c_size) echo "<td align=right>$fsize</td>";
  if($c_mime) echo "<td align=center>$mtype</td>";
  if($c_date) echo "<td align=center>$now</td>";
  if($c_orig) echo "<td>$orig</td>";
  if($c_act) echo "<td align=center><a href='$PHP_SELF?del=$id'>■</a></td>";
  echo "</tr>\n";
  }

echo "</table></td></tr></table>";
echo paging($page,count($lines));
echo $foot;
?>

