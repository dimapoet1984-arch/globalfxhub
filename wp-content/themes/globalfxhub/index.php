<?php get_header(); ?>

<div class="article-wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <?php if ( is_category() || is_tag() || is_archive() ) { the_archive_title(); } else { echo 'Blog'; } ?>
</div>

<?php get_template_part( 'template-parts/blog-loop' ); ?>

<?php get_footer(); ?>
