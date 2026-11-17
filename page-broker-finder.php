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
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php the_title(); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'finder_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'ptitle_broker_finder' ); ?></h1>
  <p><?php globalfxhub_te( 'finder_intro' ); ?></p>
  <?php $tool_learn_link = globalfxhub_resolve_learn_link( globalfxhub_tool_learn_links()['broker-finder'] ); if ( $tool_learn_link ) : ?>
  <p style="font-size:13.5px;margin-top:8px;"><a href="<?php echo esc_url( $tool_learn_link['url'] ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'cmp_new_to_this' ); ?> <?php echo esc_html( $tool_learn_link['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="finder">
    <div class="finder__step">
      <label for="finderCountry"><?php globalfxhub_te( 'finder_label_country' ); ?></label>
      <select id="finderCountry">
        <option value=""><?php globalfxhub_te( 'finder_opt_any_country' ); ?></option>
        <?php foreach ( $finder_countries as $slug => $label ) : ?>
        <option value="<?php echo esc_attr( $slug ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="finder__step">
      <label for="finderDeposit"><?php globalfxhub_te( 'finder_label_deposit' ); ?></label>
      <select id="finderDeposit">
        <?php foreach ( $finder_deposits as $value => $label ) : ?>
        <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="finder__step">
      <label for="finderPlatform"><?php globalfxhub_te( 'finder_label_platform' ); ?></label>
      <select id="finderPlatform">
        <option value=""><?php globalfxhub_te( 'finder_opt_any_platform' ); ?></option>
        <?php foreach ( $finder_platforms as $key => $label ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="finder__step finder__step--wide">
      <label><?php globalfxhub_te( 'finder_label_instruments' ); ?></label>
      <div class="finder__checks" id="finderInstruments">
        <?php foreach ( $finder_instruments as $key => $label ) : ?>
        <label class="finder__check"><input type="checkbox" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $label ); ?></label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="finder__step">
      <label for="finderRegulation"><?php globalfxhub_te( 'finder_label_regulation' ); ?></label>
      <select id="finderRegulation">
        <?php foreach ( $finder_regulations as $value => $opt ) : ?>
        <option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $opt['label'] ); ?></option>
        <?php endforeach; ?>
      </select>
      <p class="finder__hint" id="finderRegulationHint"></p>
    </div>

    <div class="finder__step">
      <label for="finderStyle"><?php globalfxhub_te( 'finder_label_style' ); ?></label>
      <select id="finderStyle">
        <?php foreach ( $finder_styles as $key => $style ) : ?>
        <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $style['label'] ); ?></option>
        <?php endforeach; ?>
      </select>
      <p class="finder__hint" id="finderStyleHint"></p>
    </div>
  </div>

  <div id="finderCountryWarning" class="methodology-note" style="display:none;margin:0 0 24px;"></div>

  <div class="finder__summary">
    <div class="finder__count"><?php globalfxhub_te( 'finder_results_prefix' ); ?> <span id="finderResultCount">0</span> <?php globalfxhub_te( 'finder_results_suffix' ); ?></div>
    <p class="finder__countrylink" id="finderCountryLink"></p>
  </div>

  <div id="finderEmpty" class="finder__empty" style="display:none;"><?php globalfxhub_te( 'finder_empty_state' ); ?></div>

  <div class="rankings" id="finderResults"></div>

  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;">
    <?php
    $finder_country_link_html = '<a href="' . esc_url( home_url( '/countries/' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'finder_country_page_link' ) ) . '</a>';
    echo sprintf( esc_html( globalfxhub_t( 'finder_disclaimer_p1' ) ), $finder_country_link_html );
    ?>
    <?php globalfxhub_te( 'finder_disclaimer_p2' ); ?> <?php globalfxhub_te( 'finder_disclaimer_p3' ); ?>
    <?php
    $finder_methodology_link_html = '<a href="' . esc_url( home_url( '/#method' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'best_methodology_link' ) ) . '</a>';
    echo sprintf( esc_html( globalfxhub_t( 'finder_built_from_methodology' ) ), $finder_methodology_link_html );
    ?>
    <?php globalfxhub_te( 'tb_methodology_verify' ); ?>
  </p>
