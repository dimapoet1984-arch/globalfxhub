<?php
/**
 * Template Name: Broker Reviews
 * Description: Single review page per broker at /reviews/{slug}/, a
 * review index at /reviews/, and "alternatives to" pages at
 * /reviews/{slug}/alternatives/ for a bounded set of reference brokers
 * (top 40 by rank) -- see inc/broker-alternatives.php.
 */
get_header();

$slug = get_query_var( 'broker' );
if ( ! $slug && isset( $_GET['broker'] ) ) {
    $slug = sanitize_title( wp_unslash( $_GET['broker'] ) );
}
$broker = $slug ? globalfxhub_get_broker_by_slug( $slug ) : null;

$alts_slug = get_query_var( 'broker_alts' );
$alts_data = $alts_slug ? globalfxhub_broker_alternatives( $alts_slug ) : null;
if ( $alts_slug && ! $alts_data ) {
    status_header( 404 );
}
?>

<?php if ( $alts_slug ) :
    if ( ! $alts_data ) :
?>

<div class="wrap page-head">
  <div class="eyebrow">BROKER ALTERNATIVES</div>
  <h1>Alternatives not available</h1>
  <p>We don't have an alternatives page for that broker -- it may be outside our top-ranked set. <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);">See all researched broker reviews</a>.</p>
</div>

    <?php else :
        $ref = $alts_data['broker'];
        $alt_brokers = $alts_data['alternatives'];
    ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>">Reviews</a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' . $ref['slug'] . '/' ) ); ?>"><?php echo esc_html( $ref['name'] ); ?></a> /
  Alternatives
</div>

<div class="wrap page-head">
  <div class="eyebrow">BROKER ALTERNATIVES</div>
  <h1><?php echo esc_html( $ref['name'] ); ?> Alternatives</h1>
  <p>Brokers below share at least two of <?php echo esc_html( $ref['name'] ); ?>'s confirmed instruments or platforms, ranked by our disclosed nine-category score -- not a popularity guess or a paid placement.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( empty( $alt_brokers ) ) : ?>
  <p style="padding:24px;color:var(--ink-soft);">No sufficiently similar broker found in our researched set.</p>
  <?php else : ?>
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span>Broker</span><span class="col-fees">Min. deposit</span><span class="col-plat">Platforms</span><span>Score</span><span></span>
    </div>
    <?php foreach ( $alt_brokers as $i => $alt ) :
        $vs_pair_check = globalfxhub_vs_pair( $ref['slug'], $alt['slug'] );
    ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker"><span class="rank-broker__name"><?php echo esc_html( $alt['name'] ); ?></span><span class="rank-broker__tag"><?php echo esc_html( globalfxhub_broker_regulation_label( $alt ) ); ?></span></span>
      <span class="rank-detail col-fees"><?php echo esc_html( $alt['min_deposit_display'] ? $alt['min_deposit_display'] : 'Not confirmed' ); ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $alt['platforms'] ? implode( ', ', $alt['platforms'] ) : 'Not confirmed' ); ?></span>
      <span class="rank-score"><?php echo esc_html( $alt['scores']['overall'] ?? 'N/A' ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $alt['slug'] . '/' ) ); ?>" class="rank-cta">Read review</a>
        <?php if ( $vs_pair_check ) : ?>
        <a href="<?php echo esc_url( home_url( '/compare/' . $ref['slug'] . '-vs-' . $alt['slug'] . '/' ) ); ?>" class="rank-cta">Compare vs <?php echo esc_html( $ref['name'] ); ?></a>
        <?php endif; ?>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <p style="margin-top:20px;"><a href="<?php echo esc_url( home_url( '/reviews/' . $ref['slug'] . '/' ) ); ?>" style="color:var(--teal);">&larr; Back to the full <?php echo esc_html( $ref['name'] ); ?> review</a></p>
</div>

    <?php endif;
