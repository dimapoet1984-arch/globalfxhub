<?php
/**
 * Template Name: Broker Reviews
 * Description: Single review page per broker at /reviews/{slug}/, and a review index at /reviews/.
 */
get_header();

$slug = get_query_var( 'broker' );
if ( ! $slug && isset( $_GET['broker'] ) ) {
    $slug = sanitize_title( wp_unslash( $_GET['broker'] ) );
}
$broker = $slug ? globalfxhub_get_broker_by_slug( $slug ) : null;
?>

<?php if ( $broker ) :
    $pc = globalfxhub_broker_pros_cons( $broker );
    $sub_labels = array(
        'regulation'   => 'Regulation',
        'cost'         => 'Cost',
        'platforms'    => 'Platforms',
        'track_record' => 'Track record',
    );
    $reg_label = ( '—' === $broker['cysec'] )
        ? ( $broker['cysec_note'] ?? 'EU-regulated via passporting' )
        : 'No. ' . $broker['cysec'];
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>">Reviews</a> /
  <?php echo esc_html( $broker['name'] ); ?>
</div>

<div class="wrap page-head review-head">
  <div class="eyebrow">BROKER REVIEW &middot; RANK #<?php echo esc_html( str_pad( (string) $broker['rank'], 2, '0', STR_PAD_LEFT ) ); ?> OF 15</div>
  <h1><?php echo esc_html( $broker['name'] ); ?> review</h1>
  <p><?php echo esc_html( $broker['blurb'] ); ?></p>
  <div class="review-head__meta">
    <span class="review-head__score"><span class="score-num"><?php echo esc_html( $broker['scores']['overall'] ); ?></span> / 5 overall</span>
    <span class="review-head__tag"><?php echo esc_html( $reg_label ); ?> &middot; est. <?php echo esc_html( $broker['founded'] ); ?></span>
  </div>
  <div class="hero__actions" style="margin-top:22px;">
    <a href="<?php echo esc_url( home_url( '/compare/' ) . '?a=' . rawurlencode( $broker['slug'] ) ); ?>" class="btn btn--gold">Compare vs another broker</a>
    <a href="https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/" target="_blank" rel="noopener" class="btn btn--ghost" style="color:var(--navy);border-color:var(--rule);">Verify on CySEC register</a>
    <a href="#" class="btn btn--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>
  </div>
</div>

<div class="wrap review-grid">
  <div>
    <table class="compare-table review-facts">
      <tbody>
        <tr><th>Regulated entity</th><td><?php echo esc_html( $broker['entity'] ); ?></td></tr>
        <tr><th>CySEC licence</th><td><?php echo esc_html( $reg_label ); ?></td></tr>
        <tr><th>Other Tier-1 regulators</th><td><?php echo esc_html( implode( ', ', $broker['other_reg'] ) ); ?></td></tr>
        <tr><th>Founded</th><td><?php echo esc_html( $broker['founded'] ); ?></td></tr>
        <tr><th>Headquarters</th><td><?php echo esc_html( $broker['hq'] ); ?></td></tr>
        <tr><th>Minimum deposit</th><td><?php echo esc_html( $broker['min_deposit_display'] ); ?></td></tr>
        <tr><th>Avg. spread (EUR/USD)</th><td><?php echo esc_html( $broker['spread_eurusd'] ); ?> pips</td></tr>
        <tr><th>Platforms</th><td><?php echo esc_html( implode( ', ', $broker['platforms'] ) ); ?></td></tr>
        <tr><th>Instruments</th><td><?php echo esc_html( $broker['instruments'] ); ?></td></tr>
      </tbody>
    </table>

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
      <div class="bar-track"><div class="bar-fill" style="width:<?php echo esc_attr( $broker['scores'][ $key ] / 5 * 100 ); ?>%;"></div></div>
      <span class="review-scorebox__num"><?php echo esc_html( $broker['scores'][ $key ] ); ?> / 5</span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="wrap methodology-note">
  <strong>How this score is calculated:</strong> 30% regulatory footprint, 30% cost (spread + minimum deposit), 20% platform breadth, 20% track record — each ranked relative to the other brokers in our researched set, not hands-on tested. See the <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">full methodology</a>. Verify current terms directly with the broker and the CySEC register before depositing funds. This is not personalized financial advice.
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
?>

<div class="wrap page-head">
  <div class="eyebrow">BROKER REVIEWS</div>
  <h1><?php the_title(); ?></h1>
  <p>In-depth reviews of every CySEC-regulated broker in our rankings, built from the same disclosed methodology behind the scores.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span>Broker</span><span class="col-fees">Avg. spread</span><span class="col-plat">Platforms</span><span>Score</span><span></span>
    </div>
    <?php foreach ( $brokers as $b ) : ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) $b['rank'], 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?></span>
        <span class="rank-broker__tag"><?php echo esc_html( '—' === $b['cysec'] ? 'EU-regulated (MiFID passporting)' : 'CySEC ' . $b['cysec'] ); ?> &middot; est. <?php echo esc_html( $b['founded'] ); ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo esc_html( $b['spread_eurusd'] ); ?> pips (EUR/USD)</span>
      <span class="rank-detail col-plat"><?php echo esc_html( implode( ', ', $b['platforms'] ) ); ?></span>
      <span class="rank-score"><?php echo esc_html( $b['scores']['overall'] ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta">Read review</a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