</div>

<script>
const FINDER_BROKERS = <?php echo wp_json_encode( globalfxhub_finder_broker_payload() ); ?>;
const FINDER_REG_NOTES = <?php echo wp_json_encode( array_map( function( $o ) { return $o['note']; }, $finder_regulations ) ); ?>;
const FINDER_STYLE_NOTES = <?php echo wp_json_encode( array_map( function( $s ) { return $s['note']; }, $finder_styles ) ); ?>;
const FINDER_COUNTRY_LABELS = <?php echo wp_json_encode( $finder_countries ); ?>;
const FINDER_COUNTRIES_URL = <?php echo wp_json_encode( home_url( '/countries/' ) ); ?>;
// Translated strings for the two JS-built messages below (the
// per-selection "see local detail" link and the country-eligibility
// warning), which interpolate a country name that's only known once
// the visitor picks one, so these can't be rendered server-side.
const T = <?php echo wp_json_encode( array(
    'see_local_detail' => globalfxhub_t( 'finder_js_see_local_detail' ),
    'country_guide_link' => globalfxhub_t( 'finder_js_country_guide_link' ),
    'country_warning_strong' => globalfxhub_t( 'finder_js_country_warning_strong' ),
    'country_warning_body' => globalfxhub_t( 'finder_js_country_warning_body' ),
) ); ?>;

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
  const countryWarning = document.getElementById('finderCountryWarning');

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

    const notConfirmedLabel = <?php echo wp_json_encode( globalfxhub_t( 'tb_not_confirmed' ) ); ?>;
    const naLabel = <?php echo wp_json_encode( globalfxhub_t( 'rev_na_abbr' ) ); ?>;
    let html = '<div class="rank-row head"><span></span><span>' + <?php echo wp_json_encode( globalfxhub_t( 'table_broker' ) ); ?> + '</span><span class="col-fees">' + <?php echo wp_json_encode( globalfxhub_t( 'rev_minimum_deposit' ) ); ?> + '</span><span class="col-plat">' + <?php echo wp_json_encode( globalfxhub_t( 'table_platforms' ) ); ?> + '</span><span>' + <?php echo wp_json_encode( globalfxhub_t( 'finder_match_score_label' ) ); ?> + '</span><span></span></div>';
    results.forEach((b, i) => {
      const score = b.style_scores[styleKey];
      html += '<div class="rank-row">'
        + '<span class="rank-num">' + String(i + 1).padStart(2, '0') + '</span>'
        + '<span class="rank-broker"><span class="rank-broker__name">' + b.name + '</span><span class="rank-broker__tag">' + b.regulation_label + '</span></span>'
        + '<span class="rank-detail col-fees">' + (b.min_deposit_display || notConfirmedLabel) + '</span>'
        + '<span class="rank-detail col-plat">' + (b.platforms.length ? b.platforms.join(', ') : notConfirmedLabel) + '</span>'
        + '<span class="rank-score">' + (score !== null ? score.toFixed(2) : naLabel) + ' / 5</span>'
        + '<span class="rank-ctas">'
        + '<a href="' + b.review_url + '" class="rank-cta">' + <?php echo wp_json_encode( globalfxhub_t( 'btn_read_review' ) ); ?> + '</a>'
        + '<a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">' + <?php echo wp_json_encode( globalfxhub_t( 'tb_visit_broker' ) ); ?> + '</a>'
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
      ? T.see_local_detail.replace('%s', FINDER_COUNTRY_LABELS[country]) + ' <a href="' + FINDER_COUNTRIES_URL + country + '/" style="color:var(--teal);">' + T.country_guide_link + '</a>'
      : '';

    if (country) {
      countryWarning.style.display = 'block';
      countryWarning.innerHTML = '<strong>' + T.country_warning_strong + '</strong> ' + T.country_warning_body.replace('%s', FINDER_COUNTRY_LABELS[country]);
    } else {
      countryWarning.style.display = 'none';
    }

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
