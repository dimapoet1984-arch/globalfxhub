<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<div class="article-wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<article class="article-wrap art-head">
  <h1><?php the_title(); ?></h1>
</article>

<div class="article-wrap art-body" style="padding-bottom:60px;">
  <?php the_content(); ?>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
