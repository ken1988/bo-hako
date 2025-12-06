<?php
return [
  '_defaults' => [
    'warn_threshold' => 0.75,
    'bad_threshold'  => 0.90,
    'icon_base'      => '/assets/icons', // 無ければ null のまま
  ],
  // 並び順（数値小さいほど先頭）
  'order' => ['money','food','goods','material','fuel','wood','stone','steel','oil','alcohol','silver','shell'],
  // 個別UI上書き（あれば）
  'money' => ['icon'=>null],
  'food'  => ['icon'=>null],
  // ...
];
?>