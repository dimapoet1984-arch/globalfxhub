<?php
/**
 * Template Name: Broker Regulation Checker
 * Description: Search any researched broker by name for its full
 * regulation dossier -- legal entity, FCA/CySEC numbers, named Tier-1
 * regulators, offshore entities, which regulator covers which
 * geography, direct register links, and when the regulation data was
 * last reviewed. See inc/regulation-checker.php for exactly what each
 * field traces back to.
 */
get_header();

$rc_payload = globalfxhub_regulation_checker_payload();
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php the_title(); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'rc_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'ptitle_regulation_checker' ); ?></h1>
  <p><?php globalfxhub_te( 'rc_intro' ); ?></p>
  <?php $tool_learn_link = globalfxhub_resolve_learn_link( globalfxhub_tool_learn_links()['regulation-checker'] ); if ( $tool_learn_link ) : ?>
  <p style="font-size:13.5px;margin-top:8px;"><a href="<?php echo esc_url( $tool_learn_link['url'] ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'cmp_new_to_this' ); ?> <?php echo esc_html( $tool_learn_link['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="picker" style="grid-template-columns:1fr;">
    <div>
      <label for="rcSearch"><?php globalfxhub_te( 'rc_label_search' ); ?></label>
      <input type="text" id="rcSearch" list="rcBrokerList" placeholder="<?php echo esc_attr( globalfxhub_t( 'rc_search_placeholder' ) ); ?>" autocomplete="off">
      <datalist id="rcBrokerList">
        <?php foreach ( $rc_payload as $b ) : ?>
        <option value="<?php echo esc_attr( $b['name'] ); ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
  </div>

  <div id="rcEmpty" class="finder__empty"><?php globalfxhub_te( 'rc_empty_state' ); ?></div>

  <div id="rcDossier" style="display:none;">
    <div class="wrap" style="padding:0;margin:0 0 20px;">
      <h2 id="rcName" style="font-family:var(--font-display);font-weight:500;font-size:26px;margin:0 0 6px;"></h2>
      <p id="rcHq" style="color:var(--ink-soft);font-size:14px;margin:0 0 4px;"></p>
      <p id="rcLastVerified" style="color:var(--ink-soft);font-size:13px;margin:0;"></p>
    </div>

    <table class="compare-table" style="margin-bottom:24px;">
      <tbody>
        <tr><th><?php globalfxhub_te( 'rc_primary_entity_label' ); ?></th><td id="rcEntity"></td></tr>
      </tbody>
    </table>

    <table class="compare-table" id="rcRegTable">
      <thead>
        <tr><th><?php globalfxhub_te( 'rc_col_regulator' ); ?></th><th><?php globalfxhub_te( 'rc_col_license_status' ); ?></th><th><?php globalfxhub_te( 'rc_col_geography' ); ?></th><th><?php globalfxhub_te( 'rc_col_verify' ); ?></th></tr>
      </thead>
      <tbody id="rcRegRows"></tbody>
    </table>

    <p style="margin-top:8px;">
      <a id="rcReviewLink" href="#" style="color:var(--teal);"><?php globalfxhub_te( 'rc_read_full_review_link' ); ?></a>
    </p>
  </div>

  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:28px;">
    <?php globalfxhub_te( 'rc_disclaimer' ); ?>
  </p>
</div>

<script>
const RC_BROKERS = <?php echo wp_json_encode( $rc_payload ); ?>;
// Translated strings for the JS-rendered dossier below, which builds
// each regulator row (including the three always-present FCA/CySEC/
// Seychelles rows) client-side once a broker is picked.
const T = <?php echo wp_json_encode( array(
    'eu_mifid_no_number' => globalfxhub_t( 'rc_eu_mifid_no_number' ),
    'confirmed_no_number' => globalfxhub_t( 'rc_confirmed_no_number' ),
    'not_separately_itemized' => globalfxhub_t( 'rc_not_separately_itemized' ),
    'verify' => globalfxhub_t( 'rc_col_verify' ),
    'hq_prefix' => globalfxhub_t( 'rc_hq_prefix' ),
    'label_fca' => globalfxhub_t( 'rc_label_fca' ),
    'label_cysec' => globalfxhub_t( 'rc_label_cysec' ),
    'label_seychelles' => globalfxhub_t( 'rc_label_seychelles' ),
    'geo_united_kingdom' => globalfxhub_t( 'rc_geo_united_kingdom' ),
    'geo_cysec_note' => globalfxhub_t( 'rc_geo_cysec_note' ),
    'geo_seychelles_note' => globalfxhub_t( 'rc_geo_seychelles_note' ),
    'listed_among_confirmed' => globalfxhub_t( 'rc_listed_among_confirmed' ),
    'no_regulator_confirmed' => globalfxhub_t( 'rc_no_regulator_confirmed' ),
) ); ?>;

