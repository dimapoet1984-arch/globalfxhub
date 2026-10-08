<?php get_header(); ?>

<?php while ( have_posts() ) : the_post();
    $cats      = get_the_category();
    $cat_slug  = ! empty( $cats ) ? $cats[0]->slug : '';
    $cat_name  = ! empty( $cats ) ? strtoupper( $cats[0]->name ) : 'ARTICLE';
    $byline    = globalfxhub_get_post_byline( get_the_ID() );

    /*
     * The breadcrumb's parent link depends on which section this post
     * actually belongs to -- a Learn-category post belongs under
     * /learn/, an FX Market News post under /news/markets/ -- rather
     * than a single hardcoded "Blog" link back to the old unfiltered
     * archive every post used to share.
     */
    $section_map = array(
        'learn'          => array( 'Learn', home_url( '/learn/' ) ),
        'fx-market-news' => array( 'FX Market News', home_url( '/news/markets/' ) ),
    );
    if ( isset( $section_map[ $cat_slug ] ) ) {
        list( $section_label, $section_url ) = $section_map[ $cat_slug ];
    } else {
        $section_label = 'Learn';
        $section_url   = home_url( '/learn/' );
    }
?>

<div class="article-wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( $section_url ); ?>"><?php echo esc_html( $section_label ); ?></a> /
  <?php the_title(); ?>
</div>

<article class="article-wrap art-head">
  <span class="art-cat"><?php echo esc_html( $cat_name ); ?></span>
  <h1><?php the_title(); ?></h1>
  <?php if ( has_excerpt() ) : ?>
    <p class="art-sub"><?php echo esc_html( get_the_excerpt() ); ?></p>
  <?php endif; ?>
  <div class="art-meta">
    <span><?php echo esc_html( $byline['name'] ); ?></span>
    <span>·</span>
    <span><?php echo esc_html( get_the_date() ); ?></span>
    <span>·</span>
    <span><?php echo esc_html( globalfxhub_reading_time( get_the_content() ) ); ?> min read</span>
  </div>
</article>

<?php
$theme_hero_image = get_post_meta( get_the_ID(), '_guide_hero_image', true );
if ( $theme_hero_image ) :
?>
<div class="article-wrap">
  <div class="art-hero" style="background-image:url('<?php echo esc_url( $theme_hero_image ); ?>');background-size:cover;background-position:center;"></div>
</div>
<?php elseif ( has_post_thumbnail() ) : ?>
<div class="article-wrap">
  <div class="art-hero" style="background-image:url('<?php the_post_thumbnail_url( 'large' ); ?>');background-size:cover;background-position:center;"></div>
</div>
<?php else : ?>
<div class="article-wrap">
  <div class="art-hero" style="background:linear-gradient(135deg, #0e1c30, #2f6f5e);">
    <span><?php echo esc_html( $cat_name ); ?></span>
  </div>
</div>
<?php endif; ?>

<div class="article-wrap art-body">
  <?php the_content(); ?>
</div>

<div class="article-wrap">
  <div class="author">
    <div class="author__avatar"></div>
    <div>
      <div class="author__name">Written by <?php echo esc_html( $byline['name'] ); ?></div>
      <div class="author__role"><?php echo esc_html( $byline['role'] ); ?> · Published <?php echo esc_html( get_the_date() ); ?></div>
      <?php if ( ! empty( $byline['bio'] ) ) : ?>
      <div class="author__bio"><?php echo esc_html( $byline['bio'] ); ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php
$related = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => 2,
    'post__not_in'   => array( get_the_ID() ),
    'orderby'        => 'rand',
    'ignore_sticky_posts' => true,
) );
if ( $related->have_posts() ) :
?>
<div class="article-wrap related">
  <h2>Keep reading</h2>
  <div class="related__grid">
    <?php while ( $related->have_posts() ) : $related->the_post();
        $rcats = get_the_category();
        $rcat  = ! empty( $rcats ) ? strtoupper( $rcats[0]->name ) : 'ARTICLE';
    ?>
    <a href="<?php the_permalink(); ?>" class="related-card">
      <div class="cat"><?php echo esc_html( $rcat ); ?></div>
      <h3><?php the_title(); ?></h3>
    </a>
    <?php endwhile; wp_reset_postdata(); ?>
  </div>
</div>
<?php endif; ?>

<?php endwhile; ?>

<?php get_footer(); ?>
