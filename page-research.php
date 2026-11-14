<?php
/**
 * Template Name: Research
 * Description: /research/ index of original data reports, and
 * /research/{slug}/ for each individual report. See
 * inc/research-reports.php for the content -- every figure there is
 * computed directly from globalfxhub_get_brokers(), the same dataset
 * behind every review, best-list, and comparison on this site.
 */
get_header();

$report_slug = get_query_var( 'report' );
$report = $report_slug ? globalfxhub_get_localized_research_report( $report_slug ) : null;
if ( $report_slug && ! $report ) {
    status_header( 404 );
}
?>

<?php if ( $report_slug ) :
    if ( ! $report ) :
?>

<div class="wrap page-head">
  <div class="eyebrow">RESEARCH</div>
  <h1>Report not available</h1>
  <p>We don't have that report yet. <a href="<?php echo esc_url( home_url( '/research/' ) ); ?>" style="color:var(--teal);">See all research reports</a>.</p>
</div>

    <?php else : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> /
  <a href="<?php echo esc_url( home_url( '/research/' ) ); ?>">Research</a> /
  <?php echo esc_html( $report['title'] ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow">ORIGINAL RESEARCH</div>
  <h1><?php echo esc_html( $report['title'] ); ?></h1>
  <p><?php echo esc_html( $report['intro'] ); ?></p>
</div>

<div class="wrap art-body" style="padding-bottom:20px;">
  <?php foreach ( $report['sections'] as $section ) : ?>
  <h2><?php echo esc_html( $section['heading'] ); ?></h2>
  <p><?php echo esc_html( $section['body'] ); ?></p>
  <?php endforeach; ?>
</div>

<div class="wrap" style="padding-bottom:40px;">
  <h2 style="font-family:var(--font-display);font-weight:500;font-size:23px;margin:0 0 12px;">Frequently asked questions</h2>
  <div>
    <?php foreach ( $report['faqs'] as $faq ) : ?>
    <div style="margin-bottom:18px;">
      <p style="font-weight:600;margin:0 0 4px;color:var(--navy);"><?php echo esc_html( $faq['q'] ); ?></p>
      <p style="margin:0;color:var(--ink-soft);line-height:1.6;"><?php echo esc_html( $faq['a'] ); ?></p>
    </div>
    <?php endforeach; ?>
  </div>
  <p style="color:var(--ink-soft);font-size:13.5px;margin-top:4px;">See <a href="<?php echo esc_url( home_url( '/research/' ) ); ?>" style="color:var(--teal);">all research reports</a>, or the <a href="<?php echo esc_url( home_url( '/#method' ) ); ?>" style="color:var(--teal);">full scoring methodology</a> this data feeds into.</p>
</div>

    <?php endif; ?>

<?php else : ?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow">RESEARCH</div>
  <h1><?php the_title(); ?></h1>
  <p>Original data studies built directly from our own 139-broker research set -- platform adoption, regulation, fees, and more, computed from the same dataset behind every review and ranking on this site, not a separate survey or a third-party source.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <div class="guides__grid">
    <?php foreach ( globalfxhub_get_research_reports() as $slug => $report_data ) : ?>
    <a href="<?php echo esc_url( home_url( '/research/' . $slug . '/' ) ); ?>" class="guide">
      <h3><?php echo esc_html( $report_data['title'] ); ?></h3>
      <p><?php echo esc_html( $report_data['meta_description'] ); ?></p>
    </a>
    <?php endforeach; ?>
  </div>
</div>

<?php endif; ?>

<?php get_footer(); ?>
