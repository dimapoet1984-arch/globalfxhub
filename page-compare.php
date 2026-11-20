<?php
/**
 * Template Name: Compare Brokers
 * Description: Interactive side-by-side broker comparison tool at
 * /compare/, plus static, indexable pages at /compare/{a}-vs-{b}/ for
 * a bounded set of pairs (both brokers in the top 40 by rank) -- see
 * inc/broker-vs.php for the eligibility rule and server-rendered row
 * data this template reuses below.
 */
get_header();
$a_param = isset($_GET['a']) ? sanitize_title($_GET['a']) : '';
$b_param = isset($_GET['b']) ? sanitize_title($_GET['b']) : '';

$vs_a_slug = get_query_var( 'vs_a' );
$vs_b_slug = get_query_var( 'vs_b' );
$vs_requested = (bool) ( $vs_a_slug && $vs_b_slug );
$vs_pair = $vs_requested ? globalfxhub_vs_pair( $vs_a_slug, $vs_b_slug ) : null;
if ( $vs_requested && ! $vs_pair ) {
    status_header( 404 );
}
if ( $vs_pair ) {
    $a_param = $vs_a_slug;
    $b_param = $vs_b_slug;
}
?>

<?php if ( $vs_requested && ! $vs_pair ) : ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'cmp_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'cmp_not_available_h1' ); ?></h1>
  <p><?php globalfxhub_te( 'cmp_not_available_body' ); ?> <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'compare' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'cmp_use_full_tool_link' ); ?></a> <?php globalfxhub_te( 'cmp_use_full_tool_suffix' ); ?></p>
</div>

<?php else : ?>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'cmp_eyebrow' ); ?></div>
  <?php if ( $vs_pair ) : ?>
  <h1><?php echo esc_html( $vs_pair['a']['name'] . ' vs ' . $vs_pair['b']['name'] ); ?></h1>
  <p><?php globalfxhub_te( 'cmp_pair_intro' ); ?></p>
  <?php else : ?>
  <h1><?php globalfxhub_te( 'ptitle_compare' ); ?></h1>
  <p><?php globalfxhub_te( 'cmp_tool_intro' ); ?></p>
  <?php endif; ?>
  <?php $tool_learn_link = globalfxhub_resolve_learn_link( globalfxhub_tool_learn_links()['compare'] ); if ( $tool_learn_link ) : ?>
  <p style="font-size:13.5px;margin-top:8px;"><a href="<?php echo esc_url( $tool_learn_link['url'] ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'cmp_new_to_this' ); ?> <?php echo esc_html( $tool_learn_link['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<?php
$vs_editorial = null;
if ( $vs_pair ) {
    $vs_editorial = globalfxhub_get_localized_editorial_comparison( $vs_pair['a']['slug'] . '-' . $vs_pair['b']['slug'] );
}
if ( $vs_editorial ) :
?>
<div class="wrap art-body" style="padding-bottom:20px;">
  <p><?php echo esc_html( $vs_editorial['intro'] ); ?></p>
  <p><strong><?php globalfxhub_te( 'cmp_the_verdict' ); ?></strong> <?php echo esc_html( $vs_editorial['verdict'] ); ?></p>
  <div class="guides__grid" style="margin:20px 0;">
    <div class="guide" style="cursor:default;">
      <h3><?php echo esc_html( $vs_editorial['choose_a_heading'] ); ?></h3>
      <ul>
        <?php foreach ( $vs_editorial['choose_a_points'] as $point ) : ?>
        <li><?php echo esc_html( $point ); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="guide" style="cursor:default;">
      <h3><?php echo esc_html( $vs_editorial['choose_b_heading'] ); ?></h3>
      <ul>
        <?php foreach ( $vs_editorial['choose_b_points'] as $point ) : ?>
        <li><?php echo esc_html( $point ); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
  <?php if ( ! empty( $vs_editorial['worth_knowing'] ) ) : ?>
  <div class="methodology-note" style="margin-bottom:0;">
    <strong><?php echo esc_html( $vs_editorial['worth_knowing_heading'] ); ?></strong> <?php echo esc_html( $vs_editorial['worth_knowing'] ); ?>
  </div>
  <?php endif; ?>
  <?php
  $vs_research_links = array_merge(
      globalfxhub_broker_research_articles_for_broker( $vs_pair['a']['slug'] ),
      globalfxhub_broker_research_articles_for_broker( $vs_pair['b']['slug'] )
  );
  if ( ! empty( $vs_research_links ) ) :
  ?>
  <p style="margin:14px 0 0;font-size:13.5px;"><?php globalfxhub_te( 'cmp_related_research' ); ?>
    <?php foreach ( $vs_research_links as $i => $research_link ) : ?>
    <a href="<?php echo esc_url( $research_link['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $research_link['title'] ); ?></a><?php echo ( $i < count( $vs_research_links ) - 1 ) ? ' &middot; ' : ''; ?>
    <?php endforeach; ?>
  </p>
  <?php endif; ?>
