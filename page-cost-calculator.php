<?php
/**
 * Template Name: True Cost Calculator
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
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">TRUE COST CALCULATOR</div>
  <h1><?php the_title(); ?></h1>
  <p>Estimate your annual EUR/USD spread cost at every one of our researched brokers with a confirmed spread, based on your own trading pattern -- not a generic "our average spread" figure.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="finder">
    <div class="finder__step">
      <label for="ccAccountSize">Account size (USD)</label>
      <input type="number" id="ccAccountSize" min="0" step="1" value="1000">
    </div>
    <div class="finder__step">
      <label for="ccTradesPerMonth">EUR/USD trades per month</label>
      <input type="number" id="ccTradesPerMonth" min="0" step="1" value="20">
    </div>
    <div class="finder__step">
      <label for="ccTradeSize">Average trade size (lots)</label>
      <input type="number" id="ccTradeSize" min="0" step="0.01" value="0.10">
      <p class="finder__hint">1.0 = standard lot (100,000 units), 0.1 = mini lot, 0.01 = micro lot.</p>
    </div>
    <div class="finder__step">
      <label for="ccHoldingPeriod">Typical holding period</label>
      <select id="ccHoldingPeriod">
        <?php foreach ( $cc_holding_periods as $key => $label ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div id="ccOvernightNote" class="methodology-note" style="display:none;margin:0 0 24px;">
    <strong>Overnight financing isn't included in this estimate.</strong> Since your selected holding period involves holding positions overnight, swap/financing charges will add to your real cost. Swap rates vary by broker, by direction (long vs. short), and change daily with benchmark rates -- they're documented as research notes on each broker's review page, not as a single comparable number we could multiply in here with confidence. Check the "Overnight financing" section on a broker's review page before trading with a multi-day holding period.
  </div>

  <div id="ccMarginNote" class="methodology-note" style="display:none;margin:0 0 24px;"></div>

  <div class="finder__summary">
    <div class="finder__count">Estimated annual cost, cheapest first (<span id="ccResultCount">0</span> of <?php echo esc_html( $cc_payload['total_count'] ); ?> researched brokers)</div>
  </div>

  <div class="rankings" id="ccResults"></div>

  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;max-width:78ch;">
    <?php echo esc_html( $cc_payload['excluded_count'] ); ?> of our <?php echo esc_html( $cc_payload['total_count'] ); ?> researched brokers have no independently confirmed EUR/USD spread and are excluded from this comparison entirely, rather than assumed to be free or average. This estimate covers <strong>spread cost only</strong> -- round-turn commission, common on ECN/Razor-style accounts, is a real additional cost documented in free-text research notes per broker, not as a structured number we could average with confidence, so it is not counted here. A broker with the lowest spread shown is not necessarily the lowest true cost once its commission is included -- check the "Execution model" section on that broker's review page. Formula: spread (pips) &times; $<?php echo esc_html( $cc_pip_value ); ?> per pip per standard lot (universal EUR/USD pip-value arithmetic, since USD is the quote currency) &times; trade size (lots) &times; trades/month &times; 12. Built from the same <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">disclosed methodology</a> used sitewide. Verify current spreads directly with the broker before trading -- spreads are variable in practice and the figure here is the one on file from our research.
  </p>
</div>

<script>
const CC_PIP_VALUE_PER_LOT = <?php echo wp_json_encode( $cc_pip_value ); ?>;
const CC_BROKERS = <?php echo wp_json_encode( $cc_payload['brokers'] ); ?>;

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
      resultsEl.innerHTML = '<p style="padding:24px;color:var(--ink-soft);">Enter a trade size and trade frequency above to see estimated costs.</p>';
      return;
    }
    let html = '<div class="rank-row head"><span></span><span>Broker</span><span class="col-fees">EUR/USD spread</span><span class="col-plat">Cost per trade</span><span>Est. annual cost</span><span></span></div>';
    rows.forEach(function(r, i) {
      html += '<div class="rank-row">'
        + '<span class="rank-num">' + String(i + 1).padStart(2, '0') + '</span>'
        + '<span class="rank-broker"><span class="rank-broker__name">' + r.name + '</span><span class="rank-broker__tag">' + r.regulation_label + '</span></span>'
        + '<span class="rank-detail col-fees">' + r.spread_eurusd + ' pips</span>'
        + '<span class="rank-detail col-plat">$' + r.cost_per_trade.toFixed(2) + '</span>'
        + '<span class="rank-score">$' + r.annual.toFixed(2) + (r.pct_of_account !== null ? ' <span style="color:var(--ink-soft);font-size:12px;">(' + r.pct_of_account.toFixed(2) + '% of account)</span>' : '') + '</span>'
        + '<span class="rank-ctas">'
        + '<a href="' + r.review_url + '" class="rank-cta">Read review</a>'
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
        marginNote.innerHTML = '<strong>Trade size may not be feasible at this account size.</strong> At the EU retail leverage cap of 30:1 on major pairs, a $' + accountSize.toLocaleString() + ' account supports up to approximately ' + maxLots + ' standard lots of margin. Your selected trade size of ' + tradeSizeLots + ' lots would need more margin than that, higher leverage than the EU retail cap permits (available only to eligible professional clients, or outside the EU/UK where rules differ), or a larger account.';
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
