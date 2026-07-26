<?php
/*******************************************************************

  箱庭諸島２ for PHP


  $Id: hako-main.php,v 1.14 2003/10/15 17:52:15 Watson Exp $
  Extracted as hako-file.php by Sanda - 2004/01/22
  Rev. 1.1 by Sanda         2004/01/22
*******************************************************************/

//--------------------------------------------------------------------
class HakoIO {
  var $islandTurn;	// ターン数
  var $islandLastTime;	// 最終更新時刻
  var $islandNumber;	// 島の総数
  var $islandNextID;	// 次に割り当てる島ID
  var $islands;		// 全島の情報を格納
  var $idToNumber;
  var $idToName;
  var $allyNumber;      // 同盟の総数
  var $ally;            // 各同盟の情報を格納
  var $idToAllyNumber;

  //---------------------------------------------------
  // 国データの空欄を、用途に応じた初期値で補完する
  //---------------------------------------------------
  function normalizeIslandData($island) {
    $textFields = array(
      'name', 'owner', 'prize', 'comment', 'password', 'Cname',
      'banum', 'wiki_link', 'trade_link', 'news_link'
    );
    $numericFields = array(
      'id', 'starturn', 'absent', 'comment_turn', 'news', 'news_point',
      'point', 'pots', 'money', 'lot', 'gold', 'food', 'rice', 'pop',
      'peop', 'spop', 'area', 'goods', 'alcohol', 'wood', 'stone',
      'steel', 'silver', 'material', 'oil', 'fuel', 'explosive', 'shell',
      'farm', 'factory', 'market', 'service', 'mfactory', 'sfactory',
      'ffactory', 'mountain', 'mining', 'hatuden', 'monster', 'taiji',
      'rena', 'fire', 'tenki', 'freeze', 'port', 'hapiness', 'siji',
      'invest', 'capital', 'edinv', 'cmente', 'indnum', 'polit', 'soclv',
      'bport', 'milpop', 'navy', 'sfarmy', 'civwar'
    );

    foreach($textFields as $field) {
      if(!isset($island[$field])) $island[$field] = '';
    }
    foreach($numericFields as $field) {
      if(!isset($island[$field]) || trim((string)$island[$field]) === '') {
        $island[$field] = 0;
      }
    }

    $shipTypes = array('passenger', 'fishingboat', 'tansaku', 'senkan', 'viking');
    if(!isset($island['ship']) || !is_array($island['ship'])) $island['ship'] = array();
    foreach($shipTypes as $shipType) {
      if(!isset($island['ship'][$shipType]) || trim((string)$island['ship'][$shipType]) === '') {
        $island['ship'][$shipType] = 0;
      }
    }

    if(!isset($island['eisei']) || !is_array($island['eisei'])) $island['eisei'] = array();
    for($i = 0; $i < 6; $i++) {
      if(!isset($island['eisei'][$i]) || trim((string)$island['eisei'][$i]) === '') {
        $island['eisei'][$i] = 0;
      }
    }

    foreach(array('land', 'landValue', 'command', 'lbbs', 'regT') as $field) {
      if(!isset($island[$field]) || !is_array($island[$field])) $island[$field] = array();
    }

    return $island;
  }

  //---------------------------------------------------
  // CSV 形式の1行を読み、足りない項目は空欄として補う
  //---------------------------------------------------
  function readIslandValues($fp, $count) {
    return array_pad(explode(',', $this->readIslandLine($fp)), $count, '');
  }

  function readIslandLine($fp) {
    $line = fgets($fp, READ_LINE);
    return ($line === false) ? '' : chop($line);
  }

