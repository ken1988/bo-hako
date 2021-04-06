<?php
/*----------------------------------------------------------------------*
  箱庭諸島２ for PHP 用 マップ・エディタ
  
  (注)このマップエディタは、無改造の「箱庭諸島２ for PHP」用です。
      改造を行っている『箱庭』には対応していませんので、ご注意ください。
      データを確実に破壊します。
  
  Version: 1.0b (20041200)
  Author: 沙布巾
  Website: http://f22.aaa.livedoor.jp/~snufkin/
 *----------------------------------------------------------------------*/

require 'jcode.phps';
require 'config.php';
require 'hako-file.php';
require 'hako-html.php';
require 'hako-util.php';
require 'wns.php';
$init = new Init;

define("READ_LINE", 1024);
$THIS_FILE =  $init->baseDir . "/hako-edit.php";
$BACK_TO_TOP = "<A HREF=\"JavaScript:void(0);\" onClick=\"document.TOP.submit();return false;\">{$init->tagBig_}トップへ戻る{$init->_tagBig}</A>";

//----------------------------------------------------------------------
class Hako extends HakoIO {

  function readIslands(&$cgi) {
    global $init;
    
    $m = $this->readIslandsFile($cgi);

    return $m;
  }
  //---------------------------------------------------
  // 地形リストを生成
  //---------------------------------------------------
  function getLandList() {
    global $init;

    $landList = "";

    $landList .= "<option value=\"{$init->landSea}\"		>海、浅瀬</option>\n";
    $landList .= "<option value=\"{$init->landWaste}\"		>荒地</option>\n";
    $landList .= "<option value=\"{$init->landPlains}\"		>平地</option>\n";
    $landList .= "<option value=\"{$init->landTown}\"		>村、町、都市</option>\n";
    $landList .= "<option value=\"{$init->landNewtown}\"	>ニュータウン</option>\n";
    $landList .= "<option value=\"{$init->landForest}\"		>森</option>\n";
    $landList .= "<option value=\"{$init->landOil}\"		>海底油田</option>\n";
    $landList .= "<option value=\"{$init->landFarm}\"		>農場</option>\n";
    $landList .= "<option value=\"{$init->landFactory}\"	>工場</option>\n";
    $landList .= "<option value=\"{$init->landMarket}\"	    >市場</option>\n";
    $landList .= "<option value=\"{$init->landBase}\"		>ミサイル基地</option>\n";
    $landList .= "<option value=\"{$init->landHBase}\"		>偽装ミサイル基地</option>\n";
    $landList .= "<option value=\"{$init->landHaribote}\"	>ハリボテ</option>\n";
    $landList .= "<option value=\"{$init->landDefence}\"	>防衛施設(非偽装)</option>\n";
    $landList .= "<option value=\"{$init->landHDefence}\"	>防衛施設(偽装)</option>\n";
    $landList .= "<option value=\"{$init->landMonster}\"	>怪獣</option>\n";
    $landList .= "<option value=\"{$init->landSbase}\"		>海底基地</option>\n";
    $landList .= "<option value=\"{$init->landMFactory}\"	>建材工場</option>\n";
    $landList .= "<option value=\"{$init->landSFactory}\"	>砲弾工場</option>\n";
    $landList .= "<option value=\"{$init->landFFactory}\"	>精製工場</option>\n";
    $landList .= "<option value=\"{$init->landPort}\"	>港</option>\n";
    $landList .= "<option value=\"{$init->landMountain}\"	>山</option>\n";
    $landList .= "<option value=\"{$init->landnMountain}\"	>山地</option>\n";
    $landList .= "<option value=\"{$init->landStonemine}\"	>採石場</option>\n";
    $landList .= "<option value=\"{$init->landSteel}\"	    >鉄鉱山</option>\n";
    $landList .= "<option value=\"{$init->landCoal}\"	    >炭坑</option>\n";
    $landList .= "<option value=\"{$init->landUranium}\"	>ウラン鉱山</option>\n";
    $landList .= "<option value=\"{$init->landSilver}\"	    >銀鉱山</option>\n";
    $landList .= "<option value=\"{$init->landZorasu}\"	    >揚陸艦</option>\n";

    return $landList;
  }
  //---------------------------------------------------
  // 地形に関するデータ生成
  //---------------------------------------------------
  function landString($l, $lv, $x, $y, $mode, $comStr) {
    global $init;
    $point = "({$x},{$y})";
    $naviExp = "''";

    if($x < $init->islandSize / 2)
      $naviPos = 0;
    else
      $naviPos = 1;

    switch($l) {
    case $init->landSea:
      switch($lv) {
      case 1:
        // 浅瀬
        $image = 'land14.gif';
        $naviTitle = '浅瀬';
        break;
      case 2:
        // 工作船
        $image = 'ship.gif';
        $naviTitle = $init->shipName[0];
        break;
      case 3:
        // 漁船
        $image = 'fishingboat.gif';
        $naviTitle = $init->shipName[1];
        break;
      case 4:
        // 海底探索船
        $image = 'ship2.gif';
        $naviTitle = $init->shipName[2];
        break;
      case 5:
        // 戦艦
        $image = 'senkan.gif';
        $naviTitle = $init->shipName[3];
        break;
      case 255:
        // 海賊船
        $image = 'viking.gif';
        $naviTitle = '海賊船';
        break;
      default:
        // 海
        $image = 'land0.gif';
        $naviTitle = '海';
		//$naviText = "{$lv}";
      }
      break;
    case $init->landSeaCity:
      // 海底都市
        $image = 'SeaCity.gif';
        $naviTitle = '海底都市';
		$lv = Util::Rewriter($lv);
        $naviText = "{$lv}{$init->unitPop}";
      break;
    case $init->landPort:
        // 港
        $image = 'port.gif';
        $naviTitle = '港';
        break;
    case $init->landSeaSide:
        // 海岸
        $image = 'sunahama.gif';
        $naviTitle = '砂浜';
        break;
    case $init->landPark:
        // 遊園地
        $image = "park{$lv}.gif";
        $naviTitle = '遊園地';
        break;
    case $init->landFusya:
        // 風車
        $image = 'fusya.gif';
        $naviTitle = '農業改良センター';
        break;
    case $init->landNPark:
        // 消防署
        $image = 'nationalpark.gif';
        $naviTitle = '国立公園';
        break;
    case $init->landNursery:
        // 養殖場
        $image = 'Nursery.gif';
        $naviTitle = '養殖場';
	    $lv = Util::Rewriter($lv);
        $naviText = "{$lv}{$init->unitPop}規模";
        break;
    case $init->landWaste:
      // 荒地
      if($lv == 1) {
        $image = 'land13.gif'; // 着弾点
      } else {
        $image = 'land1.gif';
      }
      $naviTitle = '荒地';
      break;
    case $init->landPlains:
      // 平地
      $image = 'land2.gif';
      $naviTitle = '平地';
      break;
    case $init->landPoll:
      // 汚染土壌
      $image = 'poll.gif';
      $naviTitle = '汚染土壌';
      $naviText = "汚染レベル{$lv}";
      break;
    case $init->landForest:
      // 森
      if($mode == 1) {
        $image = 'land6.gif';
        $naviText= "${lv}{$init->unitTree}";
      } else {
        // 観光者の場合は木の本数隠す
        $image = 'land6.gif';
      }
      $naviTitle = '森';
      break;
    case $init->landTown:
      // 町
      $p; $n;
	  $nwork = (int)($lv/12);
      if($lv < 30) {
        $p = 3;
        $naviTitle = '村';
      } else if($lv < 100) {
        $p = 4;
        $naviTitle = '村落';
      } else if($lv < 200) {
        $p = 5;
        $naviTitle = '農村';
      } else {
        $p = 52;
        $naviTitle = '近郊住宅地';
		$nwork = 0;
      }
      $image = "land{$p}.gif";
	  if ($nwork == 0){
	  	$naviTexts ="";
	  }else{
	    $nwork = Util::Rewriter($nwork * 10);
      	$naviTexts = "/農業{$nwork}{$init->unitPop}";
	  }
	  $lv = Util::Rewriter($lv);
	  $naviText = "{$lv}{$init->unitPop}".$naviTexts ;
      break;
    case $init->landProcity:
      // 防災都市
      if($lv < 110) {
        $naviTitle = '防災都市ランクＥ';
      } else if($lv < 130) {
        $naviTitle = '防災都市ランクＤ';
      } else if($lv < 160) {
        $naviTitle = '防災都市ランクＣ';
      } else if($lv < 200) {
        $naviTitle = '防災都市ランクＢ';
      } else {
        $naviTitle = '防災都市ランクＡ';
      }
      $image = "bousai.gif";
	  $lv = Util::Rewriter($lv);
      $naviText = "{$lv}{$init->unitPop}";
      break;
    case $init->landNewtown:
      // ニュータウン
      $nwork =  (int)($lv/60);
      $image = 'new.gif';
      $naviTitle = 'ニュータウン';
	  if ($nwork == 0){
	  	$naviTexts ="";
	  }else{
	    $nwork = Util::Rewriter($nwork * 10);
		$naviTexts = "/商業{$nwork}{$init->unitPop}";
	  }
	  $lv = Util::Rewriter($lv);
      $naviText = "{$lv}{$init->unitPop}".$naviTexts ;
      break;
    case $init->landBigtown:
      // 現代都市
      $mwork =  (int)($lv/7);
      $lwork =  (int)($lv/100);
      $image = 'big.gif';
      $naviTitle = '現代都市';
	  $mwork = Util::Rewriter($mwork * 10);
	  $lwork = Util::Rewriter($lwork * 10);
	  $lv = Util::Rewriter($lv);
      $naviText = "{$lv}{$init->unitPop}/商業{$mwork}{$init->unitPop}/工業{$lwork}{$init->unitPop}";
      break;
	  
   	 case $init->landIndCity:
      // 工業都市
      $nwork =  (int)($lv/5);
	  $image = 'indust0.gif';
      $naviTitle = '工業都市';
	  $nwork = Util::Rewriter($nwork * 10);
	  $lv = Util::Rewriter($lv);
      $naviText = "{$lv}{$init->unitPop}/工業{$nwork}{$init->unitPop}";
      break;
	  
	case $init->landCapital:
	  //首都
      $nwork =  (int)($lv/9);
      $image = 'capital1.gif';
      $naviTitle = '首都';
	  $nwork = Util::Rewriter($nwork * 10);
	  $lv = Util::Rewriter($lv);
      $naviText = "{$lv}{$init->unitPop}/商業{$nwork}{$init->unitPop}";
      break;
    case $init->landFarm:
      // 農場
      $image = 'land7.gif';
      $naviTitle = '共同農場';
      if($lv > 25) {
      // ドーム型農場
      $image = 'land71.gif';
      $naviTitle = 'ドーム型共同農場';
      }
	  $lv = Util::Rewriter($lv * 10);
      $naviText = "{$lv}{$init->unitPop}規模";
      break;
    case $init->landFactory:
      // 工場
      $image = 'land8.gif';
      $naviTitle = '国営工場';
      if($lv > 100) {
      // 大工場
      $image = 'land82.gif';
      $naviTitle = '国営コンビナート';
      }
	  $lv = Util::Rewriter($lv * 10);
      $naviText = "{$lv}{$init->unitPop}規模";
      break;
    case $init->landMarket:
      // 市場
      $image = 'land22.gif';
      $naviTitle = '国営市場';
	  $lv = Util::Rewriter($lv * 10);
      $naviText = "{$lv}{$init->unitPop}規模";
      break;
    case $init->landHatuden:
      // 発電所
      $image = 'hatuden.gif';
      $naviTitle = '発電所';
      $naviText = "{$lv}000kw";
      if($lv > 100) {
      // 大型発電所
      $image = 'hatuden2.gif';
      $naviTitle = '大型発電所';
      }
      break;
    case $init->landSFactory:
      // 軍事工場
      $naviTitle = '軍事工場';
      $image = 'land17.gif';
      $naviText = "{$lv}{$init->unitShell}規模";
      break;
    case $init->landMFactory:
      // 建材工場
      $naviTitle = '建材工場';
      $image = 'land25.gif';
      $naviText = "{$lv}{$init->unitMaterial}規模";
      break;
    case $init->landFFactory:
      // 精製工場
      $naviTitle = '精製工場';
      $image = 'land27.gif';
      $naviText = "{$lv}0{$init->unitOil}規模";
      break;
    case $init->landHBase:
      if($mode == 0 || $mode == 2) {
        // 観光者の場合は森のふり
        $image = 'land6.gif';
        $naviTitle = '森';
      } else {
        // ミサイル基地
        $level = Util::expToLevel($l, $lv);
        $image = 'land9.gif';
        $naviTitle = '偽装ミサイル基地';
        $naviText = "レベル {$level} / 経験値 {$lv}";
      }
      break;
    case $init->landBase:
      if($mode == 0 || $mode == 2) {
        // 観光者の場合は森のふり
        $image = 'land9.gif';
        $naviTitle = 'ミサイル基地';
      } else {
        // ミサイル基地
        $level = Util::expToLevel($l, $lv);
        $image = 'land9.gif';
        $naviTitle = 'ミサイル基地';
        $naviText = "レベル {$level} / 経験値 {$lv}";
      }
      break;
    case $init->landSbase:
      // 海底基地
      if($mode == 0 || $mode == 2) {
        // 観光者の場合は海のふり
        $image = 'land0.gif';
        $naviTitle = '海';
      } else {
        $level = Util::expToLevel($l, $lv);
        $image = 'land12.gif';
        $naviTitle = '海底基地';
        $naviText = "レベル {$level} / 経験値 {$lv}";
      }
      break;
    case $init->landDefence:
      // 防衛施設
      if($mode == 0 || $mode == 2) {
        // 観光者の場合は防衛施設のレベル隠蔽
      } else {
        $naviText = "耐久力 {$lv}";
      }
      $image = 'land10.gif';
      $naviTitle = '防衛施設';
      break;
    case $init->landHDefence:
      // 偽装防衛施設
      if($mode == 0 || $mode == 2) {
        // 観光者の場合は防衛施設のレベル隠蔽
        $image = 'land6.gif';
        $naviTitle = '森';
      } else {
        $image = 'land10.gif';
        $naviTitle = '偽装防衛施設';
        $naviText = "耐久力 {$lv}";
      }
      break;
    case $init->landSdefence:
      // 海底防衛施設
      if($mode == 0 || $mode == 2) {
        $image = 'land102.gif';
        $naviTitle = '海底防衛施設';
      } else {
        $image = 'land102.gif';
        $naviTitle = '海底防衛施設';
        $naviText = "耐久力 {$lv}";
      }
      break;
	case $init->landMyhome:
      // 議事堂
      $image = "government.gif";
      $naviTitle = '議事堂';
      $naviText = "支持率 {$lv}％";
	  break;
	case $init->landSeeCity:
      // 観光都市
      $image = "land92.gif";
      $naviTitle = '観光都市';
	  $income = round($lv * 0.4 * (40 + $invest*2/3) /100);
	  $lv = Util::Rewriter($lv);
      $naviText = "滞在人口 {$lv}{$init->unitPop}/収入 {$income}{$init->unitMoney}";
	  break;
    case $init->landHaribote:
      // ハリボテ
      if($lv == 0){
        $image = 'land9.gif';
        if($mode == 0 || $mode == 2) {
          // 観光者の場合はミサイル基地のふり
          $naviTitle = 'ミサイル基地';
        } else {
          $naviTitle = 'ハリボテ';
        }
      } else {
        $image = 'land10.gif';
        if($mode == 0 || $mode == 2) {
          // 観光者の場合は防衛施設のふり
          $naviTitle = '防衛施設';
        } else {
          $naviTitle = 'ハリボテ';
        }
      }
      break;
    case $init->landOil:
      // 海底油田
      $image = 'land16.gif';
      $naviTitle = '海底油田';
      $naviText = "Lv {$lv}";
      break;
    case $init->landMountain:
      // 山
      $image = 'land11.gif';
      $naviTitle = '山';
      break;
	case $init->landnMountain:
      // 山地
      $image = 'land26.gif';
      $naviTitle = '山地';
      break;
    case $init->landStonemine:
      // 採石場
      $level = ($lv % 10) + 1;
      $maizo = (int)($lv / 10) * 50;
      $image = 'land15.gif';
      $naviTitle = '採石場';
      $naviText = "Lv{$level} 埋蔵量{$maizo}{$init->unitSulfur}";
      break;
    case $init->landCoal:
      // 炭坑
      $level = ($lv % 10) + 1;
      $maizo = (int)($lv / 10) * 100;
      $image = 'land15.gif';
      $naviTitle = '炭坑';
      $naviText = "Lv{$level} 埋蔵量{$maizo}{$init->unitCoal}";
      break;
    case $init->landSteel:
      // 鉄鉱
      $level = ($lv % 10) + 1;
      $maizo = (int)($lv / 10) * 50;
      $image = 'land15.gif';
      $naviTitle = '鉄鉱';
      $naviText = "Lv{$level} 埋蔵量{$maizo}{$init->unitSteel}";
      break;
    case $init->landUranium:
      // ウラン鉱山
      $level = ($lv % 10) + 1;
      $maizo = (int)($lv / 10) * 1;
      $image = 'land15.gif';
      $naviTitle = 'ウラン鉱山';
      $naviText = "Lv{$level} 埋蔵量{$maizo}{$init->unitUranium}";
      break;
    case $init->landSilver:
      // 金鉱山
      $level = ($lv % 10) + 1;
      $maizo = (int)($lv / 10) * 50;
      $image = 'land15.gif';
      $naviTitle = '銀鉱';
      $naviText = "Lv{$level} 埋蔵量{$maizo}{$init->unitSilver}";
      break;
    case $init->landMonument:
      // 記念碑
      $image = "monument{$lv}.gif";
      $naviTitle = '記念碑';
      $naviText = $init->monumentName[$lv];
      break;
    case $init->landMonster:
    case $init->landSleeper:
      // 怪獣
      $monsSpec = Util::monsterSpec($lv);
      $spec = $monsSpec['kind'];
      $special = $init->monsterSpecial[$spec];
      $image = "monster{$spec}.gif";
      if($l == $init->landSleeper) {
        $naviTitle = "{$monsSpec['name']}（睡眠中）";
      } else {
        $naviTitle = "{$monsSpec['name']}";
      }

      // 硬化中?
      if((($special & 0x4) && (($this->islandTurn % 2) == 1)) ||
         (($special & 0x10) && (($this->islandTurn % 2) == 0))) {
        // 硬化中
        $image = $init->monsterImage[$monsSpec['kind']];
      }
      $naviText = "体力{$monsSpec['hp']}";
    }

    if($mode == 1 || $mode == 2) {
      print "<a href=\"javascript: void(0);\" onclick=\"ps($x,$y)\">";
      $naviText = "{$comStr}\\n{$naviText}";
    }
    print "<img class=\"mapchip\" src=\"{$image}\" alt=\"{$point} {$naviTitle} {$comStr}\" onMouseOver=\"Navi({$naviPos},'{$image}', '{$naviTitle}', '{$point}', '{$naviText}', {$naviExp});\" onMouseOut=\"NaviClose(); return false\">";

    // 座標設定閉じ
    if($mode == 1 || $mode == 2)
      print "</a>";
  }
}
//----------------------------------------------------------------------
class Cgi {
  var $mode = "";
  var $dataSet = array();
  //---------------------------------------------------
  // POST、GETのデータを取得
  //---------------------------------------------------
  function parseInputData() {
    global $init;

    $this->mode = $_POST['mode'];
    if(!empty($_POST)) {
      while(list($name, $value) = each($_POST)) {
        $value = str_replace(",", "", $value);
        $value = JcodeConvert($value, 0, 2);
        $value = HANtoZEN_SJIS($value);
        if($init->stripslashes == true) {
          $this->dataSet["{$name}"] = stripslashes($value);
        } else {
          $this->dataSet["{$name}"] = $value;
        }
      }
      if(!empty($_POST['Sight'])) {
        $this->dataSet['ISLANDID'] = $_POST['Sight'];
      }
    }

  }
}

