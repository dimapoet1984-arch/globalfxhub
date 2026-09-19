<?php get_header(); ?>

<div class="article-wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / Blog
</div>

<?php get_template_part( 'template-parts/blog-loop' ); ?>

<?php get_footer(); ?>
