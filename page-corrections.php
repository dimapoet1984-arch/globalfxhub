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
  <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php globalfxhub_te( 'tb_home' ); ?></a> / <?php globalfxhub_te( 'ptitle_corrections' ); ?>
</div>

<?php globalfxhub_promo_banner(); ?>

<div class="wrap page-head">
  <div class="eyebrow"><?php globalfxhub_te( 'corrections_eyebrow' ); ?></div>
  <h1><?php globalfxhub_te( 'ptitle_corrections' ); ?></h1>
  <p><?php
  $corrections_editorial_policy_link = '<a href="' . esc_url( home_url( '/editorial-policy/' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'footer_editorial_policy' ) ) . '</a>';
  $corrections_changelog_link = '<a href="' . esc_url( globalfxhub_templated_page_url( 'broker-changelog' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'corrections_link_text_changelog' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'corrections_intro' ) ), $corrections_editorial_policy_link, $corrections_changelog_link );
  ?></p>
</div>

<div class="wrap" style="padding-bottom:60px;">
  <?php if ( empty( $corrections ) ) : ?>
  <div class="methodology-note"><?php
  $corrections_changelog_link2 = '<a href="' . esc_url( globalfxhub_templated_page_url( 'broker-changelog' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'corrections_link_text_changelog' ) ) . '</a>';
  $corrections_contact_link = '<a href="' . esc_url( home_url( '/contact/' ) ) . '" style="color:var(--teal);">' . esc_html( globalfxhub_t( 'corrections_link_text_let_us_know' ) ) . '</a>';
  echo sprintf( esc_html( globalfxhub_t( 'corrections_empty' ) ), $corrections_changelog_link2, $corrections_contact_link );
  ?></div>
  <?php else : ?>
  <div class="rankings">
    <div class="rank-row head" style="grid-template-columns:120px 1fr;"><span><?php globalfxhub_te( 'corrections_th_date' ); ?></span><span><?php globalfxhub_te( 'corrections_th_what' ); ?></span></div>
    <?php foreach ( $corrections as $c ) : ?>
    <div class="rank-row" style="grid-template-columns:120px 1fr;">
      <span class="rank-detail"><?php echo esc_html( $c['date'] ); ?></span>
      <div>
        <p style="margin:0 0 6px;font-weight:600;"><a href="<?php echo esc_url( $c['page']['url'] ); ?>" style="color:var(--teal);"><?php echo esc_html( $c['page']['title'] ); ?></a></p>
        <p style="margin:0 0 4px;"><strong><?php globalfxhub_te( 'corrections_label_previously' ); ?></strong> <?php echo esc_html( $c['previous'] ); ?></p>
        <p style="margin:0 0 4px;"><strong><?php globalfxhub_te( 'corrections_label_corrected_to' ); ?></strong> <?php echo esc_html( $c['corrected'] ); ?></p>
        <p style="margin:0 0 4px;color:var(--ink-soft);"><strong><?php globalfxhub_te( 'corrections_label_why' ); ?></strong> <?php echo esc_html( $c['reason'] ); ?></p>
        <?php if ( ! empty( $c['source'] ) ) : ?>
        <p style="margin:0;font-size:12.5px;"><a href="<?php echo esc_url( $c['source'] ); ?>" target="_blank" rel="noopener nofollow" style="color:var(--teal);"><?php globalfxhub_te( 'corrections_source_link_text' ); ?></a></p>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>
</div>

<?php get_footer(); ?>