</div>
<?php endif; ?>

<div class="wrap">
  <div class="picker">
    <div>
      <label for="brokerA"><?php globalfxhub_te( 'cmp_broker_a_label' ); ?></label>
      <select id="brokerA"></select>
    </div>
    <div class="vs"><?php globalfxhub_te( 'cmp_vs_label' ); ?></div>
    <div>
      <label for="brokerB"><?php globalfxhub_te( 'cmp_broker_b_label' ); ?></label>
      <select id="brokerB"></select>
    </div>
    <button id="compareBtn" type="button"><?php globalfxhub_te( 'nav_compare' ); ?></button>
  </div>

  <div class="presets" id="presets">
    <?php
    // Server-rendered as real <a href> links (not just a JS click
    // handler that re-renders the picker client-side) so these are
    // actual crawlable inbound links to their /compare/{a}-vs-{b}/
    // pages, not only reachable via a button click no crawler follows.
    $vs_preset_pairs = array(
        array( 'pepperstone', 'ic-markets' ), array( 'ig', 'xtb' ), array( 'etoro', 'plus500' ), array( 'xm', 'pepperstone' ),
        array( 'ic-markets', 'fxpro' ), array( 'fxcm', 'capital-com' ), array( 'avatrade', 'tickmill' ), array( 'exness', 'xm' ),
        array( 'avatrade', 'pepperstone' ), array( 'fp-markets', 'ic-markets' ), array( 'oanda', 'ig' ),
        array( 'hf-markets', 'xm' ), array( 'plus500', 'cmc-markets' ),
    );
    $vs_ranked_slugs = globalfxhub_top_ranked_broker_slugs( 40 );
    foreach ( $vs_preset_pairs as list( $preset_a, $preset_b ) ) {
        $preset_pair = globalfxhub_vs_pair( $preset_a, $preset_b );
        if ( ! $preset_pair ) {
            continue;
        }
        $pos_a = array_search( $preset_a, $vs_ranked_slugs, true );
        $pos_b = array_search( $preset_b, $vs_ranked_slugs, true );
        list( $url_first, $url_second ) = ( $pos_a < $pos_b ) ? array( $preset_a, $preset_b ) : array( $preset_b, $preset_a );
        ?>
        <a class="preset" href="<?php echo esc_url( globalfxhub_track_b_url( 'compare/' . $url_first . '-vs-' . $url_second . '/' ) ); ?>"><?php echo esc_html( $preset_pair['a']['name'] . ' vs ' . $preset_pair['b']['name'] ); ?></a>
        <?php
    }
    ?>
  </div>

  <div id="empty-state"<?php echo $vs_pair ? ' style="display:none;"' : ''; ?>><?php globalfxhub_te( 'cmp_empty_state_default' ); ?></div>

  <div id="results"<?php echo $vs_pair ? ' class="show"' : ''; ?>>
    <table class="compare-table" id="resultsTable">
      <?php if ( $vs_pair ) :
        $vs_a = $vs_pair['a'];
        $vs_b = $vs_pair['b'];
      ?>
      <thead><tr><th class="label-col"></th><th><?php echo esc_html( $vs_a['name'] ); ?></th><th><?php echo esc_html( $vs_b['name'] ); ?></th></tr></thead>
      <tbody>
        <?php foreach ( globalfxhub_vs_comparison_rows( $vs_a, $vs_b ) as $row ) : ?>
        <tr>
          <th><?php echo esc_html( $row['label'] ); ?></th>
          <td><?php echo esc_html( $row['a'] ); ?><?php if ( ! empty( $row['note_a'] ) ) : ?><div class="compare-note"><?php echo esc_html( $row['note_a'] ); ?></div><?php endif; ?></td>
          <td><?php echo esc_html( $row['b'] ); ?><?php if ( ! empty( $row['note_b'] ) ) : ?><div class="compare-note"><?php echo esc_html( $row['note_b'] ); ?></div><?php endif; ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
          <th><?php globalfxhub_te( 'cmp_row_visit' ); ?></th>
          <td><a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;"><?php globalfxhub_te( 'cmp_row_visit' ); ?> <?php echo esc_html( $vs_a['name'] ); ?></a></td>
          <td><a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;"><?php globalfxhub_te( 'cmp_row_visit' ); ?> <?php echo esc_html( $vs_b['name'] ); ?></a></td>
        </tr>
      </tbody>
      <?php endif; ?>
    </table>
    <?php if ( $vs_pair ) : ?>
    <p style="font-size:13.5px;margin:16px 0;">
      <a href="<?php echo esc_url( globalfxhub_track_b_url( 'reviews/' . $vs_a['slug'] . '/' ) ); ?>" style="color:var(--teal);"><?php echo esc_html( sprintf( globalfxhub_t( 'cmp_full_review_link' ), $vs_a['name'] ) ); ?></a>
      &middot;
      <a href="<?php echo esc_url( globalfxhub_track_b_url( 'reviews/' . $vs_b['slug'] . '/' ) ); ?>" style="color:var(--teal);"><?php echo esc_html( sprintf( globalfxhub_t( 'cmp_full_review_link' ), $vs_b['name'] ) ); ?></a>
      &middot;
      <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'compare' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'cmp_different_brokers_link' ); ?></a>
    </p>
    <?php
    $vs_related = array();
    foreach ( array( $vs_a, $vs_b ) as $vs_side ) {
        $vs_other_side = ( $vs_side === $vs_a ) ? $vs_b : $vs_a;
        foreach ( globalfxhub_broker_vs_related_pairs( $vs_side['slug'], $vs_other_side['slug'], 2 ) as $rel ) {
            if ( ! isset( $vs_related[ $rel['url'] ] ) ) {
                $vs_related[ $rel['url'] ] = $vs_side['name'] . ' vs ' . $rel['broker']['name'];
            }
        }
    }
    if ( ! empty( $vs_related ) ) :
    ?>
    <p style="font-size:13.5px;margin:0 0 16px;color:var(--ink-soft);"><?php globalfxhub_te( 'cmp_also_compare_label' ); ?>
      <?php $vs_related_links = array(); foreach ( $vs_related as $rel_url => $rel_label ) {
          $vs_related_links[] = '<a href="' . esc_url( $rel_url ) . '" style="color:var(--teal);">' . esc_html( $rel_label ) . '</a>';
      }
      echo implode( ' &middot; ', $vs_related_links ); ?>
    </p>
    <?php endif; ?>
    <?php endif; ?>
    <?php if ( $vs_editorial && ! empty( $vs_editorial['faqs'] ) ) : ?>
    <div style="margin:0 0 28px;">
      <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 12px;"><?php globalfxhub_te( 'rev_faq_h2' ); ?></h2>
      <?php foreach ( $vs_editorial['faqs'] as $faq ) : ?>
      <div style="margin-bottom:18px;">
        <p style="font-weight:600;margin:0 0 4px;color:var(--navy);"><?php echo esc_html( $faq['q'] ); ?></p>
        <p style="margin:0;color:var(--ink-soft);line-height:1.6;"><?php echo esc_html( $faq['a'] ); ?></p>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <div class="methodology-note">
      <strong><?php globalfxhub_te( 'tb_methodology_how_calculated' ); ?></strong> <?php globalfxhub_te( 'tb_methodology_note_body' ); ?> <?php globalfxhub_te( 'tb_methodology_see_the' ); ?> <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'tb_methodology_full_link' ); ?></a>. <?php globalfxhub_te( 'tb_methodology_verify' ); ?>
    </div>
  </div>
