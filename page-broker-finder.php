<?php
/**
 * Template Name: Broker Finder
 * Description: Interactive, client-side faceted filter over the same
 * 139-broker dataset and same disclosed nine-category scores used
 * sitewide -- country, deposit, platform, instruments, regulation
 * preference, and trading style. See inc/broker-finder.php for every
 * filter's exact definition.
 */
get_header();

$finder_countries   = globalfxhub_finder_country_options();
$finder_deposits    = globalfxhub_finder_deposit_bands();
$finder_platforms   = globalfxhub_finder_platform_categories();
$finder_instruments = globalfxhub_finder_instrument_categories();
$finder_regulations = globalfxhub_finder_regulation_options();
$finder_styles      = globalfxhub_finder_trading_styles();
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BROKER FINDER</div>
  <h1><?php the_title(); ?></h1>
  <p>Answer six questions and we'll filter our full researched broker set down to the ones that actually match -- built from the same disclosed data and scores as every ranking on this site, not a separate commercial shortlist.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="finder">
    <div class="finder__step">
      <label for="finderCountry">1. Country</label>
      <select id="finderCountry">
        <option value="">Any / outside the EU</option>
        <?php foreach ( $finder_countries as $slug => $label ) : ?>
        <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="finder__step">
      <label for="finderDeposit">2. Minimum deposit you can meet</label>
      <select id="finderDeposit">
        <?php foreach ( $finder_deposits as $value => $label ) : ?>
        <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="finder__step">
      <label for="finderPlatform">3. Platform</label>
      <select id="finderPlatform">
        <option value="">Any platform</option>
        <?php foreach ( $finder_platforms as $key => $label ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="finder__step finder__step--wide">
      <label>4. Instruments you want to trade (any selected must all be offered)</label>
      <div class="finder__checks" id="finderInstruments">
        <?php foreach ( $finder_instruments as $key => $label ) : ?>
        <label class="finder__check"><input type="checkbox" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $label ); ?></label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="finder__step">
      <label for="finderRegulation">5. Regulation preference</label>
      <select id="finderRegulation">
        <?php foreach ( $finder_regulations as $value => $opt ) : ?>
        <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $opt['label'] ); ?></option>
        <?php endforeach; ?>
      </select>
      <p class="finder__hint" id="finderRegulationHint"></p>
    </div>

    <div class="finder__step">
      <label for="finderStyle">6. Trading style</label>
      <select id="finderStyle">
        <?php foreach ( $finder_styles as $key => $style ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $style['label'] ); ?></option>
        <?php endforeach; ?>
      </select>
      <p class="finder__hint" id="finderStyleHint"></p>
    </div>
  </div>

  <div class="finder__summary">
    <div class="finder__count">These <span id="finderResultCount">0</span> brokers meet your criteria</div>
    <p class="finder__countrylink" id="finderCountryLink"></p>
  </div>

  <div id="finderEmpty" class="finder__empty" style="display:none;">No brokers in our researched set currently meet all of these criteria -- try loosening one of your filters above.</div>

  <div class="rankings" id="finderResults"></div>

  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;max-width:74ch;">
    Country availability reflects only whether a broker holds a CySEC licence or EU/MiFID-passported regulation -- the same test already applied on every <a href="<?php echo esc_url( home_url( '/countries/' ) ); ?>" style="color:var(--teal);">country page</a>. It is not a guarantee that a given broker has completed the specific host-country notification, or that every product/account type is available there -- see the relevant country page for that nuance. "Trading style" re-weights our existing disclosed score categories (see hint above); it adds no new, unverified claim about any broker. Built from our <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">disclosed nine-category methodology</a>. Verify current terms directly with the broker and its regulator's public register before depositing funds. This is not personalized financial advice.
  </p>
</div>

<script>
const FINDER_BROKERS = <?php echo wp_json_encode( globalfxhub_finder_broker_payload() ); ?>;
const FINDER_REG_NOTES = <?php echo wp_json_encode( array_map( function( $o ) { return $o['note']; }, $finder_regulations ) ); ?>;
const FINDER_STYLE_NOTES = <?php echo wp_json_encode( array_map( function( $s ) { return $s['note']; }, $finder_styles ) ); ?>;
const FINDER_COUNTRY_LABELS = <?php echo wp_json_encode( $finder_countries ); ?>;
const FINDER_COUNTRIES_URL = <?php echo wp_json_encode( home_url( '/countries/' ) ); ?>;

