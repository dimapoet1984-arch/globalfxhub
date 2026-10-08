<?php
/**
 * Template Name: Broker Change Log
 * Description: Search any researched broker by name for a dated log of
 * when this site's own published data about it actually changed. See
 * inc/broker-changelog.php for exactly what this does and doesn't claim.
 */
get_header();

$cl_payload = globalfxhub_broker_changelog_payload();
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BROKER CHANGE LOG</div>
  <h1><?php the_title(); ?></h1>
  <p>Search any of our researched brokers for a dated log of when our published data about it actually changed -- a spread update, a new licence number, a platform added or dropped.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="methodology-note" style="margin:0 0 28px;">
    <strong>What this is, and isn't.</strong> Each entry here comes from this site's own data being diffed against what was previously published, dated to when that diff was detected -- never a reconstructed history of what a broker itself did before we started tracking. A broker showing only a single "Baseline recorded" entry simply hasn't had a tracked field change since this log began; it isn't a sign nothing has ever changed about that broker.
  </div>

  <div class="picker" style="grid-template-columns:1fr;">
    <div>
      <label for="clSearch">Search a broker</label>
      <input type="text" id="clSearch" list="clBrokerList" placeholder="e.g. Pepperstone" autocomplete="off">
      <datalist id="clBrokerList">
        <?php foreach ( $cl_payload as $b ) : ?>
        <option value="<?php echo esc_attr( $b['name'] ); ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
  </div>

  <div id="clEmpty" class="finder__empty">Search a broker above (e.g. "Pepperstone") to see its change log.</div>

  <div id="clLog" style="display:none;">
    <h2 id="clName" style="font-family:var(--font-display);font-weight:500;font-size:26px;margin:28px 0 16px;"></h2>
    <div class="rankings" id="clEntries"></div>
    <p style="margin-top:14px;">
      <a id="clReviewLink" href="#" style="color:var(--teal);">Read the full broker review &rarr;</a>
    </p>
  </div>
</div>

<script>
const CL_BROKERS = <?php echo wp_json_encode( $cl_payload ); ?>;

(function(){
  const searchEl = document.getElementById('clSearch');
  const emptyEl = document.getElementById('clEmpty');
  const logEl = document.getElementById('clLog');
  const nameEl = document.getElementById('clName');
  const entriesEl = document.getElementById('clEntries');
  const reviewLinkEl = document.getElementById('clReviewLink');

  const byName = {};
  CL_BROKERS.forEach(function(b) { byName[b.name.toLowerCase()] = b; });

  function findBroker(query) {
    const q = query.trim().toLowerCase();
    if (!q) return null;
    if (byName[q]) return byName[q];
    return CL_BROKERS.find(function(b) { return b.name.toLowerCase().includes(q); }) || null;
  }

  function render(b) {
    emptyEl.style.display = 'none';
    logEl.style.display = 'block';
    nameEl.textContent = b.name + ' -- change log';
    reviewLinkEl.href = b.review_url;

    if (!b.entries.length) {
      entriesEl.innerHTML = '<p style="padding:24px;color:var(--ink-soft);">No change history recorded yet for this broker.</p>';
      return;
    }
    let html = '<div class="rank-row head" style="grid-template-columns:44px 160px 1fr;"><span></span><span>Date</span><span>What changed</span></div>';
    b.entries.forEach(function(e, i) {
      html += '<div class="rank-row" style="grid-template-columns:44px 160px 1fr;">'
        + '<span class="rank-num">' + String(i + 1).padStart(2, '0') + '</span>'
        + '<span class="rank-detail">' + e.date + '</span>'
        + '<span class="rank-detail">' + e.summary + '</span>'
        + '</div>';
    });
    entriesEl.innerHTML = html;
  }

  function handleSearch() {
    const broker = findBroker(searchEl.value);
    if (broker) {
      render(broker);
    } else {
      emptyEl.style.display = 'block';
      logEl.style.display = 'none';
    }
  }

  searchEl.addEventListener('input', handleSearch);
  searchEl.addEventListener('change', handleSearch);
})();
</script>

<?php get_footer(); ?>