  //---------------------------------------------------
  // 全島データを読み込む
  // 'mode'が変わる可能性があるので$cgiを参照で受け取る
  //---------------------------------------------------
  function readIslandsFile(&$cgi) {
    global $init;
    $num = $cgi->dataSet['ISLANDID'];

    $fileName = "{$init->dirName}/hakojima.dat";
    if(!is_file($fileName)) {
      return false;
    }
    $fp = fopen($fileName, "r");
    Util::lockr($fp);
    $this->islandTurn     = chop(fgets($fp, READ_LINE));
    $this->islandLastTime = chop(fgets($fp, READ_LINE));
    $this->islandNumber   = chop(fgets($fp, READ_LINE));
    $this->islandNextID   = chop(fgets($fp, READ_LINE));

    $GLOBALS['ISLAND_TURN'] = $this->islandTurn;

    // ターン処理判定
    $now = time();
    if((DEBUG && (strcmp($cgi->dataSet['mode'], 'debugTurn') == 0)) ||
       ((($now - $this->islandLastTime) >= $init->unitTime) &&
        (($init->endTurn == 0) || ($this->islandTurn < $init->endTurn)))) {
      $cgi->mode = $data['mode'] = 'turn';
      $num = -1;
    }
    for($i = 0; $i < $this->islandNumber; $i++) {
      $this->islands[$i] = $this->readIsland($fp, $num);
      $this->idToNumber[$this->islands[$i]['id']] = $i;
      $this->islands[$i]['allyId'] = array(); // エラー回避のためここで準備
    }
    Util::unlock($fp);
    fclose($fp);
    if($init->allyUse) $this->readAllyFile();
    return true;
  }
  //---------------------------------------------------
  // 島ひとつ読み込む
  //---------------------------------------------------
  function readIsland($fp, $num) {
    global $init;
    list($name, $owner, $monster, $port, $passenger, $fishingboat, $tansaku, $senkan, $viking) = $this->readIslandValues($fp, 9);
    list($id, $starturn) = $this->readIslandValues($fp, 2);
    $prize    = $this->readIslandLine($fp);
    $absent   = $this->readIslandLine($fp);
    list($comment, $comment_turn, $news, $news_point) = $this->readIslandValues($fp, 4);
    $password = $this->readIslandLine($fp);
    list($point, $pots) = $this->readIslandValues($fp, 2);
    list($eisei0, $eisei1, $eisei2, $eisei3, $eisei4, $eisei5) = $this->readIslandValues($fp, 6);
    list($money, $lot, $gold) = $this->readIslandValues($fp, 3);
    list($food, $rice) = $this->readIslandValues($fp, 2);
    list($pop, $peop, $spop) = $this->readIslandValues($fp, 3);
    $area     = $this->readIslandLine($fp);
    list($goods, $alcohol, $wood, $stone, $steel) = $this->readIslandValues($fp, 5);
    list($silver, $material, $oil, $fuel, $explosive, $shell) = $this->readIslandValues($fp, 6);
    list($farm, $market, $hatuden, $service, $milpop, $navy, $sfarmy) = $this->readIslandValues($fp, 7);
    list($factory, $mfactory, $sfactory, $ffactory) = $this->readIslandValues($fp, 4);
    list($mountain, $mining) = $this->readIslandValues($fp, 2);
    list($taiji, $rena, $fire) = $this->readIslandValues($fp, 3);
	list($tenki, $freeze, $hapiness, $siji, $invest, $capital, $Cname, $edinv, $banum, $cmente, $indnum, $polit, $soclv, $bport, $civwar) = $this->readIslandValues($fp, 15);
    $wiki_link    = $this->readIslandLine($fp);
    $trade_link   = $this->readIslandLine($fp);
    $news_link    = $this->readIslandLine($fp);
    
    $this->idToName[$id] = $name;
    $land = array();
    $landValue = array();
    $command = array();
    $lbbs = array();
    $regT = array();

    if(($num == -1) || ($num == $id)) {
      $fp_i = fopen("{$init->dirName}/island.{$id}", "r");
      Util::lockr($fp_i);

      // 地形
      $offset = 5; // 一対のデータが何文字か
      for($y = 0; $y < $init->islandSize; $y++) {
        $line = chop(fgets($fp_i, READ_LINE));
        for($x = 0; $x < $init->islandSize; $x++) {
          $l = substr($line, $x * $offset    , 2);
          $v = substr($line, $x * $offset + 2, 3);
          $land[$x][$y]      = hexdec($l);
          $landValue[$x][$y] = hexdec($v);
        }
      }

      // コマンド
      for($i = 0; $i < $init->commandMax; $i++) {
        $line = chop(fgets($fp_i, READ_LINE));
        list($kind, $target, $x, $y, $arg) = explode(",", $line);
        $command[$i] = array (
          'kind'   => $kind,
          'target' => $target,
          'x'      => $x,
          'y'      => $y,
          'arg'    => $arg
          );
      }
      // ローカル掲示板
      for($i = 0; $i < $init->lbbsMax; $i++) {
        $line = chop(fgets($fp_i, READ_LINE));
        $line = mb_convert_encoding($line, "UTF-8","UTF-8,SJIS");
        $lbbs[$i] = $line;
      }

      // 定期輸送
      for($i = 0; $i < $init->regTMax; $i++) {
        $line = chop(fgets($fp_i, READ_LINE));
    	$regT[$i] = $line;
      }

      Util::unlock($fp_i);
      fclose($fp_i);
    }
    return $this->normalizeIslandData(array(
      'name'     => $name,
      'owner'    => $owner,
      'id'       => $id,
      'starturn' => $starturn,
      'prize'    => $prize,
      'absent'   => $absent,
      'comment'  => $comment,
      'comment_turn' => $comment_turn,
	  'news'	 => $news,
	  'news_point' => $news_point,
      'password' => $password,
      'point'    => $point,
      'pots'     => $pots,
      'money'    => $money,
      'lot'      => $lot,
      'gold'     => $gold,
      'food'     => $food,
      'rice'     => $rice,
      'pop'      => $pop,
	  'spop'	 => $spop,
      'peop'     => $peop,
      'area'     => $area,
      'goods'    => $goods,
      'alcohol'  => $alcohol,
      'wood'     => $wood,
      'stone'    => $stone,
      'steel'    => $steel,
      'silver'   => $silver,
      'material' => $material,
      'oil'      => $oil,
      'fuel'     => $fuel,
      'explosive' => (int)$explosive,
      'shell'    => $shell,
      'farm'     => $farm,
      'factory'  => $factory,
      'market'   => $market,
	  'service'	 => $service,
      'mfactory' => $mfactory,
      'sfactory' => $sfactory,
      'ffactory' => $ffactory,
      'mountain' => $mountain,
      'mining'   => $mining,
      'hatuden'  => $hatuden,
      'monster'  => $monster,
      'taiji'    => (int)$taiji,
      'rena'     => (int)$rena,
      'fire'     => (int)$fire,
      'tenki'    => (int)$tenki,
	  'freeze'	 => (int)$freeze,
      'land'     => $land,                                                        
      'landValue'=> $landValue,
      'command'  => $command,
      'lbbs'     => $lbbs,                                          
	  'regT'	 => $regT,
      'port'     => $port,
      'ship'     => array('passenger' => $passenger, 'fishingboat' => $fishingboat, 'tansaku' => $tansaku, 'senkan' => $senkan, 'viking' => $viking),
      'eisei'    => array(0 => $eisei0, 1 => $eisei1, 2 => $eisei2, 3 => $eisei3, 4 => $eisei4, 5 => $eisei5),
	  'hapiness' => (int)$hapiness,
	  'siji'	 => (int)$siji,
	  'invest'	 => (float)$invest,
	  'capital'  => (int)$capital,
	  'Cname'    => $Cname,
	  'edinv'    => (int)$edinv,
	  'banum'	 => trim($banum),
	  'cmente'	 => (int)$cmente,
	  'indnum'	 => (int)$indnum,
	  'polit'	 => (int)$polit,
	  'soclv'	 => (int)$soclv,
	  'bport'	=> (int)$bport,
	  'milpop'  => (int)$milpop,
	  'navy'	=> $navy,
	  'sfarmy'  => (int)$sfarmy,
	  'civwar'  => (int)$civwar,
	  'wiki_link' => $wiki_link,
	  'trade_link' => $trade_link,
	  'news_link' =>  $news_link
      ));
  }
  //---------------------------------------------------
  // 地形を書き込む
  //---------------------------------------------------
  function writeLand($num, $island) {
    global $init;
    // 地形
    if(($num <= -1) || ($num == $island['id'])) {
      $fileName = "{$init->dirName}/island.{$island['id']}";

      if(!is_file($fileName))
        touch($fileName);

      $fp_i = fopen($fileName, "w");
      Util::lockw($fp_i);
      $land = $island['land'];
      $landValue = $island['landValue'];

      for($y = 0; $y < $init->islandSize; $y++) {
        for($x = 0; $x < $init->islandSize; $x++) {
          $l = sprintf("%02x%03x", $land[$x][$y], $landValue[$x][$y]);
          fputs($fp_i, $l);
        }
        fputs($fp_i, "\n");
      }

      // コマンド
      $command = $island['command'];
      for($i = 0; $i < $init->commandMax; $i++) {
        $com = sprintf("%d,%d,%d,%d,%d\n",
                     $command[$i]['kind'],
                     $command[$i]['target'],
                     $command[$i]['x'],
                     $command[$i]['y'],
                     $command[$i]['arg']
                );
        fputs($fp_i, $com);
      }

      // ローカル掲示板
      $lbbs = $island['lbbs'];
      for($i = 0; $i < $init->lbbsMax; $i++) {
        fputs($fp_i, $lbbs[$i] . "\n");
      }

	  // 定期輸送
      $regT = $island['regT'];
      for($i = 0; $i < $init->regTMax; $i++) {
        fputs($fp_i, $regT[$i] . "\n");
      }

      Util::unlock($fp_i);
      fclose($fp_i);
    }
  }
  //--------------------------------------------------
  // 同盟データ読みこみ
  //--------------------------------------------------
  function readAllyFile() {

    global $init;
    $fileName = "{$init->allyData}";
    if(!is_file($fileName)) {
      return false;
    }
    $fp = fopen($fileName, "r");
    Util::lockr($fp); // ※ 20031015版 以前のスクリプトはここをコメントにする
    $this->allyNumber   = chop(fgets($fp, READ_LINE));
    if($this->allyNumber == '') $this->allyNumber = 0;

    for($i = 0; $i < $this->allyNumber; $i++) {
      $this->ally[$i] = $this->readAlly($fp);
      $this->idToAllyNumber[$this->ally[$i]['id']] = $i;
    }
    // 加盟している同盟のIDを格納
    for($i = 0; $i < $this->allyNumber; $i++) {
      $member = $this->ally[$i]['memberId'];
      $j = 0;
      foreach ($member as $id) {
        $n = $this->idToNumber[$id];
        if(!($n > -1)) continue;
        array_push($this->islands[$n]['allyId'], $this->ally[$i]['id']);
      }
    }
    Util::unlock($fp); // ※ 20031015版 以前のスクリプトはここをコメントにする
    fclose($fp);
    return true;
  }
  //--------------------------------------------------
  // 同盟ひとつ読みこみ
  //--------------------------------------------------
  function readAlly($fp) {
    $name       = chop(fgets($fp, READ_LINE));
    $mark       = chop(fgets($fp, READ_LINE));
    $color      = chop(fgets($fp, READ_LINE));
    $id         = chop(fgets($fp, READ_LINE));
    $ownerName  = chop(fgets($fp, READ_LINE));
    $password   = chop(fgets($fp, READ_LINE));

    $score      = chop(fgets($fp, READ_LINE));
    $number     = chop(fgets($fp, READ_LINE));
    $occupation = chop(fgets($fp, READ_LINE));
    $tmp        = chop(fgets($fp, READ_LINE));
    $allymember = explode(",", $tmp);
    $tmp        = chop(fgets($fp, READ_LINE));
    $ext        = explode(",", $tmp);        // 拡張領域
    $comment    = chop(fgets($fp, READ_LINE));
    $title      = chop(fgets($fp, READ_LINE));
    list($title, $message) = explode("<>", $title);

    return array(
      'name'     => $name,
      'mark'     => $mark,
      'color'    => $color,
      'id'       => $id,
      'oName'    => $ownerName,
      'password' => $password,

      'score'      => $score,
      'number'     => $number,
      'occupation' => $occupation,
      'memberId'   => $allymember,
      'ext'        => $ext,
      'comment'    => $comment,
      'title'      => $title,
      'message'    => $message,
    );
  }
  //---------------------------------------------------
  // 全島データを書き込む
  //---------------------------------------------------
  function writeIslandsFile($num = 0) {
    global $init;
    $fileName = "{$init->dirName}/hakojima.dat";

    if(!is_file($fileName))
      touch($fileName);

    $fp = fopen($fileName, "w");
    Util::lockw($fp);
    fputs($fp, $this->islandTurn . "\n");
    fputs($fp, $this->islandLastTime . "\n");
    fputs($fp, $this->islandNumber . "\n");
    fputs($fp, $this->islandNextID . "\n");
    for($i = 0; $i < $this->islandNumber; $i++) {
      $this->writeIsland($fp, $num, $this->islands[$i]);
    }
    Util::unlock($fp);
    fclose($fp);
  }
  //---------------------------------------------------
  // 統計データを書き込む
  //---------------------------------------------------
  function writeStatistFile($stat) {
    global $init;
    $fileName = "{$init->dirName}/statistic.xml";

	if(!is_file($fileName))
      touch($fileName);

	$xmldata = simplexml_load_file($fileName);

	$year = Util::MKCal($this->islandTurn,1);

	$fact_node = $xmldata->addChild('factdata');
	$chname = array('turn'=>$this->islandTurn,
					'year'=>$year,
					'pop'=>$stat['pop'],
					'farm'=>$stat['farm'],
					'ind'=>$stat['ind'],
					'market'=>$stat['market'],
					'pgoods'=>$stat['pgoods'],
					'pmoneys'=>$stat['pmoneys'],
					'shell'=>$stat['shell']);

	foreach($chname as $key => $value){
		$fact_node->addChild($key,$value);
	}

	$xmldata->asXML($fileName);

  }
   //---------------------------------------------------
  // 国民経済統計データを更新する
  //---------------------------------------------------
  function writeNESTFile($nests) {
    global $init;
    
    $updid = array();
    $nest_temp = array();
	$nest_upd = array();
	$nest_current = array();
	$nest_past = array();
    
    $o_year = Util::MKCal($this->islandTurn-1,1);
	$year = Util::MKCal($this->islandTurn,1);

    $fileName = "{$init->nestDir}/nest.txt";

	if(!is_file($fileName)){
	//ファイルが無かったら初期化＆作成
      touch($fileName);
	}
    
    $fp = fopen($fileName, "r");
    Util::lockr($fp);

	//JSONフォーマットから配列に変換して読み込み
	$json_nest = json_decode(fread($fp, filesize($fileName)),true);

    Util::unlock($fp);
    fclose($fp);

	foreach($json_nest as $nation_nest){
		if($nation_nest['year'] == $year){
			//今年分の配列を切り出す
			$nest_current[] = $nation_nest;
		}else{
			//過去分の配列を切り出す
			$nest_past[] = $nation_nest;
		}
	}
	
	foreach($nests as $nest_item){
		$tyear = $nest_item['year'];
		if($tyear == $o_year){
			//同年中は加算処理を行う
			$tid = $nest_item['id'];
			foreach($nest_current as $nation_nest){
				if($nation_nest['id'] == $tid){
					foreach($nation_nest as $key=>$val){
						if(array_key_exists($key,$init->accountName)){
						 //AccountNameにある$keyの場合は加算
				   		 $amount = $val['amount'];
				   		 $nest_item[$key]['amount'] += $amount;
				   		}
				   	}
				}
			}
		}
		$nest_upd[] = (array)$nest_item;
	}
	
	if($year == $o_year){
	//同年中なら今年分（加算済み）＋過去分
	$nest_upd = array_merge($nest_upd, $nest_past);
	}else{
	//新年なら新年分＋今年分＋過去分
	$nest_upd = array_merge($nest_upd, $nest_current, $nest_past);	
	}

    $fp = fopen($fileName, "w");
    Util::lockw($fp);

	$json_nest = json_encode($nest_upd);
	fwrite($fp,$json_nest);
	
    Util::unlock($fp);
	fclose($fp);
  }
  //---------------------------------------------------
  // 中心都市データを書き込む
  //---------------------------------------------------
  	function writeCCFile(){
    global $init;
    $fileName = "{$init->dirName}/statistic.xml";

	if(!is_file($fileName))
      touch($fileName);

	$xmldata = simplexml_load_file($fileName);

	$year = Util::MKCal($this->islandTurn,1);

	$fact_node = $xmldata->addChild('factdata');
	$chname = array('turn'=>$this->islandTurn,
					'year'=>$year,
					'pop'=>$stat['pop'],
					'farm'=>$stat['farm'],
					'ind'=>$stat['ind'],
					'market'=>$stat['market'],
					'pgoods'=>$stat['pgoods'],
					'pmoneys'=>$stat['pmoneys'],
					'shell'=>$stat['shell']);

	foreach($chname as $key => $value){
		$fact_node->addChild($key,$value);
	}

	$xmldata->asXML($fileName);

	}
  //---------------------------------------------------
  // 島ひとつ書き込む
  //---------------------------------------------------
  function writeIsland($fp, $num, $island) {
    global $init;
    $island = $this->normalizeIslandData($island);
    $ships = $island['ship']['passenger'].",".$island['ship']['fishingboat'].",".$island['ship']['tansaku'].",".$island['ship']['senkan'].",".$island['ship']['viking'];
    $eiseis = $island['eisei']['0'].",".$island['eisei']['1'].",".$island['eisei']['2'].",".$island['eisei']['3'].",".$island['eisei']['4'].",".$island['eisei']['5'];
    fputs($fp, $island['name'] . "," . $island['owner'] . "," . $island['monster'] . "," . $island['port'] . "," . $ships . "\n");
    fputs($fp, $island['id'] . "," . $island['starturn'] . "\n");
    fputs($fp, $island['prize'] . "\n");
    fputs($fp, $island['absent'] . "\n");
    fputs($fp, $island['comment'] . "," . $island['comment_turn'] . "," .$island['news'] . "," . $island['news_point'] . "\n");
    fputs($fp, $island['password'] . "\n");
    fputs($fp, $island['point'] . "," . $island['pots'] . "\n");
    fputs($fp, $eiseis . "\n");
    fputs($fp, $island['money'] . "," . $island['lot'] . "," . $island['gold'] . "\n");
    fputs($fp, $island['food'] . "," . $island['rice'] . "\n");
    fputs($fp, $island['pop'] . "," . $island['peop'] . "," . $island['spop'] . "\n");
    fputs($fp, $island['area'] . "\n");
    fputs($fp, $island['goods'] . "," . $island['alcohol'] . "," . $island['wood'] . "," . $island['stone'] . "," . $island['steel'] . "\n");
    fputs($fp, $island['silver'] . "," . $island['material'] . "," . $island['oil'] . "," . $island['fuel'] . "," . $island['explosive'] . "," . $island['shell'] . "\n");
    fputs($fp, $island['farm'] . "," . $island['market'] . "," . $island['hatuden'] .  "," . $island['service'] . "," . $island['milpop'] . "," . $island['navy'] . "," . $island['sfarmy'] . "\n");
    fputs($fp, $island['factory'] . "," . $island['mfactory'] . "," . $island['sfactory'] . "," . $island['ffactory'] . "\n");
    fputs($fp, $island['mountain'] . "," . $island['mining'] . "\n");
    fputs($fp, $island['taiji'] . "," . $island['rena'] . "," . $island['fire'] . "\n");
    fputs($fp, $island['tenki'] . "," .  $island['freeze']  . "," .  $island['hapiness'].",".$island['siji'].",".$island['invest'].",".$island['capital'].",".$island['Cname'].",".$island['edinv'].",".$island['banum'].",".$island['cmente']. ",".$island['indnum']. "," .  $island['polit']. "," .$island['soclv']. "," .$island['bport']."," .$island['civwar']."\n");
    fputs($fp, $island['wiki_link'] . "\n");
    fputs($fp, $island['trade_link'] . "\n");
    fputs($fp, $island['news_link'] . "\n");

    // 地形
    if(($num <= -1) || ($num == $island['id'])) {
      $fileName = "{$init->dirName}/island.{$island['id']}";

      if(!is_file($fileName))
        touch($fileName);

      $fp_i = fopen($fileName, "w");
      Util::lockw($fp_i);
      $land = $island['land'];
      $landValue = $island['landValue'];

      for($y = 0; $y < $init->islandSize; $y++) {
        for($x = 0; $x < $init->islandSize; $x++) {
          $l = sprintf("%02x%03x", $land[$x][$y], $landValue[$x][$y]);
          fputs($fp_i, $l);
        }
        fputs($fp_i, "\n");
      }

      // コマンド
      $command = $island['command'];
      for($i = 0; $i < $init->commandMax; $i++) {
        $com = sprintf("%d,%d,%d,%d,%d\n",
                     $command[$i]['kind'],
                     $command[$i]['target'],
                     $command[$i]['x'],
                     $command[$i]['y'],
                     $command[$i]['arg']
                );
        fputs($fp_i, $com);
      }

      // ローカル掲示板
      $lbbs = $island['lbbs'];
      for($i = 0; $i < $init->lbbsMax; $i++) {
        fputs($fp_i, $lbbs[$i] . "\n");
      }

	  // 定期輸送
      $regT = $island['regT'];
      for($i = 0; $i < $init->regTMax; $i++) {
        fputs($fp_i, $regT[$i] . "\n");
      }


      Util::unlock($fp_i);
      fclose($fp_i);
    }
  }
  //---------------------------------------------------
  // データのバックアップ
  //---------------------------------------------------
  function backUp() {
    global $init;

    if($init->backupTimes <= 0)
      return;

    $tmp = $init->backupTimes - 1;
    $this->rmTree("{$init->dirName}.bak{$tmp}");
    for($i = ($init->backupTimes - 1); $i > 0; $i--) {
      $j = $i - 1;
      if(is_dir("{$init->dirName}.bak{$j}")){
        rename("{$init->dirName}.bak{$j}", "{$init->dirName}.bak{$i}");
       }
    }
    if(is_dir("{$init->dirName}")){
      rename("{$init->dirName}", "{$init->dirName}.bak0");
    }
    
    mkdir("{$init->dirName}", $init->dirMode);

    // ログ・履歴・統計はバックアップにも残し、現役側に引き継ぐ
    for($i = 0; $i <= $init->logMax; $i++) {
      if(is_file("{$init->dirName}.bak0/hakojima.log{$i}")){
        copy("{$init->dirName}.bak0/hakojima.log{$i}", "{$init->dirName}/hakojima.log{$i}");
       }
    }
    if(is_file("{$init->dirName}.bak0/hakojima.his")){
      copy("{$init->dirName}.bak0/hakojima.his", "{$init->dirName}/hakojima.his");
    }
      
  //統計ファイルを戻す
   if(is_file("{$init->dirName}.bak0/statistic.xml")){
      copy("{$init->dirName}.bak0/statistic.xml", "{$init->dirName}/statistic.xml");
	  }
	}
  //---------------------------------------------------
  // 不要なディレクトリとファイルを削除
  //---------------------------------------------------
  function rmTree($dirName) {
    if(is_dir("{$dirName}")) {
      $dir = opendir("{$dirName}/");
      while($fileName = readdir($dir)) {
        if(!(strcmp($fileName, ".") == 0 || strcmp($fileName, "..") == 0))
          unlink("{$dirName}/{$fileName}");
      }
      closedir($dir);
      rmdir($dirName);
    }
  }
  //---------------------------------------------------
  // プレゼント関係
  //---------------------------------------------------
  function readPresentFile( $erase = false ) {
    global $init;

    for($i = 0; $i < $this->islandNumber; $i++) {
      if(!isset($this->islands[$i]['present']) || !is_array($this->islands[$i]['present'])) {
        $this->islands[$i]['present'] = array();
      }
      $this->islands[$i]['present']['item'] = 0;
      $this->islands[$i]['present']['px'] = 0;
      $this->islands[$i]['present']['py'] = 0;
    }

    $fileName = "{$init->dirName}/present.dat";
    if(is_file($fileName)) {
      $presents = file($fileName);
      foreach ($presents as $present) {
        $present = array_pad(explode(",", chop($present), 4), 4, 0);
        list($id, $item, $px, $py) = $present;
        if(!isset($this->idToNumber[$id])) {
          continue;
        }
        $num = $this->idToNumber[$id];
        $this->islands[$num]['present']['item'] = is_numeric($item) ? (int)$item : 0;
        $this->islands[$num]['present']['px'] = is_numeric($px) ? (int)$px : 0;
        $this->islands[$num]['present']['py'] = is_numeric($py) ? (int)$py : 0;
      }
      if ( $erase )
        unlink($fileName);
    }
  }
  function writePresentFile() {
    global $init;

    $presents = array();
    $fileName = "{$init->dirName}/present.dat";
    for($i = 0; $i < $this->islandNumber; $i++) {
      if(!isset($this->islands[$i]['present']) || !is_array($this->islands[$i]['present'])) {
        $this->islands[$i]['present'] = array('item' => 0, 'px' => 0, 'py' => 0);
      }
      $present =& $this->islands[$i]['present'];
      $present['item'] = isset($present['item']) ? $present['item'] : 0;
      $present['px'] = isset($present['px']) ? $present['px'] : 0;
      $present['py'] = isset($present['py']) ? $present['py'] : 0;
      if ((( $present['item'] == 0 ) && (( $present['px'] != 0 ) || (
$present['py'] != 0 ))) ||
          (( $present['item'] > 0 ) && ( $present['item'] < 9 ))) {
          $presents[] = $this->islands[$i]['id'] . ',' .
$present['item'] . ',' . $present['px'] . ',' .
$present['py'] . "\n";
      }
    }
    $num = count($presents);
    $fp = fopen($fileName, "w");
    if ( $num > 0 ) {
      for ($i = 0; $i < $num ; $i++) {
        fputs( $fp, $presents[$i]);
      }
    }
    fclose($fp);
  }
}

?>
