<?php
/**
 * Template Name: Broker Change Log
 * Description: Search any researched broker by name for a dated log of
 * when this site's own published data about it actually changed. See
 * inc/broker-changelog.php for exactly what this does and doesn't claim.
 */
get_header();

$cl_payload = globalfxhub_broker_changelog_payload();

$cl_subscribe_result = null;
if ( isset( $_GET['unsubscribe'] ) ) {
    globalfxhub_changelog_unsubscribe( sanitize_text_field( wp_unslash( $_GET['unsubscribe'] ) ) );
    $cl_subscribe_result = 'unsubscribed';
} elseif ( isset( $_POST['globalfxhub_changelog_subscribe_nonce'] )
    && wp_verify_nonce( wp_unslash( $_POST['globalfxhub_changelog_subscribe_nonce'] ), 'globalfxhub_changelog_subscribe' ) ) {
    $cl_subscribe_result = globalfxhub_changelog_subscribe( isset( $_POST['changelog_email'] ) ? wp_unslash( $_POST['changelog_email'] ) : '' );
}
$cl_subscribe_messages = array(
    'subscribed'         => array( 'ok', globalfxhub_t( 'cl_msg_subscribed' ) ),
    'already_subscribed' => array( 'ok', globalfxhub_t( 'cl_msg_already_subscribed' ) ),
    'invalid'            => array( 'error', globalfxhub_t( 'cl_msg_invalid' ) ),
    'unsubscribed'       => array( 'ok', globalfxhub_t( 'cl_msg_unsubscribed' ) ),
);
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php globalfxhub_te( 'ptitle_broker_changelog' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'cl_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'ptitle_broker_changelog' ); ?></h1>
  <p><?php globalfxhub_te( 'cl_intro' ); ?></p>
  <?php $tool_learn_link = globalfxhub_resolve_learn_link( globalfxhub_tool_learn_links()['broker-changelog'] ); if ( $tool_learn_link ) : ?>
  <p style="font-size:13.5px;margin-top:8px;"><a href="<?php echo esc_url( $tool_learn_link['url'] ); ?>" style="color:var(--teal);"><?php globalfxhub_te( 'cmp_new_to_this' ); ?> <?php echo esc_html( $tool_learn_link['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="methodology-note" style="margin:0 0 28px;">
    <strong><?php globalfxhub_te( 'cl_what_this_is_strong' ); ?></strong> <?php globalfxhub_te( 'cl_what_this_is_body' ); ?>
  </div>

  <div class="methodology-note" style="margin:0 0 28px;background:var(--navy,#0d2436);color:#fff;">
    <strong><?php globalfxhub_te( 'cl_get_notified_strong' ); ?></strong> <?php globalfxhub_te( 'cl_get_notified_body' ); ?>
    <?php if ( $cl_subscribe_result && isset( $cl_subscribe_messages[ $cl_subscribe_result ] ) ) :
        list( $cl_msg_type, $cl_msg_text ) = $cl_subscribe_messages[ $cl_subscribe_result ];
    ?>
    <p style="margin:12px 0 0;font-weight:600;color:<?php echo 'error' === $cl_msg_type ? '#ffb4b4' : '#8fe3c7'; ?>;"><?php echo esc_html( $cl_msg_text ); ?></p>
    <?php else : ?>
    <form method="post" style="margin:14px 0 0;display:flex;flex-wrap:wrap;gap:10px;align-items:center;">
      <?php wp_nonce_field( 'globalfxhub_changelog_subscribe', 'globalfxhub_changelog_subscribe_nonce' ); ?>
      <input type="email" name="changelog_email" placeholder="you@example.com" required style="flex:1;min-width:220px;padding:10px 12px;border-radius:6px;border:1px solid var(--rule);">
      <button type="submit" class="btn btn--gold" style="white-space:nowrap;"><?php globalfxhub_te( 'cl_notify_button' ); ?></button>
    </form>
    <?php endif; ?>
  </div>

  <div class="picker" style="grid-template-columns:1fr;">
    <div>
      <label for="clSearch"><?php globalfxhub_te( 'rc_label_search' ); ?></label>
      <input type="text" id="clSearch" list="clBrokerList" placeholder="<?php echo esc_attr( globalfxhub_t( 'rc_search_placeholder' ) ); ?>" autocomplete="off">
      <datalist id="clBrokerList">
        <?php foreach ( $cl_payload as $b ) : ?>
        <option value="<?php echo esc_attr( $b['name'] ); ?>">
        <?php endforeach; ?>
      </datalist>
    </div>
  </div>

  <div id="clEmpty" class="finder__empty"><?php globalfxhub_te( 'cl_empty_state' ); ?></div>

  <div id="clLog" style="display:none;">
    <h2 id="clName" style="font-family:var(--font-display);font-weight:500;font-size:26px;margin:28px 0 16px;"></h2>
    <div class="rankings" id="clEntries"></div>
    <p style="margin-top:14px;">
      <a id="clReviewLink" href="#" style="color:var(--teal);"><?php globalfxhub_te( 'rc_read_full_review_link' ); ?></a>
    </p>
  </div>
</div>

<script>
const CL_BROKERS = <?php echo wp_json_encode( $cl_payload ); ?>;
// Translated strings for the JS-rendered change log below. e.summary
// (and e.date) are already localized server-side in the current
// viewer's language by globalfxhub_get_broker_changelog() (see
// inc/broker-changelog.php) -- reconstructed from each entry's stored
// field/old/new rather than echoing the English sentence baked in at
// write time. An entry written before 'old'/'new' were persisted falls
// back to that original English text, since there's nothing to
// reconstruct a translation from.
const T = <?php echo wp_json_encode( array(
    'changelog_suffix' => globalfxhub_t( 'cl_changelog_suffix' ),
    'no_history' => globalfxhub_t( 'cl_no_history' ),
    'col_date' => globalfxhub_t( 'cl_col_date' ),
    'col_what_changed' => globalfxhub_t( 'cl_col_what_changed' ),
) ); ?>;

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
    nameEl.textContent = b.name + ' ' + T.changelog_suffix;
    reviewLinkEl.href = b.review_url;

    if (!b.entries.length) {
      entriesEl.innerHTML = '<p style="padding:24px;color:var(--ink-soft);">' + T.no_history + '</p>';
      return;
    }
    let html = '<div class="rank-row head" style="grid-template-columns:44px 160px 1fr;"><span></span><span>' + T.col_date + '</span><span>' + T.col_what_changed + '</span></div>';
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