elseif ( $broker ) :
    $pc = globalfxhub_broker_pros_cons( $broker );
    $who = globalfxhub_broker_who_for( $broker );
    $investor_protection = globalfxhub_broker_investor_protection( $broker );
    $deep_dive_na = 'Not independently confirmed in our research -- check the broker\'s own site or contact support directly.';
    $deep_dive_fields = array(
        'account_types'          => 'Account types',
        'execution_model'        => 'Execution model',
        'withdrawal_deposit_note' => 'Withdrawals & deposits',
        'overnight_financing'    => 'Overnight financing (swap)',
        'currency_conversion_fee' => 'Currency conversion cost',
        'inactivity_fee'         => 'Inactivity fee',
        'customer_service_note'  => 'Customer service',
        'mobile_note'            => 'Mobile experience',
        'education_note'         => 'Educational offering',
    );
    $sub_labels = array(
        'regulation'            => 'Regulation & client protection',
        'cost'                  => 'Trading costs',
        'non_trading_fees'      => 'Non-trading fees',
        'platforms'             => 'Platforms & tools',
        'execution'              => 'Execution / trading conditions',
        'product_range'         => 'Product range',
        'deposits_withdrawals'  => 'Deposits & withdrawals',
        'transparency'          => 'Transparency',
        'track_record'          => 'Track record',
    );
    $reg_label = globalfxhub_broker_regulation_label( $broker );
    $verify_links = globalfxhub_broker_verify_links( $broker );
    $not_confirmed = 'Not independently confirmed';
    $review_dates = globalfxhub_broker_review_dates( $broker );
    $section_learn_links = array_map( 'globalfxhub_resolve_learn_link', globalfxhub_review_section_learn_links() );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>">Reviews</a> /
  <?php echo esc_html( $broker['name'] ); ?>
</div>

<div class="wrap page-head review-head">
  <div class="eyebrow">BROKER REVIEW &middot; RANK #<?php echo esc_html( str_pad( (string) $broker['rank'], 2, '0', STR_PAD_LEFT ) ); ?> OF <?php echo esc_html( count( globalfxhub_get_brokers() ) ); ?></div>
  <h1><?php echo esc_html( $broker['name'] ); ?> review</h1>
  <p><?php echo esc_html( $broker['blurb'] ); ?></p>
  <div class="review-head__meta">
    <span class="review-head__score"><span class="score-num"><?php echo esc_html( $broker['scores']['overall'] ); ?></span> / 5 overall</span>
    <span class="review-head__tag"><?php echo esc_html( $reg_label ); ?><?php echo $broker['founded'] ? ' &middot; est. ' . esc_html( $broker['founded'] ) : ''; ?></span>
  </div>
  <div class="review-head__dates" style="font-size:12.5px;color:var(--ink-soft);margin-top:6px;display:flex;flex-wrap:wrap;gap:14px;">
    <span>Data last reviewed: <?php echo esc_html( $review_dates['data_reviewed'] ); ?></span>
    <span>Regulation last reviewed: <?php echo esc_html( $review_dates['regulation_reviewed'] ); ?></span>
    <span>Pricing last checked: <?php echo esc_html( $review_dates['pricing_checked'] ); ?></span>
    <span>Next scheduled review: <?php echo esc_html( $review_dates['next_review'] ); ?></span>
  </div>
  <?php
  $notice_parts = array_filter( array(
      isset( $broker['cysec_note'] ) ? $broker['cysec_note'] : null,
      isset( $broker['fca_note'] ) ? $broker['fca_note'] : null,
      isset( $broker['seychelles_note'] ) ? $broker['seychelles_note'] : null,
  ) );
  if ( ! empty( $notice_parts ) ) :
  ?>
  <div class="review-notice"><strong>Worth knowing:</strong> <?php echo esc_html( implode( ' ', $notice_parts ) ); ?></div>
  <?php endif; ?>
  <div class="hero__actions" style="margin-top:22px;">
    <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=' . rawurlencode( $broker['slug'] ) ); ?>" class="btn btn--gold">Compare vs another broker</a>
    <a href="<?php echo esc_url( home_url( '/broker-finder/' ) ); ?>" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);">Find similar brokers</a>
    <?php if ( globalfxhub_broker_alternatives( $broker['slug'] ) ) : ?>
    <a href="<?php echo esc_url( home_url( '/reviews/' . $broker['slug'] . '/alternatives/' ) ); ?>" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);">See alternatives</a>
    <?php endif; ?>
    <?php foreach ( $verify_links as $link ) : ?>
    <a href="<?php echo esc_url( $link['url'] ); ?>" target="_blank" rel="noopener" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);"><?php echo esc_html( $link['label'] ); ?></a>
    <?php endforeach; ?>
    <a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>
  </div>
