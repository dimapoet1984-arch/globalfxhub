<?php
/**
 * Template Name: Broker News
 * Description: Renders the curated, non-syndicated Broker News feed
 * from inc/broker-news.php -- acquisitions, licences, licence
 * withdrawals, fines, enforcement, new platforms, executive moves,
 * product launches, and operational issues. Each item is its own
 * entity with its own page at /news/brokers/{slug}/ (see
 * globalfxhub_broker_news_rewrite_rules() in inc/broker-news.php),
 * which in turn links back to the full, sourced detail already
 * disclosed on the relevant broker's own review page -- never the
 * other way around (a news item is not a substitute for the review).
 */
get_header();

$broker_news_categories = globalfxhub_broker_news_categories();
$all_brokers_by_slug = array();
foreach ( globalfxhub_get_brokers() as $b ) {
    $all_brokers_by_slug[ $b['slug'] ] = $b;
}

$news_item_slug = get_query_var( 'broker_news_item' );
$news_item = $news_item_slug ? globalfxhub_get_broker_news_item_by_slug( $news_item_slug ) : null;
if ( $news_item_slug && ! $news_item ) {
    status_header( 404 );
}
?>

<?php if ( $news_item_slug ) :
    if ( ! $news_item ) :
?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'broker_news_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'broker_news_not_found_h1' ); ?></h1>
  <p><?php
  $broker_news_feed_link = '<a href="' . esc_url( globalfxhub_news_child_page_url( 'brokers' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'broker_news_link_text_see_feed' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'broker_news_not_found_body' ) ), $broker_news_feed_link );
  ?></p>
</div>

