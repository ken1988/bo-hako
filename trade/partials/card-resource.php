<?php
/**
 * 既存の $init, $island, Util をそのまま利用して資源カードを描画する。
 * - amount は数値で受け取り、表示は aboutMoney / Rewriter2 で整形
 * - capacity があればメーター表示、無ければメーター非表示
 * - income/expense が未提供ならデルタ表示を隠す
 */

function _res_format_display(array $r, $init){
  $amount = (float)($r['amount'] ?? 0);
  $unit   = $r['unit'] ?? '';
  $fmt    = $r['fmt']  ?? null;   // Rewriter2 用の種別: 'money' | 'food' | 'oil' | 'silver'
  $about  = $r['about']?? null;   // aboutMoney の第2引数: 'money' 等

  // moneyMode時だけ、従来どおり aboutMoney を優先（money/food/goods/material/fuel/shell）
  $aboutTargets = ['money','food','goods','material','fuel','shell'];
  if ($init->moneyMode && $about && in_array($about, $aboutTargets, true)) {
    return Util::aboutMoney($amount, $about); // 単位込みの文字列が返る想定
  }

  // それ以外は Rewriter2 + 単位
  if ($fmt){
    return Util::Rewriter2($fmt, $amount) . $unit;
  }
  // フォールバック
  return number_format($amount) . $unit;
}

function render_resource_card(array $r, $init){
  $id       = htmlspecialchars($r['id'] ?? uniqid('res_'), ENT_QUOTES, 'UTF-8');
  $label    = htmlspecialchars($r['label'] ?? 'Resource', ENT_QUOTES, 'UTF-8');
  $icon     = htmlspecialchars($r['icon'] ?? '', ENT_QUOTES, 'UTF-8');

  $amount   = (float)($r['amount'] ?? 0);
  $capacity = isset($r['capacity']) ? (float)$r['capacity'] : null;

  $income   = isset($r['income'])  ? (float)$r['income']  : null;
  $expense  = isset($r['expense']) ? (float)$r['expense'] : null;
  $hasDelta = ($income !== null && $expense !== null);

  $display  = _res_format_display($r, $init);

  // メーター計算
  $meterClass = ''; $meterStyle = ''; $capText = '';
  if ($capacity !== null && $capacity > 0){
    $ratio = max(0, min(1, $amount / $capacity));
    $meterClass = ($ratio >= 0.9) ? 'bad' : (($ratio >= 0.75) ? 'warn' : 'good');
    $meterStyle = 'style="width:'.round($ratio*100,1).'%"';
    $capText    = '容量 '.number_format((int)$capacity);
  }

  // ネット増減
  $deltaClass = 'zero'; $deltaText = '±0';
  if ($hasDelta){
    $net = $income - $expense;
    $deltaClass = $net > 0 ? 'pos' : ($net < 0 ? 'neg' : 'zero');
    $deltaText  = ($net > 0 ? '+' : ($net < 0 ? '−' : '±')) . number_format(abs($net));
  }
?>
<section class="card resource-card" data-resource-id="<?= $id ?>" aria-busy="false">
  <header>
    <div class="title">
      <?php if($icon): ?><img src="<?= $icon ?>" alt="" loading="lazy"><?php endif; ?>
      <span><?= $label ?></span>
    </div>
    <?php if(!empty($r['badge'])): ?><span class="badge"><?= htmlspecialchars($r['badge'],ENT_QUOTES,'UTF-8') ?></span><?php endif; ?>
  </header>

  <div class="value-row">
    <div class="value" aria-label="現在量"><?= $display ?></div>
    <?php if($hasDelta): ?>
      <div class="delta <?= $deltaClass ?>" aria-label="増減/ターン"><?= $deltaText ?></div>
    <?php endif; ?>
  </div>

  <?php if($capacity !== null && $capacity > 0): ?>
    <div class="meter <?= $meterClass ?>" role="meter"
         aria-valuemin="0" aria-valuenow="<?= (int)$amount ?>" aria-valuemax="<?= (int)$capacity ?>">
      <i <?= $meterStyle ?>></i>
    </div>
    <div class="meta"><span><?= $capText ?></span></div>
  <?php endif; ?>
</section>
<?php
}

function render_resource_grid(array $resources, $init){
  echo '<div class="resource-grid">';
  foreach($resources as $r) render_resource_card($r, $init);
  echo '</div>';
}