</div>

<div class="wrap review-grid">
  <div>
    <table class="compare-table review-facts">
      <tbody>
        <tr><th>Regulated entity</th><td><?php echo esc_html( $broker['entity'] ); ?></td></tr>
        <tr><th>Regulation</th><td><?php echo esc_html( $reg_label ); ?></td></tr>
        <tr><th>Other Tier-1 regulators</th><td><?php echo esc_html( $broker['other_reg'] ? implode( ', ', $broker['other_reg'] ) : 'None confirmed' ); ?></td></tr>
        <tr><th>Founded</th><td><?php echo esc_html( $broker['founded'] ? $broker['founded'] : $not_confirmed ); ?></td></tr>
        <tr><th>Headquarters</th><td><?php echo esc_html( $broker['hq'] ? $broker['hq'] : $not_confirmed ); ?></td></tr>
        <tr><th>Minimum deposit</th><td><?php echo esc_html( $broker['min_deposit_display'] ? $broker['min_deposit_display'] : $not_confirmed ); ?></td></tr>
        <tr><th>Avg. spread (EUR/USD)</th><td><?php echo null !== $broker['spread_eurusd'] ? esc_html( $broker['spread_eurusd'] ) . ' pips' : esc_html( $not_confirmed ); ?></td></tr>
        <tr><th>Platforms</th><td><?php echo esc_html( $broker['platforms'] ? implode( ', ', $broker['platforms'] ) : $not_confirmed ); ?></td></tr>
        <tr><th>Instruments</th><td><?php echo esc_html( $broker['instruments'] ? $broker['instruments'] : $not_confirmed ); ?></td></tr>
      </tbody>
    </table>
    <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( home_url( '/regulation-checker/' ) . '?broker=' . rawurlencode( $broker['slug'] ) ); ?>" style="color:var(--teal);">See <?php echo esc_html( $broker['name'] ); ?>'s full regulation dossier &rarr;</a><?php if ( $section_learn_links['regulation'] ) : ?> &middot; <a href="<?php echo esc_url( $section_learn_links['regulation']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $section_learn_links['regulation']['label'] ); ?></a><?php endif; ?></p>

    <div class="review-proscons">
      <div class="review-proscons__col review-proscons__pros">
        <h3>Strengths</h3>
        <ul>
          <?php foreach ( $pc['pros'] as $pro ) : ?><li><?php echo esc_html( $pro ); ?></li><?php endforeach; ?>
        </ul>
      </div>
      <div class="review-proscons__col review-proscons__cons">
        <h3>Weaknesses</h3>
        <ul>
          <?php foreach ( $pc['cons'] as $con ) : ?><li><?php echo esc_html( $con ); ?></li><?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>

  <div class="review-scorebox">
    <h3>Score breakdown</h3>
    <?php foreach ( $sub_labels as $key => $label ) : ?>
    <div class="subscore-row">
      <?php echo esc_html( $label ); ?>
      <?php if ( null === $broker['scores'][ $key ] ) : ?>
      <span class="review-scorebox__na">Not enough confirmed data to score</span>
      <?php else : ?>
      <div class="bar-track"><div class="bar-fill" style="width:<?php echo esc_attr( $broker['scores'][ $key ] / 5 * 100 ); ?>%;"></div></div>
      <span class="review-scorebox__num"><?php echo esc_html( $broker['scores'][ $key ] ); ?> / 5</span>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 20px;">Who <?php echo esc_html( $broker['name'] ); ?> is -- and isn't -- a good fit for</h2>
  <div class="review-proscons">
    <div class="review-proscons__col review-proscons__pros">
      <h3>Likely a good fit if you're...</h3>
      <ul>
        <?php foreach ( $who['for'] as $reason ) : ?><li><?php echo esc_html( $reason ); ?></li><?php endforeach; ?>
      </ul>
    </div>
    <div class="review-proscons__col review-proscons__cons">
      <h3>Look elsewhere if you're...</h3>
      <ul>
        <?php foreach ( $who['against'] as $reason ) : ?><li><?php echo esc_html( $reason ); ?></li><?php endforeach; ?>
      </ul>
    </div>
  </div>
  <p style="color:var(--ink-soft);font-size:13px;margin-top:10px;">Derived from the same confirmed data behind this broker's score (regulation, cost, platforms, deposit minimum) -- not a separate editorial judgment.</p>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 10px;">Investor protection</h2>
  <p><?php echo esc_html( $investor_protection ); ?></p>
  <?php if ( $section_learn_links['investor_protection'] ) : ?>
  <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( $section_learn_links['investor_protection']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $section_learn_links['investor_protection']['label'] ); ?> &rarr;</a></p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;">Accounts, costs & support in more depth</h2>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;">Beyond the headline spread and minimum deposit above. Where our research hasn't independently confirmed a specific detail, we say so rather than guess -- verify directly with the broker before relying on it.</p>
  <table class="compare-table review-facts">
    <tbody>
      <?php foreach ( $deep_dive_fields as $field_key => $field_label ) : ?>
      <tr><th><?php echo esc_html( $field_label ); ?></th><td><?php echo esc_html( ! empty( $broker[ $field_key ] ) ? $broker[ $field_key ] : $deep_dive_na ); ?></td></tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <p style="margin:10px 0 0;font-size:13.5px;"><a href="<?php echo esc_url( home_url( '/cost-calculator/' ) ); ?>" style="color:var(--teal);">See how <?php echo esc_html( $broker['name'] ); ?>'s spread cost compares across every researched broker &rarr;</a><?php if ( $section_learn_links['fees'] ) : ?> &middot; <a href="<?php echo esc_url( $section_learn_links['fees']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $section_learn_links['fees']['label'] ); ?></a><?php endif; ?></p>