<?php else :
    $broker = isset( $all_brokers_by_slug[ $news_item['broker_slug'] ] ) ? $all_brokers_by_slug[ $news_item['broker_slug'] ] : null;
    $cat_label = isset( $broker_news_categories[ $news_item['category'] ] ) ? $broker_news_categories[ $news_item['category'] ] : globalfxhub_t( 'bn_cat_fallback' );
    $news_headline = globalfxhub_tf( 'broker_news', $news_item_slug, 'headline', $news_item['headline'] );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'news' ) ); ?>"><?php globalfxhub_te( 'nav_news' ); ?></a> /
  <a href="<?php echo esc_url( globalfxhub_news_child_page_url( 'brokers' ) ); ?>"><?php globalfxhub_te( 'footer_broker_news' ); ?></a> /
  <?php echo esc_html( $broker ? $broker['name'] : $news_item['broker_slug'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php echo esc_html( mb_strtoupper( $cat_label, 'UTF-8' ) ); ?> &middot; <?php echo esc_html( date_i18n( 'j F Y', strtotime( $news_item['date'] ) ) ); ?></div>
  <h1><?php echo esc_html( $news_headline ); ?></h1>
</div>

<div class="wrap content-with-sidebar" style="padding-bottom:40px;">
  <div class="content-main">
    <div style="color:var(--ink-soft);line-height:1.75;font-size:15.5px;">
      <?php if ( ! empty( $news_item['article']['paragraphs'] ) ) : ?>
        <?php foreach ( $news_item['article']['paragraphs'] as $p_i => $p ) : ?>
        <p><?php echo esc_html( globalfxhub_tf( 'broker_news', $news_item_slug, "article.paragraphs[$p_i]", $p ) ); ?></p>
        <?php endforeach; ?>
      <?php else : ?>
        <p><?php echo esc_html( globalfxhub_tf( 'broker_news', $news_item_slug, 'body', $news_item['body'] ) ); ?></p>
      <?php endif; ?>
    </div>

    <?php if ( $broker ) : ?>
    <div class="methodology-note" style="margin:24px 0 0;"><?php
    $broker_review_link = '<a href="' . esc_url( globalfxhub_track_b_url( 'reviews/' . $broker['slug'] . '/' ) ) . '" style="color:var(--teal);">' . sprintf( globalfxhub_t( 'broker_news_link_text_read_review' ), esc_html( $broker['name'] ) ) . '</a>';
    echo sprintf( globalfxhub_t( 'broker_news_about_broker' ), esc_html( $broker['name'] ), esc_html( $broker['rank'] ), esc_html( $broker['scores']['overall'] ), $broker_review_link );
    ?></div>
    <?php endif; ?>

    <?php if ( ! empty( $news_item['article']['sources'] ) ) : ?>
    <div style="margin-top:28px;">
      <h2 style="font-family:var(--font-display);font-weight:500;font-size:18px;margin:0 0 10px;"><?php globalfxhub_te( 'broker_news_sources_h2' ); ?></h2>
      <ul style="margin:0;padding-left:20px;color:var(--ink-soft);font-size:14px;line-height:1.8;">
        <?php foreach ( $news_item['article']['sources'] as $source ) : ?>
        <li><a href="<?php echo esc_url( $source['url'] ); ?>" target="_blank" rel="nofollow noopener" style="color:var(--teal);"><?php echo esc_html( $source['label'] ); ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <?php endif; ?>

    <p style="margin-top:28px;"><a href="<?php echo esc_url( globalfxhub_news_child_page_url( 'brokers' ) ); ?>" style="color:var(--teal);"><?php echo globalfxhub_t( 'broker_news_back_to_feed' ); ?></a></p>
  </div>
  <?php globalfxhub_ad_sidebar(); ?>
</div>

<?php endif; ?>

<?php else :
    $broker_news_items = globalfxhub_get_broker_news_items();
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> /
  <a href="<?php echo esc_url( globalfxhub_templated_page_url( 'news' ) ); ?>"><?php globalfxhub_te( 'nav_news' ); ?></a> /
  <?php globalfxhub_te( 'footer_broker_news' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'broker_news_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'footer_broker_news' ); ?></h1>
  <p><?php
  $fx_market_news_link = '<a href="' . esc_url( globalfxhub_news_child_page_url( 'markets' ) ) . '">' . esc_html( globalfxhub_t( 'footer_fx_market_news' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'broker_news_index_intro' ) ), $fx_market_news_link );
  ?></p>
</div>

<div class="wrap content-with-sidebar" style="padding-bottom:60px;">
  <div class="content-main">
    <div class="news__grid">
      <?php if ( $broker_news_items ) : foreach ( $broker_news_items as $item ) :
          $broker = isset( $all_brokers_by_slug[ $item['broker_slug'] ] ) ? $all_brokers_by_slug[ $item['broker_slug'] ] : null;
          if ( ! $broker ) { continue; }
          $cat_label = isset( $broker_news_categories[ $item['category'] ] ) ? $broker_news_categories[ $item['category'] ] : globalfxhub_t( 'bn_cat_fallback' );
          $item_slug = globalfxhub_broker_news_item_slug( $item );
          $item_headline = globalfxhub_tf( 'broker_news', $item_slug, 'headline', $item['headline'] );
          $item_body = globalfxhub_tf( 'broker_news', $item_slug, 'body', $item['body'] );
      ?>
      <a href="<?php echo esc_url( globalfxhub_track_b_url( 'news/brokers/' . $item_slug . '/' ) ); ?>" class="news-item">
        <div class="tag"><?php echo esc_html( mb_strtoupper( $cat_label, 'UTF-8' ) ); ?></div>
        <h4><?php echo esc_html( $item_headline ); ?></h4>
        <p><?php echo esc_html( globalfxhub_trim_excerpt( $item_body, 24 ) ); ?></p>
        <time><?php echo esc_html( date_i18n( 'j M Y', strtotime( $item['date'] ) ) ); ?> &middot; <?php echo esc_html( $broker['name'] ); ?> &rarr;</time>
      </a>
      <?php endforeach; else : ?>
      <p style="padding:24px;color:var(--ink-soft);"><?php globalfxhub_te( 'broker_news_empty' ); ?></p>
      <?php endif; ?>
    </div>
  </div>
  <?php globalfxhub_ad_sidebar(); ?>
</div>

<?php endif; ?>

<?php get_footer(); ?>
