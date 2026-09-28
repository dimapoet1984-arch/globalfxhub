<?php
/**
 * Template Name: News Index
 * Description: Lists published posts in the "News" category.
 */
get_header();

$news_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 30,
    'category_name'  => 'news',
    'orderby'        => 'date',
    'order'          => 'DESC',
) );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">MARKET &amp; BROKER NEWS</div>
  <h1><?php the_title(); ?></h1>
  <p>Broker developments and market-moving headlines, in brief.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="news__grid">
    <?php if ( $news_query->have_posts() ) : while ( $news_query->have_posts() ) : $news_query->the_post();
        $news_type = get_post_meta( get_the_ID(), '_news_type', true );
    ?>
    <a href="<?php the_permalink(); ?>" class="news-item">
      <?php if ( has_post_thumbnail() ) : ?>
      <?php the_post_thumbnail( 'medium_large', array( 'class' => 'news-item__thumb' ) ); ?>
      <?php endif; ?>
      <div class="tag<?php echo 'market' === $news_type ? ' market' : ''; ?>"><?php echo 'market' === $news_type ? 'MARKET NEWS' : 'BROKER NEWS'; ?></div>
      <h4><?php the_title(); ?></h4>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20 ) ); ?></p>
      <time><?php echo esc_html( get_the_date() ); ?></time>
    </a>
    <?php endwhile; wp_reset_postdata(); else : ?>
    <p style="padding:24px;color:var(--ink-soft);">No news published yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