</div>

<div class="wrap methodology-note">
  <strong>How this score is calculated:</strong> Nine weighted categories against a fixed, disclosed rubric -- regulation & client protection 30%, trading costs 20%, non-trading fees 10%, platforms & tools 10%, execution/trading conditions 10%, product range 5%, deposits/withdrawals 5%, transparency 5%, track record 5% -- not a ranking relative to other brokers, and not hands-on tested. See the <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">full methodology</a>. Verify current terms directly with the broker and its regulator's public register before depositing funds. This is not personalized financial advice.
</div>

<?php
$ext = globalfxhub_broker_external_reviews( $broker['slug'] );
if ( ! empty( $ext['reviews'] ) ) :
?>
<div class="wrap" style="padding-bottom:20px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;">What other reviewers say</h2>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;">Ratings as found via each site's published/indexed content, not our own testing and not independently re-verified against the live page -- treat as a snapshot and check the source before relying on it. Each card links to where we found it.</p>
  <div class="external-reviews">
    <?php foreach ( $ext['reviews'] as $r ) : ?>
    <a href="<?php echo esc_url( $r['url'] ); ?>" target="_blank" rel="noopener nofollow" class="external-review-card">
      <div class="external-review-card__source"><?php echo esc_html( $r['source'] ); ?></div>
      <?php if ( null !== $r['rating'] ) : ?>
      <div class="external-review-card__score"><?php echo esc_html( $r['rating'] ); ?> <span>/ <?php echo esc_html( $r['scale'] ); ?><?php echo ! empty( $r['label'] ) ? ' ' . esc_html( $r['label'] ) : ''; ?></span></div>
      <?php else : ?>
      <div class="external-review-card__score external-review-card__score--link">Read the review &rarr;</div>
      <?php endif; ?>
      <?php if ( ! empty( $r['note'] ) ) : ?><div class="external-review-card__note"><?php echo esc_html( $r['note'] ); ?></div><?php endif; ?>
    </a>
    <?php endforeach; ?>
    <?php if ( null !== $ext['average'] ) : ?>
    <div class="external-review-card external-review-card--average">
      <div class="external-review-card__source">Average of star-rated sources above</div>
      <div class="external-review-card__score"><?php echo esc_html( $ext['average'] ); ?> <span>/ 5</span></div>
    </div>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<?php $changelog_entries = array_slice( globalfxhub_get_broker_changelog( $broker['slug'] ), 0, 5 ); ?>
