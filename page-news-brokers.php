<?php
/**
 * Template Name: Broker News
 * Description: Renders the curated, non-syndicated Broker News list
 * from inc/broker-news.php -- acquisitions, licences, licence
 * withdrawals, fines, enforcement, new platforms, executive moves, and
 * product launches, each linked to the full, sourced detail already
 * disclosed on that broker's own review page.
 */
get_header();

$broker_news_items = globalfxhub_get_broker_news_items();
$broker_news_categories = globalfxhub_broker_news_categories();
$all_brokers_by_slug = array();
foreach ( globalfxhub_get_brokers() as $b ) {
    $all_brokers_by_slug[ $b['slug'] ] = $b;
}
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News</a> /
  <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BROKER NEWS</div>
  <h1><?php the_title(); ?></h1>
  <p>Acquisitions, licences, licence withdrawals, fines, enforcement, new platforms, executive moves, and product launches -- curated from our own broker research, each item linked to the full, sourced detail on that broker's review page. Not pulled from any news wire. Looking for market-wide analysis instead? See <a href="<?php echo esc_url( home_url( '/news/markets/' ) ); ?>">FX Market News</a>.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="news__grid">
    <?php if ( $broker_news_items ) : foreach ( $broker_news_items as $item ) :
        $broker = isset( $all_brokers_by_slug[ $item['broker_slug'] ] ) ? $all_brokers_by_slug[ $item['broker_slug'] ] : null;
        if ( ! $broker ) { continue; }
        $cat_label = isset( $broker_news_categories[ $item['category'] ] ) ? $broker_news_categories[ $item['category'] ] : 'Update';
    ?>
    <a href="<?php echo esc_url( home_url( '/reviews/' . $broker['slug'] . '/' ) ); ?>" class="news-item">
      <div class="tag"><?php echo esc_html( strtoupper( $cat_label ) ); ?></div>
      <h4><?php echo esc_html( $item['headline'] ); ?></h4>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( $item['body'], 24 ) ); ?></p>
      <time><?php echo esc_html( date_i18n( 'j M Y', strtotime( $item['date'] ) ) ); ?> &middot; <?php echo esc_html( $broker['name'] ); ?> review &rarr;</time>
    </a>
    <?php endforeach; else : ?>
    <p style="padding:24px;color:var(--ink-soft);">No broker news items yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
