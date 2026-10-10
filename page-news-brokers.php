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
  <div class="eyebrow">BROKER NEWS</div>
  <h1>Story not found</h1>
  <p>We don't have a Broker News item at that address. <a href="<?php echo esc_url( home_url( '/news/brokers/' ) ); ?>" style="color:var(--teal);">See the full Broker News feed</a>.</p>
</div>

<?php else :
    $broker = isset( $all_brokers_by_slug[ $news_item['broker_slug'] ] ) ? $all_brokers_by_slug[ $news_item['broker_slug'] ] : null;
    $cat_label = isset( $broker_news_categories[ $news_item['category'] ] ) ? $broker_news_categories[ $news_item['category'] ] : 'Update';
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News</a> /
  <a href="<?php echo esc_url( home_url( '/news/brokers/' ) ); ?>">Broker News</a> /
  <?php echo esc_html( $broker ? $broker['name'] : $news_item['broker_slug'] ); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow"><?php echo esc_html( strtoupper( $cat_label ) ); ?> &middot; <?php echo esc_html( date_i18n( 'j F Y', strtotime( $news_item['date'] ) ) ); ?></div>
  <h1><?php echo esc_html( $news_item['headline'] ); ?></h1>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <div style="color:var(--ink-soft);line-height:1.75;font-size:15.5px;">
    <?php if ( ! empty( $news_item['article']['paragraphs'] ) ) : ?>
      <?php foreach ( $news_item['article']['paragraphs'] as $p ) : ?>
      <p><?php echo esc_html( $p ); ?></p>
      <?php endforeach; ?>
    <?php else : ?>
      <p><?php echo esc_html( $news_item['body'] ); ?></p>
    <?php endif; ?>
  </div>

  <?php if ( $broker ) : ?>
  <div class="methodology-note" style="margin:24px 0 0;">
    This story is about <strong><?php echo esc_html( $broker['name'] ); ?></strong>, ranked #<?php echo esc_html( $broker['rank'] ); ?> in our rankings (<?php echo esc_html( $broker['scores']['overall'] ); ?> / 5 overall). <a href="<?php echo esc_url( home_url( '/reviews/' . $broker['slug'] . '/' ) ); ?>" style="color:var(--teal);">Read the full <?php echo esc_html( $broker['name'] ); ?> review &rarr;</a>
  </div>
  <?php endif; ?>

  <?php if ( ! empty( $news_item['article']['sources'] ) ) : ?>
  <div style="margin-top:28px;">
    <h2 style="font-family:var(--font-display);font-weight:500;font-size:18px;margin:0 0 10px;">Sources</h2>
    <ul style="margin:0;padding-left:20px;color:var(--ink-soft);font-size:14px;line-height:1.8;">
      <?php foreach ( $news_item['article']['sources'] as $source ) : ?>
      <li><a href="<?php echo esc_url( $source['url'] ); ?>" target="_blank" rel="nofollow noopener" style="color:var(--teal);"><?php echo esc_html( $source['label'] ); ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
  <?php endif; ?>

  <p style="margin-top:28px;"><a href="<?php echo esc_url( home_url( '/news/brokers/' ) ); ?>" style="color:var(--teal);">&larr; Back to all Broker News</a></p>
</div>

<?php endif; ?>

<?php else :
    $broker_news_items = globalfxhub_get_broker_news_items();
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News</a> /
  <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">BROKER NEWS</div>
  <h1><?php the_title(); ?></h1>
  <p>Acquisitions, licences, licence withdrawals, fines, enforcement, new platforms, executive moves, product launches, and operational issues -- curated from our own broker research. Each story is its own page here, in turn linking to the full, sourced detail on the relevant broker's own review page. Not pulled from any news wire. Looking for market-wide analysis instead? See <a href="<?php echo esc_url( home_url( '/news/markets/' ) ); ?>">FX Market News</a>.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="news__grid">
    <?php if ( $broker_news_items ) : foreach ( $broker_news_items as $item ) :
        $broker = isset( $all_brokers_by_slug[ $item['broker_slug'] ] ) ? $all_brokers_by_slug[ $item['broker_slug'] ] : null;
        if ( ! $broker ) { continue; }
        $cat_label = isset( $broker_news_categories[ $item['category'] ] ) ? $broker_news_categories[ $item['category'] ] : 'Update';
        $item_slug = globalfxhub_broker_news_item_slug( $item );
    ?>
    <a href="<?php echo esc_url( home_url( '/news/brokers/' . $item_slug . '/' ) ); ?>" class="news-item">
      <div class="tag"><?php echo esc_html( strtoupper( $cat_label ) ); ?></div>
      <h4><?php echo esc_html( $item['headline'] ); ?></h4>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( $item['body'], 24 ) ); ?></p>
      <time><?php echo esc_html( date_i18n( 'j M Y', strtotime( $item['date'] ) ) ); ?> &middot; <?php echo esc_html( $broker['name'] ); ?> &rarr;</time>
    </a>
    <?php endforeach; else : ?>
    <p style="padding:24px;color:var(--ink-soft);">No broker news items yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
