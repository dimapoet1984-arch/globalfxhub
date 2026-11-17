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
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( home_url( '/best/' ) ); ?>"><?php globalfxhub_te( 'nav_best' ); ?></a> /
  <?php echo esc_html( $list['title'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'best_eyebrow' ); ?></div>
  <h1><?php echo esc_html( $list['title'] ); ?></h1>
  <p><?php echo wp_kses( $list['intro'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( empty( $brokers ) ) : ?>
  <p style="padding:24px;color:var(--ink-soft);"><?php globalfxhub_te( 'best_no_brokers_match' ); ?></p>
  <?php else : ?>
  <div class="rankings">
    <div class="rank-row head">
      <span></span><span><?php globalfxhub_te( 'table_broker' ); ?></span><span class="col-fees"><?php globalfxhub_te( 'table_spread' ); ?></span><span class="col-plat"><?php globalfxhub_te( 'table_platforms' ); ?></span><span><?php echo esc_html( $list['sort_label'] ); ?></span><span></span>
    </div>
    <?php foreach ( $brokers as $i => $b ) : ?>
    <div class="rank-row">
      <span class="rank-num"><?php echo esc_html( str_pad( (string) ( $i + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
      <span class="rank-broker">
        <span class="rank-broker__name"><?php echo esc_html( $b['name'] ); ?></span>
        <span class="rank-broker__tag"><?php echo esc_html( globalfxhub_broker_regulation_label( $b ) ); ?><?php echo $b['founded'] ? ' &middot; ' . esc_html( sprintf( globalfxhub_t( 'rev_founded_inline' ), $b['founded'] ) ) : ''; ?></span>
      </span>
      <span class="rank-detail col-fees"><?php echo null !== $b['spread_eurusd'] ? esc_html( $b['spread_eurusd'] ) . ' pips (EUR/USD)' : esc_html( globalfxhub_t( 'tb_not_confirmed' ) ); ?></span>
      <span class="rank-detail col-plat"><?php echo esc_html( $b['platforms'] ? globalfxhub_render_platforms_list( $b['platforms'] ) : globalfxhub_t( 'tb_not_confirmed' ) ); ?></span>
      <span class="rank-score"><?php echo esc_html( round( call_user_func( $list['sort_key'], $b ), 2 ) ); ?> / 5</span>
      <span class="rank-ctas">
        <a href="<?php echo esc_url( globalfxhub_track_b_url( 'reviews/' . $b['slug'] . '/' ) ); ?>" class="rank-cta"><?php globalfxhub_te( 'btn_read_review' ); ?></a>
        <a href="#" class="rank-cta rank-cta--visit" target="_blank" rel="nofollow sponsored noopener" onclick="return false;"><?php globalfxhub_te( 'tb_visit_broker' ); ?></a>
      </span>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:18px;">
    <?php
    $methodology_link = '<a href="' . esc_url( home_url( '/#method' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'best_methodology_link' ) ) . '</a>';
    $all_lists_link = '<a href="' . esc_url( home_url( '/best/' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'best_all_lists_link' ) ) . '</a>';
    echo sprintf( esc_html( globalfxhub_t( 'best_built_from_note' ) ), $methodology_link, $all_lists_link );
    ?>
  </p>
</div>

<?php else : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php globalfxhub_te( 'best_index_h1' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'best_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'best_index_h1' ); ?></h1>
  <p><?php globalfxhub_te( 'best_index_intro' ); ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( $best_lists as $slug => $list ) : ?>
    <a href="<?php echo esc_url( globalfxhub_track_b_url( 'best/' . $slug . '/' ) ); ?>" class="guide">
      <div class="guide__time"><?php globalfxhub_te( 'best_top15_label' ); ?> &middot; <?php echo esc_html( $list['sort_label'] ); ?></div>
      <h3><?php echo esc_html( $list['title'] ); ?></h3>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( wp_strip_all_tags( $list['intro'] ), 22 ) ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
