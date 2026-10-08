<?php
/**
 * Template Name: FX Market News
 * Description: Lists posts in the "FX Market News" category -- original
 * 300-700 word analysis of genuinely market-moving developments. See
 * inc/news-feed.php for how and why this is a low-volume weekly digest,
 * not a daily feed.
 */
get_header();

$fx_market_news_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 30,
    'category_name'  => 'fx-market-news',
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/news/' ) ); ?>">News</a> /
  <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">FX MARKET NEWS</div>
  <h1><?php the_title(); ?></h1>
  <p>Original 300-700 word analysis of genuinely market-moving developments, each with a "why this matters" angle for the relevant pair or asset and a link back to the source story. Published only when a story clears a real significance bar -- a handful of pieces a week, not a daily digest. Looking for broker-specific news instead? See <a href="<?php echo esc_url( home_url( '/news/brokers/' ) ); ?>">Broker News</a>.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
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
