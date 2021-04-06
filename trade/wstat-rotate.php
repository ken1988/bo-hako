<?
$fileName = "./data/statistic.xml";
$ofileName = "./data/statistic_bak.xml";

if(!is_file($fileName)){
  echo "ファイルが存在しない";
}

$xmlstr = <<<XML
<?xml version="1.0" encoding="SHIFT_JIS"?>
<world_fact>
</world_fact>
XML;

$xmldata = simplexml_load_file($fileName);
$nxmldata = new SimpleXMLElement($xmlstr);

rename($fileName,$ofileName);

	foreach ($xmldata->factdata as $factdata) {
		if($factdata->year == 0){
			$factdata->year = floor($factdata->turn / 36);
			echo "書換えターン：".$factdata->turn."<br>";
		}
		if($factdata->year > 399){
			$fact_node = $nxmldata->addChild('factdata');
			echo $factdata->turn ."ターンのデータを移動<br>";
			foreach ($factdata as $key => $value){
				$fact_node->addChild($key,$value);
			}
		}
	}

$nxmldata->asXML($fileName);
echo "処理完了";
?>