(function(){
  const countrySel = document.getElementById('finderCountry');
  const depositSel = document.getElementById('finderDeposit');
  const platformSel = document.getElementById('finderPlatform');
  const instrumentChecks = document.querySelectorAll('#finderInstruments input[type="checkbox"]');
  const regSel = document.getElementById('finderRegulation');
  const styleSel = document.getElementById('finderStyle');

  const countEl = document.getElementById('finderResultCount');
  const emptyEl = document.getElementById('finderEmpty');
  const resultsEl = document.getElementById('finderResults');
  const regHint = document.getElementById('finderRegulationHint');
  const styleHint = document.getElementById('finderStyleHint');
  const countryLink = document.getElementById('finderCountryLink');

  function render(results, styleKey) {
    countEl.textContent = results.length;
    if (!results.length) {
      emptyEl.style.display = 'block';
      resultsEl.style.display = 'none';
      resultsEl.innerHTML = '';
      return;
    }
    emptyEl.style.display = 'none';
    resultsEl.style.display = '';

    let html = '<div class="rank-row head"><span></span><span>Broker</span><span class="col-fees">Min. deposit</span><span class="col-plat">Platforms</span><span>Match score</span><span></span></div>';
    results.forEach((b, i) => {
      const score = b.style_scores[styleKey];
      html += '<div class="rank-row">'
        + '<span class="rank-num">' + String(i + 1).padStart(2, '0') + '</span>'
        + '<span class="rank-broker"><span class="rank-broker__name">' + b.name + '</span><span class="rank-broker__tag">' + b.regulation_label + '</span></span>'
        + '<span class="rank-detail col-fees">' + (b.min_deposit_display || 'Not confirmed') + '</span>'
        + '<span class="rank-detail col-plat">' + (b.platforms.length ? b.platforms.join(', ') : 'Not confirmed') + '</span>'
        + '<span class="rank-score">' + (score !== null ? score.toFixed(2) : 'N/A') + ' / 5</span>'
        + '<span class="rank-ctas">'
        + '<a href="' + b.review_url + '" class="rank-cta">Read review</a>'
        + '<a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>'
        + '</span>'
        + '</div>';
    });
    resultsEl.innerHTML = html;
  }

  function applyFilters() {
    const country = countrySel.value;
    const deposit = depositSel.value;
    const platform = platformSel.value;
    const instruments = Array.from(instrumentChecks).filter(c => c.checked).map(c => c.value);
    const regulation = regSel.value;
    const style = styleSel.value;

    regHint.textContent = FINDER_REG_NOTES[regulation] || '';
    styleHint.textContent = FINDER_STYLE_NOTES[style] || '';
    countryLink.innerHTML = country
      ? 'See full local detail for ' + FINDER_COUNTRY_LABELS[country] + ': <a href="' + FINDER_COUNTRIES_URL + country + '/" style="color:var(--teal);">country regulation, tax & deposit guide &rarr;</a>'
      : '';

    const results = FINDER_BROKERS.filter(function(b) {
      if (country && !b.eu_eligible) return false;
      if (deposit) {
        const cap = parseInt(deposit, 10);
        if (b.min_deposit_usd === null || b.min_deposit_usd > cap) return false;
      }
      if (platform && !b.platform_tags.includes(platform)) return false;
      if (instruments.length && !instruments.every(function(tag) { return b.instrument_tags.includes(tag); })) return false;
      if (regulation === 'eu' && !b.eu_eligible) return false;
      if (regulation === 'tier1' && !b.meets_tier1) return false;
      return true;
    });

    results.sort(function(a, b) {
      const sa = a.style_scores[style] ?? 0;
      const sb = b.style_scores[style] ?? 0;
      if (sb !== sa) return sb - sa;
      return a.rank - b.rank;
    });

    render(results, style);
  }

  [countrySel, depositSel, platformSel, regSel, styleSel].forEach(function(el) {
    el.addEventListener('change', applyFilters);
  });
  instrumentChecks.forEach(function(el) {
    el.addEventListener('change', applyFilters);
  });

  applyFilters();
})();
</script>

<?php get_footer(); ?>
