<?php
/********************************************************************
* PHP���Ղ낾 customizing by NetMania
*http://www.netmania.jp
*
*Let's PHP �����PHP���Ղ낾�̃J�X�^�}�C�Y�o�[�W�����ł��B
*�V���v���E�N�[���Ƀf�U�C���ύX���Ă���܂��B
*���p�K���Let's PHP����ɏ����܂��B
*
*log/		[777]
*imgs/		[777]
*���O�t�H���_���͑S��666�ɕύX(�d�v)
*
/*********************************************************************
  PHP���Ղ낾               by ToR

  http://php.s3.to
  
  source by ����ۂ�
  http://zurubon.virtualave.net/

  2001/08/30
  2001/09/04 v1.1 �N�b�L�[�Ŋ��ݒ�AFTP�]���i�폜�͂܂�
�@2002/06/12 v1.2 move_uploaded_file�ɕύX�i215�s
  2002/07/23 v1.3 del=��CSS�΍�(147�s
  2002/08/06 v2.0 �d�l����ƕς���(���g���q�A���t�@�C�����\��
  2004/10/10 v2.2 �������C��

  �������ׂ̈�.htaccess �iCGI�֎~SSI�֎~Index�\���֎~�j
  Options -ExecCGI -Includes -Indexes

�@.txt�ł��A���g��HTML���ƕ\�����ꂿ�Ⴄ�̂Œ���
 *********************************************/
if(phpversion()>="4.1.0"){//PHP4.1.0�ȍ~�Ή�
  $_GET = array_map("_clean", $_GET);
  $_POST = array_map("_clean", $_POST);
  extract($_GET);
  extract($_POST);
  extract($_COOKIE);
  extract($_SERVER);
  $upfile_type=_clean($_FILES['up']['type']);
  $upfile_size=$_FILES["upfile"]["size"];//�^���ŋC�Â��E�E�E
  $upfile_name=_clean($_FILES["upfile"]["name"]);
  $upfile=$_FILES["upfile"]["tmp_name"];
}

  $logfile	= "./log/upup.log";	//���O�t�@�C�����i�ύX���鎖�j
  $updir 	= "./log/";	//�A�b�v�p�f�B���N�g��(�ύX����ꍇ�́A35.48.50�s���ύX�j
  $prefix	= '';		//�ړ���iup001.txt,up002.jpg�Ȃ�up�j
  $logmax	= 200;		//log�ۑ��s�i����ȏ�͌Â��̂���폜�j
  $commax	= 2500;		//�R�����g���e�ʐ����i�o�C�g�B�S�p�͂��̔����j
  $limitk	= 4;		//�A�b�v���[�h�����iKB �L���o�C�g�j
  $page_def	= 20;		//��y�[�W�̕\���s��
  $admin	= "63810";	//�폜�Ǘ��p�X
  $auto_link	= 0;		//�R�����g�̎��������N�iYes=1;No=0);
  $denylist	= array('192.168.0.1','sex.com','annony');	//�A�N�Z�X���ۃz�X�g
  $arrowext	= array('png');	//���g���q �������i����ȊO�̓G���[


  $count_file	= "./log/count.txt";  //�J�E���^�t�@�C���i��t�@�C����666�j

  $last_file	= "./log/last.cgi";	//�A�����e�����p�t�@�C���i��t�@�C����666�j
  $last_time	= 0;		//����IP����̘A�����e������Ԋu�i���j�i0�Ŗ������j
  $count_start	= "2008/12/18";	

  /* ���ڕ\���i���ݒ�j�̏������ (�\���Ȃ�Checked �\�����Ȃ��Ȃ��) */
  $f_act  = 'checked';	//ACT�i�폜�����N�j
  $f_com  = 'checked';	//�R�����g
  $f_size = 'checked';	//�t�@�C���T�C�Y
  $f_mime = '';		//MIME�^�C�v
  $f_date = 'checked';	//���t��
  $f_anot = 'checked';	//�ʑ��ŊJ���H
  $f_orig = 'checked';	//���t�@�C����

if($act=="envset"){
  $cookval = implode(",", array($acte,$come,$sizee,$mimee,$datee,$anote,$orige));
  setcookie ("upcook", $cookval,time()+365*24*3600);
}
function _clean($str) {
  $str = htmlspecialchars($str);
  if (get_magic_quotes_gpc()) $str = stripslashes($str);
  return $str;
}
/* ��������w�b�_�[ */
?>