</div>
<?php endif; ?>

<script>
<?php
// Only 'instruments' is shown by the JS table below (T.instruments) --
// localized here, server-side, before the dump, same as
// globalfxhub_vs_comparison_rows() does for the static /compare/{a}-vs-{b}/
// pages. Every other field BROKERS carries (scores, platforms, etc.)
// is either numeric/language-neutral already or unused by this script.
$brokers_for_js = array_map( function( $b ) {
    if ( ! empty( $b['instruments'] ) ) {
        $b['instruments'] = globalfxhub_tf( 'broker', $b['slug'], 'instruments', $b['instruments'] );
    }
    return $b;
}, globalfxhub_get_brokers() );
?>
const BROKERS = <?php echo wp_json_encode( $brokers_for_js ); ?>;
const PRESET_A = <?php echo wp_json_encode( $a_param ); ?>;
const PRESET_B = <?php echo wp_json_encode( $b_param ); ?>;
// Translated strings for the JS-rendered results table below, which
// duplicates inc/broker-vs.php's row-label logic client-side (that
// PHP path can't be reused directly here since this is the interactive
// /compare/ tool with no server-known pair until the visitor picks one).
const T = <?php echo wp_json_encode( array(
    'choose_different' => globalfxhub_t( 'cmp_js_choose_different' ),
    'choose_two' => globalfxhub_t( 'cmp_js_choose_two' ),
    'na' => globalfxhub_t( 'tb_not_independently_confirmed' ),
    'none_confirmed' => globalfxhub_t( 'tb_none_confirmed' ),
    'not_enough_data' => globalfxhub_t( 'tb_not_enough_data_score' ),
    'eu_mifid' => globalfxhub_t( 'cmp_reg_eu_mifid' ),
    'fca_unconfirmed' => globalfxhub_t( 'cmp_reg_fca_unconfirmed' ),
    'seychelles_unconfirmed' => globalfxhub_t( 'cmp_reg_seychelles_unconfirmed' ),
    'reg_not_confirmed' => globalfxhub_t( 'cmp_reg_not_confirmed' ),
    'cysec_no' => globalfxhub_t( 'rev_reglabel_cysec_no' ),
    'fca_no' => globalfxhub_t( 'rev_reglabel_fca_no' ),
    'seychelles_no' => globalfxhub_t( 'rev_reglabel_seychelles_no' ),
    'overall_score' => globalfxhub_t( 'cmp_row_overall_score' ),
    'regulation' => globalfxhub_t( 'nav_regulation' ),
    'entity' => globalfxhub_t( 'rev_regulated_entity' ),
    'founded' => globalfxhub_t( 'rev_founded' ),
    'headquarters' => globalfxhub_t( 'rev_headquarters' ),
    'min_deposit' => globalfxhub_t( 'rev_minimum_deposit' ),
    'avg_spread' => globalfxhub_t( 'rev_avg_spread_eurusd' ),
    'platforms' => globalfxhub_t( 'table_platforms' ),
    'other_tier1' => globalfxhub_t( 'rev_other_tier1' ),
    'instruments' => globalfxhub_t( 'rev_instruments' ),
    'score_breakdown' => globalfxhub_t( 'tb_score_breakdown' ),
    'visit' => globalfxhub_t( 'cmp_row_visit' ),
    'sub_regulation' => globalfxhub_t( 'method1_h3' ),
    'sub_cost' => globalfxhub_t( 'method2_h3' ),
    'sub_fees' => globalfxhub_t( 'method3_h3' ),
    'sub_platforms' => globalfxhub_t( 'method4_h3' ),
    'sub_execution' => globalfxhub_t( 'method5_h3' ),
    'sub_product_range' => globalfxhub_t( 'method6_h3' ),
    'sub_deposits' => globalfxhub_t( 'method7_h3' ),
    'sub_transparency' => globalfxhub_t( 'method8_h3' ),
    'sub_track_record' => globalfxhub_t( 'method9_h3' ),
) ); ?>;

