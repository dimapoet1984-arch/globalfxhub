<?php
/**
 * Template Name: News Index
 * Description: Two distinct feeds -- a curated, non-syndicated Broker
 * News bulletin list (inc/broker-news.php) and the FX Market News
 * category of original analysis posts (inc/news-feed.php). Deliberately
 * not one merged "news" list: they're different kinds of content with
 * different depth and different cadence, and treating them the same
 * is exactly the pattern this redesign moved away from.
 */
get_header();

$fx_market_news_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 20,
    'category_name'  => 'fx-market-news',
    'orderby'        => 'date',
    'order'          => 'DESC',
) );

$broker_news_items = array_slice( globalfxhub_get_broker_news_items(), 0, 20 );
$broker_news_categories = globalfxhub_broker_news_categories();
$all_brokers_by_slug = array();
foreach ( globalfxhub_get_brokers() as $b ) {
    $all_brokers_by_slug[ $b['slug'] ] = $b;
}
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">MARKET &amp; BROKER NEWS</div>
  <h1><?php the_title(); ?></h1>
  <p>Two separate feeds: short, factual broker-specific bulletins (licences, fines, acquisitions), and original FX market analysis -- never a bulk reproduction of wire-service headlines.</p>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <div class="section__head">
    <div>
      <h2>Broker News</h2>
      <p style="color:var(--ink-soft);font-size:14px;">Acquisitions, licences, licence withdrawals, fines, enforcement, new platforms, executive moves, and product launches -- curated from our own broker research, each item linked to the full, sourced detail on that broker's review page. Not pulled from any news wire.</p>
    </div>
  </div>
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

<div class="wrap" style="padding-bottom:60px;">
  <div class="section__head">
    <div>
      <h2>FX Market News</h2>
      <p style="color:var(--ink-soft);font-size:14px;">Original 300-700 word analysis of genuinely market-moving developments, each with its own "why this matters" angle and a link back to the source story. Published only when a story clears a real significance bar -- a handful of pieces a week, not a daily digest.</p>
    </div>
  </div>
  <div class="news__grid">
    <?php if ( $fx_market_news_query->have_posts() ) : while ( $fx_market_news_query->have_posts() ) : $fx_market_news_query->the_post(); ?>
    <a href="<?php the_permalink(); ?>" class="news-item">
      <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( 'medium_large', array( 'class' => 'news-item__thumb' ) ); ?>
      <?php endif; ?>
      <div class="tag market">FX MARKET NEWS</div>
      <h4><?php the_title(); ?></h4>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20 ) ); ?></p>
      <time><?php echo esc_html( get_the_date() ); ?></time>
    </a>
    <?php endwhile; wp_reset_postdata(); else : ?>
    <p style="padding:24px;color:var(--ink-soft);">No FX market analysis published yet this week.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