<HTML>
<HEAD><META HTTP-EQUIV="Content-type" CONTENT="text/html; charset=Shift_JIS">

<TITLE>PHP�A�b�v���[�_�[</TITLE>
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
  <td align=center><b>PHP�A�b�v���[�_�[<b></td>
 </tr>
</table>


<?php
/* �w�b�_�[�����܂� */
$foot = <<<FOOT
<BR>
<table width=98% border=0 align=center cellpadding=2 cellspacing=0><tr><td align=right class=ss>

<a href="http://zurubon.strange-x.com/uploader/">����ۂ񂠂Ղ낾</a> + <a href="http://php.s3.to/" target="_top">���b�cPHP!</A> + <a href="http://www.netmania.jp" target="_top">�l�b�g�}�j�A</A></td></tr></table>

</BODY>
</HTML>
FOOT;

function FormatByte($size){//�o�C�g�̃t�H�[�}�b�g�iB��kB�j
  if($size == 0)			$format = "";
  else if($size <= 1024)		$format = $size."B";
  else if($size <= (1024*1024))		$format = sprintf ("%dKB",($size/1024));
  else if($size <= (10*1024*1024))	$format = sprintf ("%.2fMB",($size/(1024*1024)));
  else					$format = $size."B";

  return $format;
}
function paging($page, $total){//�y�[�W�����N�쐬
  global $PHP_SELF,$page_def;

    for ($j = 1; $j * $page_def < $total+$page_def; $j++) {
      if($page == $j){//���\�����Ă���̂��ݸ���Ȃ�
        $next .= "[ <b>$j</b> ]";
      }else{
        $next .= sprintf("[<a href=\"%s?page=%d\">%d</a>]", $PHP_SELF,$j,$j);//�����ݸ
      }
    }
    if($page=="all") return sprintf ("<BR><table width=650 border=0 align=center cellpadding=2 cellspacing=0><tr><td align=right>Page: %s [ALL]</td></tr></table><BR>",$next,$PHP_SELF);
    else return sprintf ("<BR><table width=650 border=0 align=center cellpadding=2 cellspacing=0><tr><td align=right>Page: %s [<a href=\"%s?page=all\">ALL</a>]</td></tr></table><BR>",$next,$PHP_SELF);
}
function error($mes1,$mes2=""){//������[ү����
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

/* �A�N�Z�X���� */
if(is_array($denylist)){
  while(list(,$line)=each($denylist)){
    if(strstr($host, $line)) error('�A�N�Z�X����','���Ȃ��ɂ̓A�N�Z�X����������܂���B');
  }
}
/* �폜���s */
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
  if(!$find) error('�폜�G���[','�Y���t�@�C����������܂���');
  if($delpass == $admin || substr(md5($delpass), 2, 7) == $del_pwd){
    if(file_exists($updir.$prefix.$delid.".$del_ext")) unlink($updir.$prefix.$delid.".$del_ext");

    $fp = fopen($logfile, "w");
    flock($fp, 2);
    fputs($fp, implode("",$new));
    fclose($fp);
  }else{
    error('�폜�G���[','�p�X���[�h���Ⴂ�܂�');
  }
}
/* �폜�t�H�[�� */
if($del){
  error("���e�f�[�^�폜","
<form action=$PHP_SELF method=\"POST\">
<input type=hidden name=delid value=\"".htmlspecialchars($del)."\">
�p�X���[�h���́F<input type=password size=12 name=delpass>
<input type=submit value=\"�폜\"></form>");
}
/* ���ݒ�t�H�[�� */
if($act=="env"){
  echo "
<BR><table width=370 border=0 align=center cellpadding=2 cellspacing=0><tr>

<td bgcolor=#F8F8F8 align=center>
<strong>���ݒ�</strong><br>
</td></tr><tr><td>
<form method=GET action=\"$PHP_SELF\"><input type=hidden name=act value=\"envset\">

<li><strong>�\���ݒ�</strong>
<BR>
<input type=checkbox name=acte value=checked $c_act>�폜�����N<br>
<input type=checkbox name=come value=checked $c_com>���l�E����<br>
<input type=checkbox name=sizee value=checked $c_size>�t�@�C���T�C�Y<br>
<input type=checkbox name=mimee value=checked $c_mime>�t�@�C�����<br>
<input type=checkbox name=datee value=checked $c_date>���t<br>
<input type=checkbox name=orige value=checked $c_orig>���t�@�C����<br>
<BR>
<li><strong>����ݒ�</strong>
<BR>
<input type=checkbox name=anote value=checked $c_anot>�t�@�C�����J�����͕ʑ��ŊJ��<br>

<br>
cookie�𗘗p���Ă��܂��B<br>
��L�̐ݒ�ŖK�₷�邱�Ƃ��ł��܂��B<br><br>
<table border=0 align=center cellpadding=5 cellspacing=0><tr><td>
<input type=submit value=\"�o�^\">
<input type=reset value=\"���ɖ߂�\">�@�@>> <a href=\"$PHP_SELF\">�߂�</a></td></tr></table>
</form>

</td></tr></table>
";
echo $foot;
exit;
}
$lines = file($logfile);
/* �A�v���[�h�������ݏ��� */
if(file_exists($upfile) && $com && $upfile_size > 0){
  if(strlen($com) > $commax) error('���e�G���[','�R�����g���������܂�');
  if($upfile_size > $limitb)        error('���e�G���[','�t�@�C�����f�J�����܂�');
  /* �A�����e���� */
  if($last_time > 0){
    $now = time();
    $last = @fopen($last_file, "r+") or die("�A�����e�p�t�@�C�� $last_file ���쐬���Ă�������");
    $lsize = fgets($last, 1024);
    list($ltime, $lip) = explode("\t", $lsize);
    if($host == $lip && $last_time*60 > ($now-$ltime)){
      error('�A�����e������','���Ԃ�u���Ă�蒼���Ă�������');
    }
    rewind($last);
    fputs($last, "$now\t$host\t");
    fclose($last);
  }
  /* �g���q�ƐV�t�@�C���� */
  $pos = strrpos($upfile_name,".");	//�g���q�擾
  $ext = substr($upfile_name,$pos+1,strlen($upfile_name)-$pos);
  $ext = strtolower($ext);//��������
  if(!in_array($ext, $arrowext))
    error("�g���q�G���[","���̊g���q�t�@�C���̓A�b�v���[�h�ł��܂���");
  /* ���ۊg���q��txt�ɕϊ�
  for($i=0; $i<count($denyext); $i++){
    if(strstr($ext,$denyext[$i])) $ext = 'txt';
  }
  */
  list($id,) = explode("\t", $lines[0]);//No�擾
  $id = sprintf("%03d", ++$id);		//�C���N��
  $newname = $prefix.$id.".".$ext;

  /* ���I�]�� */
  move_uploaded_file($upfile, $updir.$newname);//3.0.16����̃o�[�W������PHP 3�܂��� 4.0.2 ��
  //copy($upfile, $updir.$newname);
  chmod($updir.$newname, 0604);

  /* MIME�^�C�v */
  if(!$upfile_type) $upfile_type = "text/plain";//�f�t�HMIME��text/plain

  /* �R�����g�� */
  $com = htmlspecialchars($com);	//�^�O�ϊ�
  if(get_magic_quotes_gpc()) $com = stripslashes($com);	//������

  $now = gmdate("Y/m/d(D)H:i", time()+9*60*60);	//���t�̃t�H�[�}�b�g
  $pwd = ($pass) ? substr(md5($pass), 2, 7) : "*";	//�p�X���쐬�i�����Ȃ�*�j

  $dat = implode("\t", array($id,$ext,$com,$host,$now,$upfile_size,$upfile_type,$pwd,$upfile_name,));

  if(count($lines) >= $logmax){		//���O�I�[�o�[�Ȃ�f�[�^�폜
    for($d = count($lines)-1; $d >= $logmax-1; $d--){
      list($did,$dext,)=explode("\t", $lines[$d]);
      if(file_exists($updir.$prefix.$did.".".$dext)) {
        unlink($updir.$prefix.$did.".".$dext);
      }
    }
  }

  $fp = fopen ($logfile , "w");		//�������݃��[�h�ŃI�[�v��
  flock($fp ,2);
  fputs ($fp, "$dat\n");		//�擪�ɏ�������
  for($i = 0; $i < $logmax-1; $i++)	//���܂܂ł̕���ǋL
    fputs($fp, $lines[$i]);
  fclose ($fp);
  reset($lines);
  $lines = file($logfile);		//����Ȃ���
}
foreach($arrowext as $list) $arrow .= $list." ";
/* ���e�t�H�[�� */
echo '

<table width=370 border=0 align=center cellpadding=6 cellspacing=0 bgcolor=#F8F8F8 style=\"border-color:#CBCBCB; border-width:1px; border-style:solid;\"><tr>

<FORM METHOD="POST" ENCTYPE="multipart/form-data" ACTION="'.$PHP_SELF.'" ><tr><td align=center width=60>�t�@�C��</td>
<td>
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="'.$limitb.'">
<INPUT TYPE=file  SIZE="16" NAME="upfile"> �p�X�F<INPUT TYPE=password SIZE="6" NAME="pass" maxlength="10"></td></tr>
<tr><td align=center width=60>�R�����g</td><td><INPUT TYPE=text SIZE="47" NAME="com"><BR>
<INPUT TYPE=submit VALUE="�A�b�v���[�h�E�ēǍ�"><INPUT TYPE=reset VALUE="�L�����Z��"><br>


</td></FORM></tr>

<tr>
 <td colspan=2 class=ss>���R�����g���L���̏ꍇ�����[�h�ɂȂ�܂��BURL�̓I�[�g�����N���܂��B<BR>���A�b�v�\�F'.$arrow.'<BR>���t�@�C����'.$limitk.' KB�܂ŃA�b�v���[�h�\�ł��B<BR>���E�N���b�N - �Ώۂ��t�@�C���ɕۑ������ĉ������B</td>
 </tr>


</table>
<table width=370 border=0 align=center cellpadding=2 cellspacing=0 bgcolor=#EFEFEF>
 <tr>
  <td align=center class=ss>�@
';
/* �J�E���^ */
echo "[count: ";
if(file_exists($count_file)){
  $fp = fopen($count_file,"r+");//�ǂݏ������[�h�ŃI�[�v��
  $count = fgets($fp, 64);	//64�o�C�gorEOF�܂Ŏ擾�A�J�E���g�A�b�v
  $count++;
  fseek($fp, 0);			//�|�C���^��擪�ɁA���b�N���ď�������
  flock($fp,2);
  fputs($fp, $count);
  fclose($fp);			//�t�@�C�������
  echo $count;			//�J�E���^�\��
}
/* ���[�h�����N */
echo '

]�@<a href="'.$PHP_SELF.'?act=env">���ݒ�</a> | <a href=?>�����[�h</a> | <a href="sam.php">�摜�ꗗ</a>

</td>
 </tr>
</table>
';
/* ���O�J�n�ʒu */
$st = ($page) ? ($page - 1) * $page_def : 0;
if(!$page) $page = 1;
if($page == "all"){
  $st = 0;
  $page_def = count($lines);
}
echo paging($page, count($lines));//�y�[�W�����N
//���C���w�b�_
echo "

<table width=650 border=0 align=center cellpadding=0 cellspacing=0 bgcolor=#CCCCCC><tr><td>

<table width=650 align=center cellpadding=2 cellspacing=1><tr bgcolor=#F8F8F8>";

echo "<th>DL</th>";
if($c_com) echo "<th>���l�E����</th>";
if($c_size) echo "<th>�T�C�Y</th>";
if($c_mime) echo "<th>���</th>";
if($c_date) echo "<th>���t</th>";
if($c_orig) echo "<th>���t�@�C����</th>";
if($c_act) echo "<th>�폜</th>";
echo "</tr>";
//���C���\��
for($i = $st; $i < $st+$page_def; $i++){
  if($lines[$i]=="") continue;
  list($id,$ext,$com,$host,$now,$size,$mtype,$pas,$orig,)=explode("\t",$lines[$i]);
  $fsize = FormatByte($size);
  if($auto_link) $com = ereg_replace("(https?|ftp|news)(://[[:alnum:]\+\$\;\?\.%,!#~*/:@&=_-]+)","<a href=\"\\1\\2\" target=\"_blank\">\\1\\2</a>",$com);

  $filename = $prefix.$id.".$ext";
  $target = $updir.$filename;

  if($c_anot) $jump = "target='_new'";
  echo "<tr bgcolor=#FFFFFF><!--$host-->";//�z�X�g�\��

  echo "<td align=center>[<a href='$target' $jump>$filename</a>]</td>";
  if($c_com) echo "<td>$com</td>";
  if($c_size) echo "<td align=right>$fsize</td>";
  if($c_mime) echo "<td align=center>$mtype</td>";
  if($c_date) echo "<td align=center>$now</td>";
  if($c_orig) echo "<td>$orig</td>";
  if($c_act) echo "<td align=center><a href='$PHP_SELF?del=$id'>��</a></td>";
  echo "</tr>\n";
  }

echo "</table></td></tr></table>";
echo paging($page,count($lines));
echo $foot;
?>

