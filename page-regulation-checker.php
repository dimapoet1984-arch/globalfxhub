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
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BROKER REGULATION CHECKER</div>
  <h1><?php the_title(); ?></h1>
  <p>Search any of our researched brokers for its full regulation dossier: legal entity on file, FCA/CySEC numbers, named Tier-1 regulators, offshore entities, which regulator covers which geography, direct register links, and when we last reviewed its regulation data.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="picker" style="grid-template-columns:1fr;">
    <div>
      <label for="rcSearch">Search a broker</label>
      <input type="text" id="rcSearch" list="rcBrokerList" placeholder="e.g. Pepperstone" autocomplete="off">
      <datalist id="rcBrokerList">
        <?php foreach ( $rc_payload as $b ) : ?>
        <option value="<?php echo esc_attr( $b['name'] ); ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
  </div>

  <div id="rcEmpty" class="finder__empty">Search a broker above (e.g. "Pepperstone") to see its regulation dossier.</div>

  <div id="rcDossier" style="display:none;">
    <div class="wrap" style="padding:0;margin:0 0 20px;">
      <h2 id="rcName" style="font-family:var(--font-display);font-weight:500;font-size:26px;margin:0 0 6px;"></h2>
      <p id="rcHq" style="color:var(--ink-soft);font-size:14px;margin:0 0 4px;"></p>
      <p id="rcLastVerified" style="color:var(--ink-soft);font-size:13px;margin:0;"></p>
    </div>

    <table class="compare-table" style="margin-bottom:24px;">
      <tbody>
        <tr><th>Primary regulated entity on file</th><td id="rcEntity"></td></tr>
      </tbody>
    </table>

    <table class="compare-table" id="rcRegTable">
      <thead>
        <tr><th>Regulator</th><th>License / status</th><th>Geography covered</th><th>Verify</th></tr>
      </thead>
      <tbody id="rcRegRows"></tbody>
    </table>

    <p style="margin-top:8px;">
      <a id="rcReviewLink" href="#" style="color:var(--teal);">Read the full broker review &rarr;</a>
    </p>
  </div>

  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:28px;max-width:78ch;">
    Every figure here traces back to the same researched data behind this broker's review page and our rankings -- nothing shown here is scored or computed differently for this tool. Every regulator code currently in our data has been resolved to a specific geography, verified against each broker's own regulatory disclosures (see inc/regulation-checker.php for exactly what evidence resolved each one) -- but if we add a broker whose regulator code hasn't been checked that way yet, we show the code as researched and leave the geography unstated rather than guess. Always verify current licence status directly on the regulator's own public register before depositing funds -- links above go to each regulator's official register or its landing page, not a broker-specific pre-filled search.
  </p>
</div>

<script>
const RC_BROKERS = <?php echo wp_json_encode( $rc_payload ); ?>;

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
      status = 'EU-regulated (MiFID passporting, no CySEC number on file)';
    } else if (entry.note) {
      status = entry.note;
    } else {
      status = 'Confirmed, number not on file';
    }
    const geo = extraNote || 'Not separately itemized';
    const link = entry.register_url ? '<a href="' + entry.register_url + '" target="_blank" rel="nofollow noopener" style="color:var(--teal);">Verify</a>' : '&mdash;';
    return '<tr><td><strong>' + label + '</strong></td><td>' + status + '</td><td>' + geo + '</td><td>' + link + '</td></tr>';
  }

  function render(b) {
    emptyEl.style.display = 'none';
    dossierEl.style.display = 'block';

    nameEl.textContent = b.name;
    hqEl.textContent = b.hq ? ('HQ: ' + b.hq) : '';
    lastVerifiedEl.textContent = 'Regulation last reviewed: ' + b.review_dates.regulation_reviewed + ' · Next scheduled review: ' + b.review_dates.next_review;
    entityEl.textContent = b.entity || 'Not confirmed in our research';
    reviewLinkEl.href = b.review_url;

    let rows = '';
    rows += regRow('FCA (UK)', b.fca, 'United Kingdom');
    rows += regRow('CySEC (Cyprus)', b.cysec, 'Cyprus, passportable across the EU/EEA (subject to host-country notification)');
    rows += regRow('FSA Seychelles (offshore)', b.seychelles, 'Seychelles -- offshore, no statutory investor compensation scheme');
    b.other_regulators.forEach(function(r) {
      rows += regRow(r.code, { number: null, passported: false, note: 'Listed among confirmed regulators', register_url: r.register_url }, r.geography);
    });

    if (!rows) {
      rows = '<tr><td colspan="4" style="color:var(--ink-soft);">No regulator confirmed in our research for this broker.</td></tr>';
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
})();
</script>

<?php get_footer(); ?>
