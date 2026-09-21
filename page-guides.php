<?php
/**
 * Template Name: Guides Index
 * Description: Lists published posts in the "Guides" category.
 */
get_header();

$guides_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'category_name'  => 'guides',
    'orderby'        => 'date',
    'order'          => 'ASC',
) );
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">EDUCATION</div>
  <h1><?php the_title(); ?></h1>
  <p>Plain-English guides to how forex trading actually works -- no jargon, no sales pitch.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php if ( $guides_query->have_posts() ) : while ( $guides_query->have_posts() ) : $guides_query->the_post(); ?>
    <a href="<?php the_permalink(); ?>" class="guide">
      <div class="guide__time"><?php echo esc_html( globalfxhub_reading_time( get_the_content() ) ); ?> MIN READ</div>
      <h3><?php the_title(); ?></h3>
      <p><?php echo esc_html( globalfxhub_trim_excerpt( get_the_excerpt() ? get_the_excerpt() : get_the_content(), 20 ) ); ?></p>
    </a>
    <?php endwhile; wp_reset_postdata(); else : ?>
    <p style="padding:24px;color:var(--ink-soft);">No guides published yet.</p>
    <?php endif; ?>
  </div>
</div>

<?php get_footer(); ?>