<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 6px;">Recent changes</h2>
  <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 20px;">A dated log of when our own published data about this broker actually changed -- not a reconstructed history of what the broker itself did. See the full <a href="<?php echo esc_url( home_url( '/broker-changelog/' ) ); ?>" style="color:var(--teal);">change log tool</a> for every broker.</p>
  <div class="rankings">
    <?php foreach ( $changelog_entries as $i => $entry ) : ?>
    <div class="rank-row" style="grid-template-columns:44px 160px 1fr;">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-detail"><?php echo esc_html( $entry['date'] ); ?></span>
      <span class="rank-detail"><?php echo esc_html( $entry['summary'] ); ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php
$others = array_filter( globalfxhub_get_brokers(), function( $b ) use ( $broker ) {
    return $b['slug'] !== $broker['slug'];
} );
usort( $others, function( $a, $b ) { return $a['rank'] <=> $b['rank']; } );
$others = array_slice( $others, 0, 3 );
?>
<div class="wrap" style="padding-bottom:60px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 20px;">Other reviewed brokers</h2>
  <div class="guides__grid">
    <?php foreach ( $others as $other ) : ?>
    <a href="<?php echo esc_url( home_url( '/reviews/' . $other['slug'] . '/' ) ); ?>" class="guide">
      <div class="guide__time">#<?php echo esc_html( $other['rank'] ); ?> &middot; <?php echo esc_html( $other['scores']['overall'] ); ?> / 5</div>
      <h3><?php echo esc_html( $other['name'] ); ?></h3>
      <p><?php echo esc_html( $other['blurb'] ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php else :
    $brokers = globalfxhub_get_brokers();
    usort( $brokers, function( $a, $b ) { return $a['rank'] <=> $b['rank']; } );

    $search_query = isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';
    if ( $search_query ) {
        $needle = strtolower( $search_query );
        $brokers = array_values( array_filter( $brokers, function( $b ) use ( $needle ) {
            return false !== strpos( strtolower( $b['name'] ), $needle );
        } ) );
    }
?>

<div class="wrap page-head">
  <div class="eyebrow">BROKER REVIEWS</div>
  <h1><?php the_title(); ?></h1>
  <?php if ( $search_query ) : ?>
  <p>Showing results for "<?php echo esc_html( $search_query ); ?>" &middot; <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);">clear search</a></p>
  <?php else : ?>
  <p>In-depth reviews of every broker in our rankings -- CySEC-, FCA-, or Seychelles FSA-licensed, or more than one -- built from the same disclosed methodology behind the scores.</p>
  <?php endif; ?>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( $search_query && empty( $brokers ) ) : ?>
  <p style="padding:24px;color:var(--ink-soft);">No brokers matched "<?php echo esc_html( $search_query ); ?>". <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" style="color:var(--teal);">View all reviews →</a></p>
  <?php else : ?>
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span>Broker</span><span class="col-fees">Avg. spread</span><span class="col-plat">Platforms</span><span>Score</span><span></span>
    </div>
    <?php foreach ( $brokers as $b ) : ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) $b['rank'], 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?></span>
        <span class="rank-broker__tag"><?php echo esc_html( globalfxhub_broker_regulation_label( $b ) ); ?><?php echo $b['founded'] ? ' &middot; est. ' . esc_html( $b['founded'] ) : ''; ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo null !== $b['spread_eurusd'] ? esc_html( $b['spread_eurusd'] ) . ' pips (EUR/USD)' : 'Not confirmed'; ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $b['platforms'] ? implode( ', ', $b['platforms'] ) : 'Not confirmed' ); ?></span>
      <span class="rank-score"><?php echo esc_html( $b['scores']['overall'] ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta">Read review</a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php endif; ?>

<?php get_footer(); ?>
