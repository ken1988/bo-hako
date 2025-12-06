<?php
require_once __DIR__.'/../partials/card-resource.php';

/** 既存の $island と $init から、資源カード定義を作成 */
$resources = [
  // fmt: Rewriter2用の種別 / about: aboutMoney用の種別
  ['id'=>'money',   'label'=>'資金',   'unit'=>$init->unitMoney,   'fmt'=>'money','about'=>'money',
    'amount'=>$island['money'] ?? 0,    'capacity'=>$init->maxMoney ?? null,   'icon'=>''],
  ['id'=>'food',    'label'=>'食料',   'unit'=>$init->unitFood,    'fmt'=>'food', 'about'=>'food',
    'amount'=>$island['food'] ?? 0,     'capacity'=>$init->maxFood ?? null,    'icon'=>''],
  ['id'=>'goods',   'label'=>'商品',   'unit'=>$init->unitGoods,   'fmt'=>'money','about'=>'goods',
    'amount'=>$island['goods'] ?? 0,    'capacity'=>$init->maxGoods ?? null,   'icon'=>''],
  ['id'=>'material','label'=>'建材',   'unit'=>$init->unitMaterial,'fmt'=>'oil',  'about'=>'material',
    'amount'=>$island['material'] ?? 0, 'capacity'=>$init->maxMaterial ?? null,'icon'=>''],
  ['id'=>'fuel',    'label'=>'燃料',   'unit'=>$init->unitFuel,    'fmt'=>'oil',  'about'=>'fuel',
    'amount'=>$island['fuel'] ?? 0,     'capacity'=>$init->maxFuel ?? null,    'icon'=>''],
  ['id'=>'shell',   'label'=>'砲弾',   'unit'=>$init->unitShell,   'fmt'=>null,   'about'=>'shell', // Rewriter2無しでそのまま
    'amount'=>$island['shell'] ?? 0,    'capacity'=>$init->maxShell ?? null,   'icon'=>''],

  ['id'=>'wood',    'label'=>'木材',   'unit'=>$init->unitWood,    'fmt'=>'oil',  'about'=>'wood',
    'amount'=>$island['wood'] ?? 0,     'capacity'=>$init->maxWood ?? null,    'icon'=>''],
  ['id'=>'stone',   'label'=>'石材',   'unit'=>$init->unitStone,   'fmt'=>'oil',  'about'=>'stone',
    'amount'=>$island['stone'] ?? 0,    'capacity'=>$init->maxStone ?? null,   'icon'=>''],
  ['id'=>'steel',   'label'=>'鉄鋼',   'unit'=>$init->unitSteel,   'fmt'=>'oil',  'about'=>'steel',
    'amount'=>$island['steel'] ?? 0,    'capacity'=>$init->maxSteel ?? null,   'icon'=>''],

  ['id'=>'oil',     'label'=>'石油',   'unit'=>$init->unitOil,     'fmt'=>'oil',  'about'=>'oil',
    'amount'=>$island['oil'] ?? 0,      'capacity'=>$init->maxOil ?? null,     'icon'=>''],
  ['id'=>'alcohol', 'label'=>'酒',     'unit'=>$init->unitAlcohol, 'fmt'=>'silver','about'=>'alcohol',
    'amount'=>$island['alcohol'] ?? 0,  'capacity'=>$init->maxAlcohol ?? null, 'icon'=>''],
  ['id'=>'silver',  'label'=>'銀',     'unit'=>$init->unitSilver,  'fmt'=>'silver','about'=>'silver',
    'amount'=>$island['silver'] ?? 0,   'capacity'=>$init->maxSilver ?? null,  'icon'=>''],

  // 弾薬など、存在すれば追加
  <?php /* 例:
  ['id'=>'explosive','label'=>'弾薬','unit'=>$init->unitExplosive,'fmt'=>'oil','about'=>'explosive',
    'amount'=>$island['explosive'] ?? 0,'capacity'=>$init->maxExplosive ?? null,'icon'=>''],
  */ ?>
];

// 描画
render_resource_grid($resources, $init);