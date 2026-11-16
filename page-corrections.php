<?php
/**
 * Template Name: Corrections Log
 * Description: /corrections/ -- a public log of material corrections
 * (not trivial typo fixes). See inc/corrections-log.php for exactly
 * what qualifies and why this is a separate thing from the automated
 * /broker-changelog/ field-diff tracker.
 */
get_header();

$corrections = globalfxhub_get_corrections();
?>

<div class="wrap crumb">
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a> / <?php the_title(); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow">CORRECTIONS</div>
  <h1><?php the_title(); ?></h1>
  <p>A public record of material corrections to our published content -- not a log of typo fixes or rewording, but of cases where something we published was actually wrong and we're saying so, what it said before, what it says now, and why. See our <a href="<?php echo esc_url( home_url( '/editorial-policy/' ) ); ?>" style="color:var(--teal);">editorial policy</a> for how corrections fit into our broader research process, or the automated <a href="<?php echo esc_url( home_url( '/broker-changelog/' ) ); ?>" style="color:var(--teal);">broker change log</a> for when a tracked fact (pricing, platforms, licence numbers) simply changed in the real world.</p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( empty( $corrections ) ) : ?>
  <div class="methodology-note">
    No material corrections have been logged yet. This isn't a claim that nothing on this site has ever needed a fix -- see the <a href="<?php echo esc_url( home_url( '/broker-changelog/' ) ); ?>" style="color:var(--teal);">broker change log</a> for routine field updates -- it means no correction has risen to "something we published was actually wrong" since this log began. If you believe something here should be corrected, <a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>" style="color:var(--teal);">let us know</a>.
  </div>
  <?php else : ?>
  <div class="rankings">
    <div class="rank-row head" style="grid-template-columns:120px 1fr;"><span>Date</span><span>What was corrected</span></div>
    <?php foreach ( $corrections as $c ) : ?>
    <div class="rank-row" style="grid-template-columns:120px 1fr;">
      <span class="rank-detail"><?php echo esc_html( $c['date'] ); ?></span>
      <div>
        <p style="margin:0 0 6px;font-weight:600;"><a href="<?php echo esc_url( $c['page']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $c['page']['title'] ); ?></a></p>
        <p style="margin:0 0 4px;"><strong>Previously:</strong> <?php echo esc_html( $c['previous'] ); ?></p>
        <p style="margin:0 0 4px;"><strong>Corrected to:</strong> <?php echo esc_html( $c['corrected'] ); ?></p>
        <p style="margin:0 0 4px;color:var(--ink-soft);"><strong>Why:</strong> <?php echo esc_html( $c['reason'] ); ?></p>
        <?php if ( ! empty( $c['source'] ) ) : ?>
        <p style="margin:0;font-size:12.5px;"><a href="<?php echo esc_url( $c['source'] ); ?>" target="_blank" rel="noopener nofollow" style="color:var(--teal);">Source</a></p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
