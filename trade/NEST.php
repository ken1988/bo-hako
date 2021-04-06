<?
//Content-Typeを「application/json」に設定します。
header("Content-Type: application/json; charset=UTF-8");
header("X-Content-Type-Options: nosniff");

$from_year = htmlentities($_GET['from'], ENT_QUOTES, "utf-8");
$to_year =  htmlentities($_GET['to'], ENT_QUOTES, "utf-8");
$id = htmlentities($_GET['id'], ENT_QUOTES, "utf-8");

if(empty($_GET['id'])){
	$mode = 'ALL';
}
if(empty($_GET['to'])){
	$to_year = $from_year;
}

$jsons = new NEST_functions();
$nest = $jsons->MakeNEST($from_year,$to_year,$id,$mode);

//JSON形式で結果を返します。
echo json_encode($nest);
  //---------------------------------------------------
  //国民経済統計出力
  //---------------------------------------------------
  Class NEST_functions{
  	function MakeNEST($from_year,$to_year,$pid,$mode){
    $fileName = "./nest/nest.txt";
	$fp = fopen($fileName, "r");
	$json_nest = json_decode(fread($fp, filesize($fileName)),true);
	fclose($fp);
	
	$res = array();
	
	foreach($json_nest as $nest_item){
		if((($mode == 'ALL')||($nest_item['id'] == $pid)) &&
		   (($nest_item['year'] >= $from_year)&&($nest_item['year'] <= $to_year))){
		   	$res[] = $nest_item;
		   }
	}
	return $res;
	}
}
?>