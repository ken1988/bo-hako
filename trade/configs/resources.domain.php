<?php
// $init を受け取り、資源の情報を返す
return function($init){
  var $unitMoney        = "0億Va";     // 資金の単位
  var $unitGoods        = "0億Va相当"; // 商品の単位
  var $unitOil          = "0万バレル"; // 石油の単位
  var $unitFuel         = "0万ガロン"; // 燃料の単位
  var $unitShell        = "0メガトン"; // 砲弾の単位
  var $unitResourceNT   = "0トン";     // 資源の単位（トン単位）
  var $unitResourceMT   = "0万トン";   // 資源の単位（万トン単位）

  return [
    'money'    => ['label'=>'資金', 'unit'=>$unitMoney,        'initial'=>1500,  'max'=>300000   ?? null,  'fmt'=>'money',  'about'=>'money'],
    'food'     => ['label'=>'食料', 'unit'=>$unitResourceMT,   'initial'=>1500,  'max'=>10000000    ?? null,  'fmt'=>'food',   'about'=>'food'],
    'goods'    => ['label'=>'商品', 'unit'=>$unitGoods,        'initial'=>0,     'max'=>300000   ?? null,  'fmt'=>'money',  'about'=>'goods'],
    'material' => ['label'=>'建材', 'unit'=>$unitResourceMT,'initial'=>500,  'max'=>50000?? null,  'fmt'=>'oil',    'about'=>'material'],
    'fuel'     => ['label'=>'燃料', 'unit'=>$unitFuel,   'initial'=>1000,  'max'=>100000?? null,  'fmt'=>'oil',    'about'=>'fuel'],
    'shell'    => ['label'=>'砲弾', 'unit'=>$unitShell,  'initial'=>0,     'max'=>99999?? null,  'fmt'=>null,     'about'=>'shell'],
    'wood'     => ['label'=>'木材', 'unit'=>$unitResourceMT,   'initial'=>500,   'max'=>50000    ?? null,  'fmt'=>'oil',    'about'=>'wood'],
    'stone'    => ['label'=>'石材', 'unit'=>$unitResourceMT,  'initial'=>500,   'max'=>10000   ?? null,  'fmt'=>'oil',    'about'=>'stone'],
    'steel'    => ['label'=>'鉄鋼', 'unit'=>$unitResourceMT,  'initial'=>500,   'max'=>20000   ?? null,  'fmt'=>'oil',    'about'=>'steel'],
    'oil'      => ['label'=>'石油', 'unit'=>$unitOil,    'initial'=>1000,  'max'=>100000     ?? null,  'fmt'=>'oil',    'about'=>'oil'],
    'alcohol'  => ['label'=>'肉',   'unit'=>$unitResourceNT,   'initial'=>0,     'max'=>100000 ?? null,  'fmt'=>'silver', 'about'=>'alcohol'],
    'silver'   => ['label'=>'銀',   'unit'=>$unitResourceNT,   'initial'=>0,     'max'=>20000  ?? null,  'fmt'=>'silver', 'about'=>'silver'],
    'uranium'   => ['label'=>'ウラン',   'unit'=>$unitResourceNT,   'initial'=>0,     'max'=>10000  ?? null,  'fmt'=>'silver', 'about'=>'silver'],
    // 追加資源が出たらここへ
  ];
};
?>