(function(){
  const selA = document.getElementById('brokerA');
  const selB = document.getElementById('brokerB');
  const btn = document.getElementById('compareBtn');
  const results = document.getElementById('results');
  const empty = document.getElementById('empty-state');
  const table = document.getElementById('resultsTable');
  const presetsEl = document.getElementById('presets');
  // The "comparison not available" branch (an out-of-bound or
  // nonexistent /compare/{a}-vs-{b}/ pair) renders no picker UI at all.
  if (!selA || !selB || !btn || !results || !empty || !table || !presetsEl) return;

  const bySlug = {};
  BROKERS.forEach(b => bySlug[b.slug] = b);

  const sorted = [...BROKERS].sort((a,b) => a.name.localeCompare(b.name));
  sorted.forEach(b => {
    selA.add(new Option(b.name, b.slug));
    selB.add(new Option(b.name, b.slug));
  });

  // The six "popular comparisons" are now server-rendered as real
  // <a href> links directly to their /compare/{a}-vs-{b}/ page (see
  // the PHP block above #presets) rather than built here as
  // JS-only buttons -- that gave them a crawlable inbound link
  // instead of only a client-side re-render no crawler follows.

  function render(){
    const a = bySlug[selA.value];
    const b = bySlug[selB.value];
    if(!a || !b || a.slug === b.slug){
      empty.textContent = a && b && a.slug === b.slug
        ? T.choose_different
        : T.choose_two;
      empty.style.display = 'block';
      results.classList.remove('show');
      return;
    }
    empty.style.display = 'none';
    results.classList.add('show');

    const NA = T.na;
    const orNA = (v, suffix) => (v === null || v === undefined || v === '') ? NA : (v + (suffix || ''));

    const regulationLabel = (x) => {
      const parts = [];
      if (x.cysec && x.cysec !== '—') parts.push(T.cysec_no.replace('%s', x.cysec));
      else if (x.cysec === '—') parts.push(T.eu_mifid);
      if (x.fca) parts.push(T.fca_no.replace('%s', x.fca));
      else if (x.fca_note && x.fca === null) parts.push(T.fca_unconfirmed);
      if (x.seychelles) parts.push(T.seychelles_no.replace('%s', x.seychelles));
      else if (x.seychelles_note && x.seychelles === null) parts.push(T.seychelles_unconfirmed);
      return parts.length ? parts.join(' + ') : T.reg_not_confirmed;
    };

    const rows = [];
    rows.push([T.overall_score,
      `<span class="score-num">${a.scores.overall}</span> / 5`,
      `<span class="score-num">${b.scores.overall}</span> / 5`,
      a.scores.overall, b.scores.overall]);
    const regNote = (x) => [x.cysec_note, x.fca_note, x.seychelles_note].filter(Boolean).join(' ');
    rows.push([T.regulation,
      regulationLabel(a) + (regNote(a) ? `<div class="compare-note">${regNote(a)}</div>` : ''),
      regulationLabel(b) + (regNote(b) ? `<div class="compare-note">${regNote(b)}</div>` : '')]);
    rows.push([T.entity, a.entity, b.entity]);
    rows.push([T.founded, orNA(a.founded), orNA(b.founded), a.founded ? (2026-a.founded) : undefined, b.founded ? (2026-b.founded) : undefined]);
    rows.push([T.headquarters, orNA(a.hq), orNA(b.hq)]);
    rows.push([T.min_deposit, orNA(a.min_deposit_display), orNA(b.min_deposit_display),
      (typeof a.min_deposit_usd === 'number') ? -a.min_deposit_usd : undefined,
      (typeof b.min_deposit_usd === 'number') ? -b.min_deposit_usd : undefined]);
    rows.push([T.avg_spread, orNA(a.spread_eurusd, ' pips'), orNA(b.spread_eurusd, ' pips'),
      (typeof a.spread_eurusd === 'number') ? -a.spread_eurusd : undefined,
      (typeof b.spread_eurusd === 'number') ? -b.spread_eurusd : undefined]);
    rows.push([T.platforms, a.platforms.length ? a.platforms.join(', ') : NA, b.platforms.length ? b.platforms.join(', ') : NA, a.platforms.length, b.platforms.length]);
    rows.push([T.other_tier1, a.other_reg.length ? a.other_reg.join(', ') : T.none_confirmed, b.other_reg.length ? b.other_reg.join(', ') : T.none_confirmed, a.other_reg_count, b.other_reg_count]);
    rows.push([T.instruments, orNA(a.instruments), orNA(b.instruments)]);

    let html = `<thead><tr><th class="label-col"></th><th>${a.name}</th><th>${b.name}</th></tr></thead><tbody>`;
    rows.forEach(([label, valA, valB, numA, numB]) => {
      let clsA = '', clsB = '';
      if(numA != null && numB != null && numA !== numB){
        if(numA > numB) clsA = ' class="better"'; else clsB = ' class="better"';
      }
      html += `<tr><th>${label}</th><td${clsA}>${valA}</td><td${clsB}>${valB}</td></tr>`;
    });

    const subLabels = [
      ['regulation', T.sub_regulation],
      ['cost', T.sub_cost],
      ['non_trading_fees', T.sub_fees],
      ['platforms', T.sub_platforms],
      ['execution', T.sub_execution],
      ['product_range', T.sub_product_range],
      ['deposits_withdrawals', T.sub_deposits],
      ['transparency', T.sub_transparency],
      ['track_record', T.sub_track_record],
    ];
    const bar = (scores, key, label) => {
      if(scores[key] === null || scores[key] === undefined){
        return `<div class="subscore-row">${label}<span class="review-scorebox__na">${T.not_enough_data}</span></div>`;
      }
      return `<div class="subscore-row">${label}<div class="bar-track"><div class="bar-fill" style="width:${scores[key]/5*100}%;"></div></div></div>`;
    };
    let barsA = '', barsB = '';
    subLabels.forEach(([key, label]) => {
      barsA += bar(a.scores, key, label);
      barsB += bar(b.scores, key, label);
    });
    html += `<tr><th>${T.score_breakdown}</th><td>${barsA}</td><td>${barsB}</td></tr>`;
    html += `<tr><th>${T.visit}</th>
      <td><a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">${T.visit} ${a.name}</a></td>
      <td><a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">${T.visit} ${b.name}</a></td>
    </tr>`;
    html += '</tbody>';
    table.innerHTML = html;

    const params = new URLSearchParams(window.location.search);
    params.set('a', a.slug); params.set('b', b.slug);
    history.replaceState(null, '', '?' + params.toString());
  }

  btn.addEventListener('click', render);

  let pa = PRESET_A, pb = PRESET_B;
  if(!pa){
    const params = new URLSearchParams(window.location.search);
    pa = params.get('a') || ''; pb = params.get('b') || '';
  }
  if(pa && bySlug[pa]) selA.value = pa;
  if(pb && bySlug[pb]) selB.value = pb;
  if(pa && bySlug[pa] && !pb){
    const other = sorted.find(x => x.slug !== pa);
    if(other) selB.value = other.slug;
  }
  if(pa && bySlug[pa]) render();
})();
</script>

<?php get_footer(); ?>
