<?php
/**
 * Template Name: Forex Spread Cost Calculator
 * Description: Estimates annual EUR/USD trading cost at every researched
 * broker with a confirmed spread, from the trader's own account size,
 * trade frequency, trade size, and holding period. See
 * inc/cost-calculator.php for exactly what is and isn't counted.
 */
get_header();

$cc_holding_periods = globalfxhub_cost_calculator_holding_periods();
$cc_payload = globalfxhub_cost_calculator_payload();
$cc_pip_value = globalfxhub_cost_calculator_pip_value_per_lot();
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php the_title(); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'cc_eyebrow' ); ?></div>
  <h1><?php the_title(); ?></h1>
  <p><?php globalfxhub_te( 'cc_intro' ); ?></p>
  <?php $tool_learn_link = globalfxhub_resolve_learn_link( globalfxhub_tool_learn_links()['cost-calculator'] ); if ( $tool_learn_link ) : ?>
  <p style="font-size:13.5px;margin-top:8px;"><a href="<?php echo esc_url( $tool_learn_link['url'] ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'cmp_new_to_this' ); ?> <?php echo esc_html( $tool_learn_link['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="finder">
    <div class="finder__step">
      <label for="ccAccountSize"><?php globalfxhub_te( 'cc_label_account_size' ); ?></label>
      <input type="number" id="ccAccountSize" min="0" step="1" value="1000">
    </div>
    <div class="finder__step">
      <label for="ccTradesPerMonth"><?php globalfxhub_te( 'cc_label_trades_per_month' ); ?></label>
      <input type="number" id="ccTradesPerMonth" min="0" step="1" value="20">
    </div>
    <div class="finder__step">
      <label for="ccTradeSize"><?php globalfxhub_te( 'cc_label_trade_size' ); ?></label>
      <input type="number" id="ccTradeSize" min="0" step="0.01" value="0.10">
      <p class="finder__hint"><?php globalfxhub_te( 'cc_hint_lot_sizes' ); ?></p>
    </div>
    <div class="finder__step">
      <label for="ccHoldingPeriod"><?php globalfxhub_te( 'cc_label_holding_period' ); ?></label>
      <select id="ccHoldingPeriod">
        <?php foreach ( $cc_holding_periods as $key => $label ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div id="ccOvernightNote" class="methodology-note" style="display:none;margin:0 0 24px;">
    <strong><?php globalfxhub_te( 'cc_overnight_strong' ); ?></strong> <?php globalfxhub_te( 'cc_overnight_body' ); ?>
  </div>

  <div id="ccMarginNote" class="methodology-note" style="display:none;margin:0 0 24px;"></div>

  <div class="finder__summary">
    <div class="finder__count"><?php globalfxhub_te( 'cc_results_label' ); ?> (<span id="ccResultCount">0</span> <?php globalfxhub_te( 'cc_results_of' ); ?> <?php echo esc_html( $cc_payload['total_count'] ); ?> <?php globalfxhub_te( 'cc_results_suffix' ); ?>)</div>
  </div>

  <div class="rankings" id="ccResults"></div>

  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;">
    <?php echo esc_html( sprintf( globalfxhub_t( 'cc_disclaimer_p1' ), $cc_payload['excluded_count'], $cc_payload['total_count'] ) ); ?>
    <?php
    $cc_spread_cost_only_html = '<strong>' . esc_html( globalfxhub_t( 'cc_spread_cost_only' ) ) . '</strong>';
    echo sprintf( esc_html( globalfxhub_t( 'cc_disclaimer_p2' ) ), $cc_spread_cost_only_html );
    ?>
    <?php globalfxhub_te( 'cc_disclaimer_p3' ); ?>
    <?php globalfxhub_te( 'cc_formula_label' ); ?> <?php echo esc_html( sprintf( globalfxhub_t( 'cc_formula_body' ), $cc_pip_value ) ); ?>
    <?php
    $cc_methodology_link_html = '<a href="' . esc_url( home_url( '/#method' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'cc_disclosed_methodology_link' ) ) . '</a>';
    echo sprintf( esc_html( globalfxhub_t( 'cc_built_from_disclosed' ) ), $cc_methodology_link_html );
    ?>
    <?php globalfxhub_te( 'cc_verify_spreads' ); ?>
  </p>
</div>

<script>
const CC_PIP_VALUE_PER_LOT = <?php echo wp_json_encode( $cc_pip_value ); ?>;
const CC_BROKERS = <?php echo wp_json_encode( $cc_payload['brokers'] ); ?>;
// Translated strings for the JS-rendered results table and margin
// note below, which can't be rendered server-side since they depend
// on the visitor's own inputs (trade size, frequency, account size).
const T = <?php echo wp_json_encode( array(
    'enter_inputs' => globalfxhub_t( 'cc_js_enter_inputs' ),
    'col_broker' => globalfxhub_t( 'table_broker' ),
    'col_spread' => globalfxhub_t( 'cc_col_spread' ),
    'col_cost_per_trade' => globalfxhub_t( 'cc_col_cost_per_trade' ),
    'col_annual_cost' => globalfxhub_t( 'cc_col_annual_cost' ),
    'pips_suffix' => globalfxhub_t( 'cc_pips_suffix' ),
    'of_account' => globalfxhub_t( 'cc_of_account' ),
    'read_review' => globalfxhub_t( 'btn_read_review' ),
    'margin_strong' => globalfxhub_t( 'cc_margin_strong' ),
    'margin_body' => globalfxhub_t( 'cc_margin_body' ),
) ); ?>;

(function(){
  const accountEl = document.getElementById('ccAccountSize');
  const tradesEl = document.getElementById('ccTradesPerMonth');
  const sizeEl = document.getElementById('ccTradeSize');
  const holdingEl = document.getElementById('ccHoldingPeriod');
  const overnightNote = document.getElementById('ccOvernightNote');
  const marginNote = document.getElementById('ccMarginNote');
  const countEl = document.getElementById('ccResultCount');
  const resultsEl = document.getElementById('ccResults');

  function render(rows) {
    countEl.textContent = rows.length;
    if (!rows.length) {
      resultsEl.innerHTML = '<p style="padding:24px;color:var(--ink-soft);">' + T.enter_inputs + '</p>';
      return;
    }
    let html = '<div class="rank-row head"><span></span><span>' + T.col_broker + '</span><span class="col-fees">' + T.col_spread + '</span><span class="col-plat">' + T.col_cost_per_trade + '</span><span>' + T.col_annual_cost + '</span><span></span></div>';
    rows.forEach(function(r, i) {
      html += '<div class="rank-row">'
        + '<span class="rank-num">' + String(i + 1).padStart(2, '0') + '</span>'
        + '<span class="rank-broker"><span class="rank-broker__name">' + r.name + '</span><span class="rank-broker__tag">' + r.regulation_label + '</span></span>'
        + '<span class="rank-detail col-fees">' + r.spread_eurusd + ' ' + T.pips_suffix + '</span>'
        + '<span class="rank-detail col-plat">$' + r.cost_per_trade.toFixed(2) + '</span>'
        + '<span class="rank-score">$' + r.annual.toFixed(2) + (r.pct_of_account !== null ? ' <span style="color:var(--ink-soft);font-size:12px;">(' + r.pct_of_account.toFixed(2) + '% ' + T.of_account + ')</span>' : '') + '</span>'
        + '<span class="rank-ctas">'
        + '<a href="' + r.review_url + '" class="rank-cta">' + T.read_review + '</a>'
        + '</span>'
        + '</div>';
    });
    resultsEl.innerHTML = html;
  }

  function recalc() {
    const accountSize = parseFloat(accountEl.value) || 0;
    const tradesPerMonth = parseFloat(tradesEl.value) || 0;
    const tradeSizeLots = parseFloat(sizeEl.value) || 0;
    const holding = holdingEl.value;

    overnightNote.style.display = (holding === 'intraday') ? 'none' : 'block';

    if (accountSize > 0) {
      const maxLots = Math.round((accountSize * 30 / 100000) * 100) / 100;
      if (tradeSizeLots > maxLots) {
        marginNote.style.display = 'block';
        marginNote.innerHTML = '<strong>' + T.margin_strong + '</strong> ' + T.margin_body
          .replace('{ACCOUNT}', accountSize.toLocaleString())
          .replace('{MAXLOTS}', maxLots)
          .replace('{TRADESIZE}', tradeSizeLots);
      } else {
        marginNote.style.display = 'none';
      }
    } else {
      marginNote.style.display = 'none';
    }

    const rows = [];
    if (tradeSizeLots > 0 && tradesPerMonth > 0) {
      CC_BROKERS.forEach(function(b) {
        const costPerTrade = b.spread_eurusd * CC_PIP_VALUE_PER_LOT * tradeSizeLots;
        const annual = costPerTrade * tradesPerMonth * 12;
        rows.push({
          slug: b.slug,
          name: b.name,
          rank: b.rank,
          spread_eurusd: b.spread_eurusd,
          regulation_label: b.regulation_label,
          review_url: b.review_url,
          cost_per_trade: costPerTrade,
          annual: annual,
          pct_of_account: accountSize > 0 ? (annual / accountSize) * 100 : null,
        });
      });
      rows.sort(function(a, b) {
        if (a.annual !== b.annual) return a.annual - b.annual;
        return a.rank - b.rank;
      });
    }
    render(rows);
  }

  [accountEl, tradesEl, sizeEl, holdingEl].forEach(function(el) {
    el.addEventListener('input', recalc);
    el.addEventListener('change', recalc);
  });

  recalc();
})();
</script>

<?php get_footer(); ?>
