<?php
/**
 * Template Name: Best Brokers
 * Description: /best/ index of commercial ranking lists, and
 * /best/{slug}/ for each individual list -- filtered, re-sorted views
 * of the same broker dataset and the same disclosed scores used
 * everywhere else on the site. See inc/best.php for each list's exact
 * filter and sort key.
 */
get_header();

$bestlist_slug = get_query_var( 'bestlist' );
$best_lists = globalfxhub_get_best_lists();
?>

<?php if ( $bestlist_slug && isset( $best_lists[ $bestlist_slug ] ) ) :
    $list = $best_lists[ $bestlist_slug ];
    $brokers = globalfxhub_get_best_list_brokers( $bestlist_slug, 15 );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/best/' ) ); ?>">Best Brokers</a> /
  <?php echo esc_html( $list['title'] ); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BEST BROKERS</div>
  <h1><?php echo esc_html( $list['title'] ); ?></h1>
  <p><?php echo wp_kses( $list['intro'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( empty( $brokers ) ) : ?>
  <p style="padding:24px;color:var(--ink-soft);">No brokers in our researched set currently meet this list's criteria.</p>
  <?php else : ?>
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span>Broker</span><span class="col-fees">Avg. spread</span><span class="col-plat">Platforms</span><span><?php echo esc_html( $list['sort_label'] ); ?></span><span></span>
    </div>
    <?php foreach ( $brokers as $i => $b ) : ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?></span>
        <span class="rank-broker__tag"><?php echo esc_html( globalfxhub_broker_regulation_label( $b ) ); ?><?php echo $b['founded'] ? ' &middot; est. ' . esc_html( $b['founded'] ) : ''; ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo null !== $b['spread_eurusd'] ? esc_html( $b['spread_eurusd'] ) . ' pips (EUR/USD)' : 'Not confirmed'; ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $b['platforms'] ? implode( ', ', $b['platforms'] ) : 'Not confirmed' ); ?></span>
      <span class="rank-score"><?php echo esc_html( round( call_user_func( $list['sort_key'], $b ), 2 ) ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( home_url( '/reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta">Read review</a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;">Visit Broker</a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;max-width:74ch;">
    Built from our <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">disclosed nine-category methodology</a> -- the same absolute rubric and the same scores shown on every individual review, filtered and sorted for this specific list rather than scored differently for it. See <a href="<?php echo esc_url( home_url( '/best/' ) ); ?>" style="color:var(--teal);">all best-broker lists</a>.
  </p>
</div>

<?php else : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BEST BROKERS</div>
  <h1><?php the_title(); ?></h1>
  <p>Filtered, re-sorted views of the same broker dataset and the same disclosed scores used sitewide -- never a separate, looser standard for a commercially-framed page. Pick a list below.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $best_lists as $slug => $list ) : ?>
    <a href="<?php echo esc_url( home_url( '/best/' . $slug . '/' ) ); ?>" class="guide">
      <div class="guide__time">TOP 15 &middot; <?php echo esc_html( $list['sort_label'] ); ?></div>
      <h3><?php echo esc_html( $list['title'] ); ?></h3>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( wp_strip_all_tags( $list['intro'] ), 22 ) ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
