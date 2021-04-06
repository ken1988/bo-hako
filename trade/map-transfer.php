<?php
require 'jcode.phps';
require 'config.php';
require 'hako-util.php';

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
?>