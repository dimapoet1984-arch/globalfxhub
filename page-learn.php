<?php
/**
 * Template Name: Learn Index
 * Description: Lists published posts in the "Learn" category, grouped
 * into topical clusters (see inc/learn-articles.php) rather than one
 * flat chronological list -- this is a deliberate topical-authority
 * build, not a pile of unrelated posts. Evergreen education only:
 * deliberately separate from /news/, which never appears here.
 */
get_header();

$learn_query = new WP_Query( array(
    'post_type'      => 'post',
    'posts_per_page' => -1,
    'category_name'  => 'learn',
    'orderby'        => 'date',
    'order'          => 'ASC',
) );

$clusters = globalfxhub_learn_clusters();
$grouped = array();
$ungrouped = array();

if ( $learn_query->have_posts() ) {
    foreach ( $learn_query->posts as $learn_post ) {
        $cluster_slug = get_post_meta( $learn_post->ID, '_learn_cluster', true );
        if ( $cluster_slug && isset( $clusters[ $cluster_slug ] ) ) {
            $grouped[ $cluster_slug ][] = $learn_post;
        } else {
            $ungrouped[] = $learn_post;
        }
    }
}
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<div class="wrap page-head">
  <div class="eyebrow">EDUCATION</div>
  <h1><?php the_title(); ?></h1>
  <p>Plain-English guides to how forex trading actually works -- built as topical clusters (fundamentals, risk, choosing a broker, regulation, fees, platforms, execution, analysis, trading concepts, and due diligence), not a pile of unrelated posts. No jargon, no sales pitch, no news mixed in.</p>
</div>

<div class="wrap" style="padding-bottom:20px;">
  <?php if ( empty( $grouped ) && empty( $ungrouped ) ) : ?>
  <p style="padding:24px;color:var(--ink-soft);">No guides published yet.</p>
  <?php endif; ?>

  <?php foreach ( $clusters as $cluster_slug => $cluster ) :
      if ( empty( $grouped[ $cluster_slug ] ) ) { continue; }
  ?>
  <div style="margin-bottom:48px;">
    <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 6px;"><?php echo esc_html( $cluster['title'] ); ?></h2>
    <p style="color:var(--ink-soft);font-size:14.5px;margin:0 0 18px;max-width:74ch;"><?php echo esc_html( $cluster['description'] ); ?></p>
    <div class="guides__grid">
      <?php foreach ( $grouped[ $cluster_slug ] as $cluster_post ) : ?>
      <a href="<?php echo esc_url( home_url( '/learn/' . $cluster_post->post_name . '/' ) ); ?>" class="guide">
        <div class="guide__time"><?php echo esc_html( globalfxhub_reading_time( $cluster_post->post_content ) ); ?> MIN READ</div>
        <h3><?php echo esc_html( $cluster_post->post_title ); ?></h3>
        <p><?php echo esc_html( globalfxhub_trim_excerpt( $cluster_post->post_excerpt ? $cluster_post->post_excerpt : $cluster_post->post_content, 20 ) ); ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <?php if ( ! empty( $ungrouped ) ) : ?>
  <div style="margin-bottom:48px;">
    <h2 style="font-family:var(--font-display);font-weight:500;font-size:22px;margin:0 0 6px;">General guides</h2>
    <div class="guides__grid">
      <?php foreach ( $ungrouped as $other_post ) : ?>
      <a href="<?php echo esc_url( home_url( '/learn/' . $other_post->post_name . '/' ) ); ?>" class="guide">
        <div class="guide__time"><?php echo esc_html( globalfxhub_reading_time( $other_post->post_content ) ); ?> MIN READ</div>
        <h3><?php echo esc_html( $other_post->post_title ); ?></h3>
        <p><?php echo esc_html( globalfxhub_trim_excerpt( $other_post->post_excerpt ? $other_post->post_excerpt : $other_post->post_content, 20 ) ); ?></p>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>
<?php wp_reset_postdata(); ?>

<?php get_footer(); ?>