//----------------------------------------------------------------------
class Edit {
  //---------------------------------------------------
  // TOP 表示（パスワード入力）
  //---------------------------------------------------
  function enter() {
    global $init;

    print <<<END
<h1>箱庭諸島２ for PHP<br>マップ・エディタ</h1>
<form action="{$GLOBALS['THIS_FILE']}" method="post">
<strong>パスワード：</strong>
<input type="password" size="32" maxlength="32" name="PASSWORD">
<input type="hidden" name="mode" value="list">
<input type="submit" value="一覧へ">
</form>

END;
  }
  //---------------------------------------------------
  // 島の一覧を表示
  //---------------------------------------------------
  function main($hako, $data) {
    global $init;

    // パスワード
    if(!Util::checkPassword("", $data['PASSWORD'])) {
      // password間違い
      Error::wrongPassword();
      return;
    }

    print "<h1>箱庭諸島２ for PHP 用<br>マップ・エディタ</h1>\n";

    print <<<END
<h2 class='Turn'>ターン$hako->islandTurn</h2>
<hr>
<div ID="IslandView">
<h2>諸島の状況</h2>
<p>
島の名前をクリックすると、<strong>マップ</strong>が表示されます。
</p>
<table border="1">
<tr>
<th {$init->bgTitleCell}>{$init->tagTH_}順位{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}島{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}人口{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}面積{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}資金{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}食料{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}農場規模{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}工場規模{$init->_tagTH}</th>
<th {$init->bgTitleCell}>{$init->tagTH_}市場規模{$init->_tagTH}</th>
</tr>

END;

    // 表示内容は、管理者用の内容
    for($i = 0; $i < $hako->islandNumber; $i++) {
      $island = $hako->islands[$i];
      $j = $i + 1;
      $id    = $island['id'];
      $pop   = $island['pop'] . $init->unitPop;
      $area  = $island['area'] . $init->unitArea;
      $money = $island['money'] . $init->unitMoney;
      $food  = $island['food'] . $init->unitFood;
      $farm  = ($island['farm'] <= 0) ? "保有せず" : $island['farm'] * 10 . $init->unitPop;
      $factory  = ($island['factory'] <= 0) ? "保有せず" : $island['factory'] * 10 . $init->unitPop;
      $market = ($island['market'] <= 0) ? "保有せず" : $island['market'] * 10 . $init->unitPop;
      $comment  = $island['comment'];
      $comment_turn = $island['comment_turn'];
      $monster = '';
      if($island['monster'] > 0) {
        $monster = "<strong class=\"monster\">[怪獣{$island['monster']}体]</strong>";
      }
      
      $name = "";
      if($island['absent']  == 0) {
        $name = "{$init->tagName_}{$island['name']}島{$init->_tagName}";
      } else {
        $name = "{$init->tagName2_}{$island['name']}島({$island['absent']}){$init->_tagName2}";
      }
      if(!empty($island['owner'])) {
        $owner = $island['owner'];
      } else {
        $owner = "コメント";
      }

      if($init->commentNew > 0 && ($comment_turn + $init->commentNew) > $hako->islandTurn) {
        $comment .= " <span class=\"new\">New</span>";
      }
      
      print "<tr>\n";
      print "<th {$init->bgNumberCell} rowspan=\"2\">{$init->tagNumber_}$j{$init->_tagNumber}</th>\n";
      print "<td {$init->bgNameCell} rowspan=\"2\"><a href=\"JavaScript:void(0);\" onClick=\"document.MAP{$id}.submit();return false;\">{$name}</a> {$monster}<br>\n{$prize}</td>\n";
      print <<<END
<form name="MAP{$id}" action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
<input type="hidden" name="mode" value="map">
<input type="hidden" name="Sight" value="{$id}">
</form>

END;

      print "<td {$init->bgInfoCell}>$pop</td>\n";
      print "<td {$init->bgInfoCell}>$area</td>\n";
      print "<td {$init->bgInfoCell}>$money</td>\n";
      print "<td {$init->bgInfoCell}>$food</td>\n";
      print "<td {$init->bgInfoCell}>$farm</td>\n";
      print "<td {$init->bgInfoCell}>$factory</td>\n";
      print "<td {$init->bgInfoCell}>$market</td>\n";
      print "</tr>\n";
      print "<tr>\n";
      print "<td {$init->bgCommentCell} colspan=\"7\">{$init->tagTH_}{$owner}：{$init->_tagTH}$comment</td>\n";
      print "</tr>\n";
    }

    print "</table>\n</div>\n";
  }
  //---------------------------------------------------
  // マップエディタの表示
  //---------------------------------------------------
  function editMap($hako, $data) {
    global $init;

    // パスワード
    if(!Util::checkPassword("", $data['PASSWORD'])) {
      // password間違い
      Error::wrongPassword();
      return;
    }

    $html = new HtmlMap;

    $id     = $data['ISLANDID'];
    $number = $hako->idToNumber[$id];
    $island = $hako->islands[$number];

    print <<<END
<script type="text/javascript">
<!--
function ps(x, y, ld, lv) {
  document.InputPlan.POINTX.options[x].selected = true;
  document.InputPlan.POINTY.options[y].selected = true;
  document.InputPlan.LAND.options[ld].selected = true;

  if(ld == {$init->landMonster}) {
    mn = Math.floor(lv / 10);
    lv = lv - mn * 10;
    document.InputPlan.MONSTER.options[mn].selected = true;
    document.InputPlan.LEVEL.options[lv].selected = true;
  } else {
    document.InputPlan.LEVEL.options[lv].selected = true;
  }

  return true;
}

//-->
</script>
<div align="center">
{$init->tagBig_}{$init->tagName_}{$island['name']}島{$init->_tagName}マップ・エディタ{$init->_tagBig}<br>
{$GLOBALS['BACK_TO_TOP']}
</div>

<form name="TOP" action="{$GLOBALS['THIS_FILE']}" method="post">
<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
<input type="hidden" name="mode" value="list">
</form>

END;

    // 島の情報を表示
    $html->islandInfo($island, $number, 1);

    // 説明文を表示
    print <<<END
<div align="center">
<table border="1">
<tr valign="top">
<td {$init->bgCommandCell}>
<b>レベルについて</b>
<ul>
<li><b>海、浅瀬</b><br>レベル 1 のとき浅瀬
<li><b>荒地</b><br>レベル 1 のとき着弾点
<li><b>村、町、都市</b><br>レベル 30 未満が村<br>レベル 100 未満が町
<li><b>ミサイル基地</b><br>経験値
<li><b>鉱山</b><br>レベル 5 まで
<li><b>怪獣</b><br>各怪獣の最大レベルを超える<br>設定はできません
<li><b>海底基地</b><br>経験値
</ul>

</td>
<td {$init->bgMapCell}>

END;

    // 地形出力
    $html->islandMap($hako, $island, 1);

    // エディタ領域の表示
    print <<<END
</td>
<td {$init->bgInputCell}>
<div align="center">
<form action="{$GLOBALS['THIS_FILE']}" method="post" name="InputPlan">
<input type="hidden" name="mode" value="regist">
<input type="hidden" name="ISLANDID" value="{$island['id']}">
<input type="hidden" name="PASSWORD" value="{$data['PASSWORD']}">
<strong>マップ・エディタ</strong><br>
<hr>
<strong>座標(</strong>
<select name="POINTX">

END;

    for($i = 0; $i < $init->islandSize; $i++) {
      if($i == $data['defaultX']) {
        print "<option value=\"{$i}\" selected>{$i}</option>\n";
      } else {
        print "<option value=\"{$i}\">{$i}</option>\n";
      }
    }
    print "</select>, <select name=\"POINTY\">";
    for($i = 0; $i < $init->islandSize; $i++) {
      if($i == $data['defaultY']) {
        print "<option value=\"{$i}\" selected>{$i}</option>\n";
      } else {
        print "<option value=\"{$i}\">{$i}</option>\n";
      }
    }

    // 地形リスト取得
    $landList = $hako->getLandList();

    print <<<END
</select><strong>)</strong>
<hr>
<strong>地形</strong>
<select name="LAND">
{$landList}
</select>
<hr>
<strong>怪獣の種類</strong>
<select name="MONSTER">

END;

    for($i = 0; $i < $init->monsterNumber; $i++) {
        print "<option value=\"{$i}\">{$init->monsterName[$i]}</option>\n";
    }

    print <<<END
</select>
<hr>
<strong>レベル</strong>
<select name="LEVEL">

END;

    for($i = 0; $i < 256; $i++) {
        print "<option value=\"{$i}\">{$i}</option>\n";
    }

    print <<<END
</select>
<hr>
<input type="submit" value="登録">

</form>
</div>

<ul>
<li>登録するときは十分注意願います。
<li>データを破壊する場合があります。
<li>バックアップを行ってから<br>行う様にしましょう。
<li>地形データを変更するのみで、<br>他のデータは変更されません。<br>
ターン更新で他のデータへ反映されます。
</ul>

</td>
</tr>
</table>
</div>
END;
  }
  //---------------------------------------------------
  // 地形の登録
  //---------------------------------------------------
  function regist($hako, $data) {
    global $init;

    // パスワード
    if(!Util::checkPassword("", $data['PASSWORD'])) {
      // password間違い
      Error::wrongPassword();
      return;
    }

    $id        = $data['ISLANDID'];
    $number    = $hako->idToNumber[$id];
    $island    = $hako->islands[$number];
    $land      = &$island['land'];
    $landValue = &$island['landValue'];

    $x     = $data['POINTX'];
    $y     = $data['POINTY'];
    $ld    = $data['LAND'];
    $mons  = $data['MONSTER'];
    $level = $data['LEVEL'];

    // 怪獣のレベル設定
    if($ld == $init->landMonster) {
      $BHP = $init->monsterBHP[$mons];
      if($init->monsterDHP[$mons] > 0) { $DHP = $init->monsterDHP[$mons] - 1; } else { $DHP = $init->monsterDHP[$mons]; }
      $level = min($level, $BHP + $DHP);
      $level = $mons  * 10 + $level;
    }

    // 鉱山のレベル設定
    if(($ld == $init->landStonemine) || ($ld == $init->landCoal) || ($ld == $init->landSteel) || ($ld == $init->landUranium) || ($ld == $init->landSilver)) {
      $level = min($level, 4);
    }

    // 更新データ設定
    $land[$x][$y]      = $ld;
    $landValue[$x][$y] = $level;

    // マップデータ更新
    $hako->writeLand($id, $island);

    // 設定した値を戻す
    $hako->islands[$number] = $island;

    print "{$init->tagBig_}地形を変更しました{$init->_tagBig}<hr>\n";

    // マップエディタの表示へ
    $this->editMap($hako, $data);
  }
}

//----------------------------------------------------------------------
class Main {

  function execute() {
    $hako = new Hako;
    $cgi = new Cgi;

    $cgi->parseInputData();
    if(!$hako->readIslands($cgi)) {
      HTML::header($cgi->dataSet);
      Error::noDataFile();
      HTML::footer();
      exit();
    }

    $edit = new Edit;

    switch($cgi->mode) {
    case "list":
      $html = new HtmlTop;
      $html->header($cgi->dataSet);
      $edit->main($hako, $cgi->dataSet);
      $html->footer();
      break;
    case "map":
      $html = new HtmlTop;
      $html->header($cgi->dataSet);
      $edit->editMap($hako, $cgi->dataSet);
      $html->footer();
      break;

    case "regist":
      $html = new HtmlTop;
      $html->header($cgi->dataSet);
      $edit->regist($hako, $cgi->dataSet);
      $html->footer();
      break;

    default:
      $html = new HtmlTop;
      $html->header($cgi->dataSet);
      $edit->enter();
      $html->footer();
    }
    exit();
  }
}

$start = new Main;
$start->execute();
?>