(function(){
  const searchEl = document.getElementById('rcSearch');
  const emptyEl = document.getElementById('rcEmpty');
  const dossierEl = document.getElementById('rcDossier');
  const nameEl = document.getElementById('rcName');
  const hqEl = document.getElementById('rcHq');
  const lastVerifiedEl = document.getElementById('rcLastVerified');
  const entityEl = document.getElementById('rcEntity');
  const regRowsEl = document.getElementById('rcRegRows');
  const reviewLinkEl = document.getElementById('rcReviewLink');

  const byName = {};
  RC_BROKERS.forEach(function(b) { byName[b.name.toLowerCase()] = b; });

  function findBroker(query) {
    const q = query.trim().toLowerCase();
    if (!q) return null;
    if (byName[q]) return byName[q];
    const match = RC_BROKERS.find(function(b) { return b.name.toLowerCase().includes(q); });
    return match || null;
  }

  function regRow(label, entry, extraNote) {
    if (!entry) return '';
    let status;
    if (entry.number) {
      status = 'No. ' + entry.number;
    } else if (entry.passported) {
      status = T.eu_mifid_no_number;
    } else if (entry.note) {
      status = entry.note;
    } else {
      status = T.confirmed_no_number;
    }
    const geo = extraNote || T.not_separately_itemized;
    const link = entry.register_url ? '<a href="' + entry.register_url + '" target="_blank" rel="nofollow noopener" style="color:var(--teal);">' + T.verify + '</a>' : '&mdash;';
    return '<tr><td><strong>' + label + '</strong></td><td>' + status + '</td><td>' + geo + '</td><td>' + link + '</td></tr>';
  }

  function render(b) {
    emptyEl.style.display = 'none';
    dossierEl.style.display = 'block';

    nameEl.textContent = b.name;
    hqEl.textContent = b.hq ? (T.hq_prefix + ' ' + b.hq) : '';
    lastVerifiedEl.textContent = <?php echo wp_json_encode( globalfxhub_t( 'rev_regulation_last_reviewed' ) ); ?> + ' ' + b.review_dates.regulation_reviewed + ' · ' + <?php echo wp_json_encode( globalfxhub_t( 'rev_next_scheduled_review' ) ); ?> + ' ' + b.review_dates.next_review;
    entityEl.textContent = b.entity || <?php echo wp_json_encode( globalfxhub_t( 'reg_checker_entity_not_confirmed' ) ); ?>;
    reviewLinkEl.href = b.review_url;

    let rows = '';
    rows += regRow(T.label_fca, b.fca, T.geo_united_kingdom);
    rows += regRow(T.label_cysec, b.cysec, T.geo_cysec_note);
    rows += regRow(T.label_seychelles, b.seychelles, T.geo_seychelles_note);
    b.other_regulators.forEach(function(r) {
      rows += regRow(r.code, { number: null, passported: false, note: r.note || T.listed_among_confirmed, register_url: r.register_url }, r.geography);
    });

    if (!rows) {
      rows = '<tr><td colspan="4" style="color:var(--ink-soft);">' + T.no_regulator_confirmed + '</td></tr>';
    }
    regRowsEl.innerHTML = rows;
  }

  function handleSearch() {
    const broker = findBroker(searchEl.value);
    if (broker) {
      render(broker);
    } else {
      emptyEl.style.display = 'block';
      dossierEl.style.display = 'none';
    }
  }

  searchEl.addEventListener('input', handleSearch);
  searchEl.addEventListener('change', handleSearch);

  // Deep link from a broker's review page (?broker=slug) -- matched on
  // the exact slug, not the fuzzy name search handleSearch() uses, so a
  // link always lands on the right broker even if its name is a
  // substring of another broker's.
  const deepLinkSlug = new URLSearchParams(window.location.search).get('broker');
  if (deepLinkSlug) {
    const linked = RC_BROKERS.find(function(b) { return b.slug === deepLinkSlug; });
    if (linked) {
      searchEl.value = linked.name;
      render(linked);
    }
  }
})();
</script>

<?php get_footer(); ?>
